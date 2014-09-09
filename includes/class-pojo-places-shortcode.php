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
		
		$html_data_target = '';
		if ( 'pojo_places_cat' === $taxonomy )
			$html_data_target = 'category';
		elseif ( 'pojo_places_tag' === $taxonomy )
			$html_data_target = 'tags';
		else
			return;
		
		if ( 'checkbox' === $type ) : ?>
			<ul class="places-filter-checkbox places-filter-<?php echo esc_attr( $html_data_target ); ?>">
				<?php foreach ( $terms as $term ) : ?>
					<li><label><input type="checkbox" value="<?php echo esc_attr( $term->term_id ); ?>" class="places-input-filter" checked="checkbox" /> <?php echo esc_attr( $term->name ); ?></label></li>
				<?php endforeach; ?>
			</ul>
		<?php else : ?>
			<select class="places-filter-select places-filter-<?php echo esc_attr( $html_data_target ); ?>">
				<option value=""><?php _e( 'All', 'pojo-places' ); ?></option>
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

			<div class="loading" style="display: none;"><?php _e( 'Loading...', 'pojo-places' ); ?></div>

			<ul class="places-list">
				<?php while ( $places_query->have_posts() ) :
					$places_query->the_post();

					$latitude  = (float) atmb_get_field( 'pl_latitude' );
					$longitude = (float) atmb_get_field( 'pl_longitude' );

					$address = atmb_get_field( 'pl_address' );
					$city    = atmb_get_field( 'pl_city' );
					$state   = atmb_get_field( 'pl_state' );
					$zipcode = atmb_get_field( 'pl_zipcode' );
					$country = atmb_get_field( 'pl_country' );

					$category_string = 'hide' !== $atts['meta_category'] ? pojo_get_taxonomies_without_links( null, 'pojo_places_cat' ) : '';
					$tags_string     = 'hide' !== $atts['meta_tags'] ? pojo_get_taxonomies_without_links( null, 'pojo_places_tag' ) : '';

					$address_line = array_filter(
						array(
							$address,
							$city,
							$state,
							$zipcode,
						)
					);

					$category = wp_list_pluck( get_the_terms( get_the_ID(), 'pojo_places_cat' ), 'term_id' );
					$tags     = wp_list_pluck( get_the_terms( get_the_ID(), 'pojo_places_tag' ), 'term_id' );
					?>
					<li class="place-item" data-latitude="<?php echo esc_attr( $latitude ); ?>" data-longitude="<?php echo esc_attr( $longitude ); ?>" data-tags=";<?php echo esc_attr( implode( ';', $tags ) ); ?>;" data-category=";<?php echo esc_attr( implode( ';', $category ) ); ?>;">
						<?php if ( 'hide' !== $atts['meta_title'] ) : ?>
						<h4 class="place-title"><?php the_title(); ?></h4>
						<?php endif; ?>
						<?php if ( 'hide' !== $atts['thumbnail'] && $image_url = Pojo_Thumbnails::get_post_thumbnail_url( array( 'width' => '150', 'height' => '150', 'crop' => true, 'placeholder' => true ) ) ) : ?>
							<div class="place-thumbnail">
								<img src="<?php echo esc_attr( $image_url ); ?>" alt="<?php echo esc_attr( get_the_title() ); ?>" />
							</div>
						<?php endif; ?>
						<div class="place-details">
							<?php if ( $address && 'hide' !== $atts['meta_address'] ) : ?>
								<div class="place-address"><?php echo esc_html( $address ); ?></div>
							<?php endif; ?>
							<?php if ( $city && 'hide' !== $atts['meta_city'] ) : ?>
								<div class="place-city"><?php echo esc_html( $city ); ?></div>
							<?php endif; ?>
							<?php if ( $state && 'hide' !== $atts['meta_state'] ) : ?>
								<div class="place-state"><?php echo esc_html( $state ); ?></div>
							<?php endif; ?>
							<?php if ( $zipcode && 'hide' !== $atts['meta_zip'] ) : ?>
								<div class="place-zip"><?php echo esc_html( $zipcode ); ?></div>
							<?php endif; ?>
							<?php if ( $country && 'hide' !== $atts['meta_country'] ) : ?>
								<div class="place-country"><?php echo esc_html( $country ); ?></div>
							<?php endif; ?>
						</div>
						<div class="extra-details">
							<?php if ( $meta = atmb_get_field( 'pl_phone' ) && 'hide' !== $atts['meta_phone'] ) : ?>
								<div class="place-phone"><?php echo esc_html( $meta ); ?></div>
							<?php endif; ?>
							<?php if ( $meta = atmb_get_field( 'pl_mobile' ) && 'hide' !== $atts['meta_mobile'] ) : ?>
								<div class="place-mobile"><?php echo esc_html( $meta ); ?></div>
							<?php endif; ?>
							<?php if ( $meta = atmb_get_field( 'pl_fax' ) && 'hide' !== $atts['meta_fax'] ) : ?>
								<div class="place-fax"><?php echo esc_html( $meta ); ?></div>
							<?php endif; ?>
							<?php if ( $meta = atmb_get_field( 'pl_opening_hours' ) && 'hide' !== $atts['meta_opening_hours'] ) : ?>
								<div class="place-opening-hours"><?php echo wpautop( esc_html( $meta ) ); ?></div>
							<?php endif; ?>
							<?php if ( $meta = atmb_get_field( 'pl_description' ) && 'hide' !== $atts['meta_description'] ) : ?>
								<div class="place-description"><?php echo wpautop( esc_html( $meta ) ); ?></div>
							<?php endif; ?>
						</div>
						<?php if ( ! empty( $category_string ) || ! empty( $tags_string ) ) : ?>
							<div class="place-taxonomies">
								<?php if ( ! empty( $category_string ) ) : ?>
									<div class="place-categories"><?php echo $category_string; ?></div>
								<?php endif; ?>
								<?php if ( ! empty( $category_string ) ) : ?>
									<div class="place-tags"><?php echo $tags_string; ?></div>
								<?php endif; ?>
							</div>
						<?php endif; ?>
						<?php if ( 'hide' !== $atts['link_google'] ) : ?>
							<a target="_blank" href="https://www.google.com/maps/preview?q=<?php echo urlencode( implode( ',', $address_line ) ); ?>"><?php _e( 'Google Map', 'pojo-places' ); ?></a>
						<?php endif; ?>
						<?php if ( 'hide' !== $atts['link_waze'] ) : ?>
							<a target="_blank" href="waze://?q=<?php echo urlencode( implode( ',', $address_line ) ); ?>"><?php _e( 'Waze Map', 'pojo-places' ); ?></a>
						<?php endif; ?>
					</li>
				<?php endwhile;
				wp_reset_postdata(); ?>
			</ul>
		</div>
		<?php
		return ob_get_clean();
	}
	
}