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
		
		if ( 'checkbox' === $type ) : ?>
			<ul class="places-filter-checkbox">
				<?php foreach ( $terms as $term ) : ?>
					<li><label><input type="checkbox" value="<?php echo esc_attr( $term->term_id ); ?>" class="places-input-filter" checked="checkbox" /> <?php echo esc_attr( $term->name ); ?></label></li>
				<?php endforeach; ?>
			</ul>
		<?php else : ?>
			<select class="places-select-filter">
				<option value=""><?php _e( 'All', '' ); ?></option>
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
		
		$places_query = new WP_Query(
			array(
				'post_type' => Pojo_Places_CPT::CPT_PLACE,
				'posts_per_page' => -1,
			)
		);
		
		if ( ! $places_query->have_posts() )
			return '';
		
		ob_start();
		?>
		<div class="pojo-places">
			<?php if ( $filter_wrapper ) : ?>
			<div class="search-wrap" data-filter_category="checkbox">
				<?php if ( 'show' === $atts['filter_address'] ) : ?>
					<input class="search-box" type="search" />
					<button class="get-geolocation-position" style="display: none;">Share Position !</button>
				<?php endif; ?>
				<?php $this->_print_filter( 'pojo_places_cat', $atts['filter_category'] ); ?>
				<?php $this->_print_filter( 'pojo_places_tag', $atts['filter_tags'] ); ?>
			</div>
			<?php endif; ?>
			
			<div class="loading" style="display: none;">Loading...</div>
			
			<ul class="places">
				<?php while ( $places_query->have_posts() ) :
					$places_query->the_post();

					$latitude  = (float) atmb_get_field( 'pl_latitude' );
					$longitude = (float) atmb_get_field( 'pl_longitude' );

					$category = wp_list_pluck( get_the_terms( get_the_ID(), 'pojo_places_cat' ), 'term_id' );
					$tags     = wp_list_pluck( get_the_terms( get_the_ID(), 'pojo_places_tag' ), 'term_id' );
					?>
				<li class="place-item" data-latitude="<?php echo esc_attr( $latitude ); ?>" data-longitude="<?php echo esc_attr( $longitude ); ?>" data-tags=";<?php echo esc_attr( implode( ';', $tags ) ); ?>;" data-category=";<?php echo esc_attr( implode( ';', $category ) ); ?>;">
					<h4 class="title"><?php the_title(); ?></h4>
					<?php the_content(); ?>
					
					<a target="_blank" href="https://www.google.com/maps/preview?q=<?php echo esc_attr( $latitude ); ?>,<?php echo esc_attr( $longitude ); ?>">Google Map</a>
					<span class="dist-debug">0</span>
				</li>
				<?php endwhile; wp_reset_postdata(); ?>
			</ul>
		</div>
		<?php
		return ob_get_clean();
	}
	
}