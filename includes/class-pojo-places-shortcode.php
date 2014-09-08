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
		
		ob_start();
		?>
		<div class="pojo-places">
			<?php if ( $filter_wrapper ) : ?>
			<div class="search-wrap" data-filter_category="checkbox">
				<?php if ( 'show' === $atts['filter_address'] ) : ?>
					<input class="search-box search-places" type="search" />
					<button class="get-geolocation-position" style="display: none;"><?php _e( 'Share Position !', 'pojo-places' ); ?></button>
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

					$category = wp_list_pluck( get_the_terms( get_the_ID(), 'pojo_places_cat' ), 'term_id' );
					$tags     = wp_list_pluck( get_the_terms( get_the_ID(), 'pojo_places_tag' ), 'term_id' );
					?>
				<li class="place-item" data-latitude="<?php echo esc_attr( $latitude ); ?>" data-longitude="<?php echo esc_attr( $longitude ); ?>" data-tags=";<?php echo esc_attr( implode( ';', $tags ) ); ?>;" data-category=";<?php echo esc_attr( implode( ';', $category ) ); ?>;">
					<h4 class="place-title"><?php the_title(); ?></h4>
					<?php /* the_content(); */ ?>
					<div class="place-thumbnail"></div>
					<div class="place-details">
						<div class="place-address"></div>
						<div class="place-city"></div>
						<div class="place-state"></div>
						<div class="place-zip"></div>
						<div class="place-country"></div>
					</div>
					<div class="extra-details">
						<div class="place-phone"></div>
						<div class="place-mobile"></div>
						<div class="place-fax"></div>
						<div class="place-opening-hours"></div>
						<div class="place-description"></div>
					</div>
					<div class="place-taxonomies">
						<div class="place-categories"></div>
						<div class="place-tags"></div>
					</div>
					<a target="_blank" href="https://www.google.com/maps/preview?q=<?php echo esc_attr( $latitude ); ?>,<?php echo esc_attr( $longitude ); ?>"><?php _e( 'Google Map', 'pojo-places' ); ?></a>
				</li>
				<?php endwhile; wp_reset_postdata(); ?>
			</ul>
		</div>
		<?php
		return ob_get_clean();
	}
	
}