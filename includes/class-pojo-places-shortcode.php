<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Pojo_Places_Shortcode {

	public function __construct() {
		add_shortcode( 'pojo-places', array( &$this, 'render' ) );
	}
	
	protected function _print_filter( $taxonomy, $type = 'checkbox' ) {
		if ( empty( $type ) || 'hide' === $type )
			return;
		
		$terms = get_terms( $taxonomy );
		
		if ( is_wp_error( $terms ) )
			return;
		
		if ( 'pojo_places_cat' === $taxonomy )
			$html_data_target = 'category';
		elseif ( 'pojo_places_tag' === $taxonomy )
			$html_data_target = 'tags';
		else
			return;
		
		if ( 'checkbox' === $type ) : ?>
			<ul class="places-filter-checkbox places-filter-<?php echo esc_attr( $html_data_target ); ?>">
				<?php foreach ( $terms as $term ) : ?>
					<li><label><input type="checkbox" name="<?php echo esc_attr( $html_data_target ); ?>[]" value="<?php echo esc_attr( $term->term_id ); ?>" class="places-input-filter" checked="checkbox" /> <?php echo esc_attr( $term->name ); ?></label></li>
				<?php endforeach; ?>
			</ul>
		<?php else : ?>
			<select name="<?php echo esc_attr( $html_data_target ); ?>" class="places-filter-select places-filter-<?php echo esc_attr( $html_data_target ); ?>">
				<option value=""><?php _e( 'Filter by', 'pojo-places' ); ?></option>
				<?php foreach ( $terms as $term ) : ?>
					<option value="<?php echo esc_attr( $term->term_id ); ?>"><?php echo $term->name; ?></option>
				<?php endforeach; ?>
			</select>
		<?php
		endif;
	}
	
	public function render( $atts ) {
		$atts = shortcode_atts(
			array(
				'category' => '',
				'tags' => '',
				'filter_address' => '',
				'filter_category' => '',
				'filter_tags' => '',
				'load_geolocation' => 'no',
				// Metadata
				'thumbnail' => 'show',
				'meta_title' => 'show',
				'meta_address' => 'show',
				'meta_city' => 'show',
				'meta_state' => 'show',
				'meta_zip' => 'show',
				'meta_country' => 'show',
				'meta_email' => 'show',
				'meta_phone' => 'show',
				'meta_mobile' => 'show',
				'meta_fax' => 'show',
				'meta_opening_hours' => 'show',
				'meta_description' => 'show',
				
				'meta_category' => 'show',
				'meta_tags' => 'show',
				// Maps
				'link_google' => 'show',
				'link_waze' => 'show',
			),
			$atts
		);
		
		$filter_wrapper = false;
		foreach ( array( 'filter_address', 'filter_category', 'filter_tags' ) as $key ) {
			if ( ! empty( $atts[ $key ] ) ) {
				$filter_wrapper = true;
				break;
			}
		}
		
		$query_args = array(
			'post_type' => Pojo_Places_CPT::CPT_PLACE,
			'posts_per_page' => -1,
		);
		
		if ( ! empty( $atts['category'] ) ) {
			$query_args['tax_query'] = array(
				array(
					'taxonomy' => 'pojo_places_cat',
					'field' => 'id',
					'terms' => explode( ',', $atts['category'] ),
					'include_children' => false,
				),
			);
		}
		
		if ( ! empty( $atts['tags'] ) ) {
			$query_args['tax_query'] = array(
				array(
					'taxonomy' => 'pojo_places_tag',
					'field' => 'id',
					'terms' => explode( ',', $atts['tags'] ),
					'include_children' => false,
				),
			);
		}
		
		$places_query = new WP_Query( $query_args );
		
		if ( ! $places_query->have_posts() )
			return '';

		$atts['load_geolocation'] = in_array( $atts['load_geolocation'], array( 'yes', 'no' ) ) ? $atts['load_geolocation'] : 'no';
				
		ob_start();
		?>
		<div class="pojo-places" data-load_geolocation="<?php echo $atts['load_geolocation']; ?>">
			<?php if ( $filter_wrapper ) : ?>
				<div class="search-wrap" data-filter_category="checkbox">
					<?php if ( 'show' === $atts['filter_address'] ) : ?>
						<input class="search-box search-places" type="search" />
						<button class="get-geolocation-position share-location" style="display: none;"></button>
					<?php endif; ?>
					<?php $this->_print_filter( 'pojo_places_cat', $atts['filter_category'] ); ?>
					<?php $this->_print_filter( 'pojo_places_tag', $atts['filter_tags'] ); ?>
				</div>
			<?php endif; ?>

			<div class="places-loading" style="display: none;"><i class="fa fa-spinner fa-spin"></i></div>

			<ul class="places-list" style="display: none;">
				<?php while ( $places_query->have_posts() ) :
					$places_query->the_post();

					$latitude  = (float) atmb_get_field( 'pl_latitude' );
					$longitude = (float) atmb_get_field( 'pl_longitude' );

					$address = atmb_get_field( 'pl_address' );
					$city    = atmb_get_field( 'pl_city' );
					$state   = atmb_get_field( 'pl_state' );
					$zipcode = atmb_get_field( 'pl_zipcode' );
					$country = atmb_get_field( 'pl_country' );

					$email  = sanitize_email( atmb_get_field( 'pl_email' ) );
					$phone  = atmb_get_field( 'pl_phone' );
					$mobile = atmb_get_field( 'pl_mobile' );
					$fax    = atmb_get_field( 'pl_fax' );
					
					$description   = atmb_get_field( 'pl_description' );
					$opening_hours = atmb_get_field( 'pl_opening_hours' );

					$category_string = 'hide' !== $atts['meta_category'] ? pojo_get_taxonomies_without_links( null, 'pojo_places_cat' ) : '';
					$tags_string     = 'hide' !== $atts['meta_tags'] ? pojo_get_taxonomies_without_links( null, 'pojo_places_tag' ) : '';

					$address_line = array_filter(
						array(
							$address,
							$city,
							//$state,
							//$zipcode,
							$country,
						)
					);
					
					$category = wp_list_pluck( (array) get_the_terms( get_the_ID(), 'pojo_places_cat' ), 'term_id' );
					$tags     = wp_list_pluck( (array) get_the_terms( get_the_ID(), 'pojo_places_tag' ), 'term_id' );
					?>
					<li class="place-item" data-latitude="<?php echo esc_attr( $latitude ); ?>" data-longitude="<?php echo esc_attr( $longitude ); ?>" data-tags=";<?php echo esc_attr( implode( ';', $tags ) ); ?>;" data-category=";<?php echo esc_attr( implode( ';', $category ) ); ?>;">
						<div class="place-item-body">
							<?php if ( 'hide' !== $atts['thumbnail'] && $image_url = Pojo_Thumbnails::get_post_thumbnail_url( array( 'width' => '100', 'height' => '100', 'crop' => true, 'placeholder' => true ) ) ) : ?>
								<div class="place-thumbnail">
									<img src="<?php echo esc_attr( $image_url ); ?>" alt="<?php echo esc_attr( get_the_title() ); ?>" />
								</div>
							<?php endif; ?>
							<div class="place-item-content">
								<?php if ( 'hide' !== $atts['meta_title'] ) : ?>
									<h3 class="place-title"><?php the_title(); ?></h3>
								<?php endif; ?>
								<?php if ( $description && 'hide' !== $atts['meta_description'] ) : ?>
									<div class="place-description"><?php echo wpautop( esc_html( $description ) ); ?></div>
								<?php endif; ?>
								<hr class="clearfix"/>
								<div class="place-details">
									<div class="address-details">
										<?php if ( $address && 'hide' !== $atts['meta_address'] ) : ?>
											<div class="place-address"><span class="place-label"><?php _e( 'Address', 'pojo-places' ); ?>:</span> <?php echo esc_html( $address ); ?></div>
										<?php endif; ?>
										<?php if ( $city && 'hide' !== $atts['meta_city'] ) : ?>
											<div class="place-city"><span class="place-label"><?php _e( 'City', 'pojo-places' ); ?>:</span> <?php echo esc_html( $city ); ?></div>
										<?php endif; ?>
										<?php if ( $state && 'hide' !== $atts['meta_state'] ) : ?>
											<div class="place-state"><span class="place-label"><?php _e( 'State', 'pojo-places' ); ?>:</span> <?php echo esc_html( $state ); ?></div>
										<?php endif; ?>
										<?php if ( $zipcode && 'hide' !== $atts['meta_zip'] ) : ?>
											<div class="place-zip"><span class="place-label"><?php _e( 'Zip', 'pojo-places' ); ?>:</span> <?php echo esc_html( $zipcode ); ?></div>
										<?php endif; ?>
										<?php if ( $country && 'hide' !== $atts['meta_country'] ) : ?>
											<div class="place-country"><span class="place-label"><?php _e( 'Country', 'pojo-places' ); ?>:</span> <?php echo esc_html( $country ); ?></div>
										<?php endif; ?>
										<hr class="clearfix"/>
										<?php if ( $email && 'hide' !== $atts['meta_email'] ) : ?>
											<div class="place-email"><span class="place-label"><?php _e( 'Email', 'pojo-places' ); ?>:</span>
												<a href="mailto:<?php echo esc_attr( $email ); ?>"><?php echo esc_html( $email ); ?></a></div>
										<?php endif; ?>
										<?php if ( $phone && 'hide' !== $atts['meta_phone'] ) : ?>
											<div class="place-phone"><span class="place-label"><?php _e( 'Phone', 'pojo-places' ); ?>:</span> <a href="tel:<?php echo esc_attr( $phone ); ?>"><?php echo esc_html( $phone ); ?></a></div>
										<?php endif; ?>
										<?php if ( $mobile && 'hide' !== $atts['meta_mobile'] ) : ?>
											<div class="place-mobile"><span class="place-label"><?php _e( 'Mobile', 'pojo-places' ); ?>:</span> <a href="tel:<?php echo esc_attr( $mobile ); ?>"><?php echo esc_html( $mobile ); ?></a></div>
										<?php endif; ?>
										<?php if ( $fax && 'hide' !== $atts['meta_fax'] ) : ?>
											<div class="place-fax"><span class="place-label"><?php _e( 'Fax', 'pojo-places' ); ?>:</span> <?php echo esc_html( $fax ); ?></div>
										<?php endif; ?>
										<hr class="clearfix"/>
										<?php if ( ! empty( $category_string ) || ! empty( $tags_string ) ) : ?>
											<div class="place-taxonomies">
												<?php if ( ! empty( $category_string ) ) : ?>
													<div class="place-categories"><span class="place-label"><?php _e( 'Categories', 'pojo-places' ); ?>:</span> <?php echo $category_string; ?></div>
												<?php endif; ?>
												<?php if ( ! empty( $category_string ) ) : ?>
													<div class="place-tags"><span class="place-label"><?php _e( 'Tags', 'pojo-places' ); ?>:</span> <?php echo $tags_string; ?></div>
												<?php endif; ?>
											</div>
										<?php endif; ?>
									</div>
									<div class="extra-details">
										<?php if ( $opening_hours && 'hide' !== $atts['meta_opening_hours'] ) : ?>
											<div class="place-opening-hours"><span class="place-label"><?php _e( 'Opening Hours', 'pojo-places' ); ?>:</span> <?php echo wpautop( esc_html( $opening_hours ) ); ?></div>
										<?php endif; ?>
									</div>
									<div class="place-go-out">
										<?php if ( $phone && 'hide' !== $atts['meta_phone'] ) : ?>
											<div class="place-phone">
												<a href="tel:<?php echo esc_attr( $phone ); ?>">
													<i class="fa fa-phone"></i>
													<?php _e( 'Call', 'pojo-places' ); ?>
												</a>
											</div>
										<?php endif; ?>
										<?php if ( 'hide' !== $atts['link_waze'] ) : ?>
											<div class="goto-waze">
												<a target="_blank" href="waze://?ll=<?php echo esc_attr( $latitude . ',' . $longitude ); ?>&navigate=yes">
													<i class="fa fa-car"></i>
													<?php _e( 'Waze', 'pojo-places' ); ?>
												</a>
											</div>
										<?php endif; ?>
										<?php if ( 'hide' !== $atts['link_google'] ) : ?>
											<div class="goto-google-map">
												<a target="_blank" href="https://www.google.com/maps/preview?q=<?php echo urlencode( implode( ', ', $address_line ) ); ?>">
													<i class="fa fa-map-marker"></i>
													<?php _e( 'Map', 'pojo-places' ); ?>
												</a>
											</div>
										<?php endif; ?>
										<?php if ( $email && 'hide' !== $atts['meta_email'] ) : ?>
											<div class="place-email">
												<a href="mailto:<?php echo esc_attr( $email ); ?>">
													<i class="fa fa-envelope"></i>
													<?php _e( 'Email', 'pojo-places' ); ?>
												</a>
											</div>
										<?php endif; ?>
									</div>
								</div>
							</div>
						</div>
					</li>
				<?php endwhile;
				wp_reset_postdata(); ?>
			</ul>
		</div>
		<?php
		return ob_get_clean();
	}
	
}