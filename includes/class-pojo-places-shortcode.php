<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Pojo_Places_Shortcode {

	public function __construct() {
		add_shortcode( 'pojo-places', array( &$this, 'render' ) );
	}

	public function render( $atts ) {
		ob_start();
		?>
		<div id="pojo-places">
			<div class="search-wrap">
				<input class="search-box" type="search" />
				<button class="get-geolocation-position" style="display: none;">Your Position !</button>
			</div>
			
			<div class="loading" style="display: none;">Loading...</div>
			
			<ul class="places">
				<li class="place" data-latitude="32.026042" data-longitude="34.857795">
					<h4 class="title">אור יהודה - עודפים</h4>
					קניון אור יהודה
					<a target="_blank" href="https://www.google.com/maps/preview?q=32.026042,34.857795">Google Map</a>
					<span class="dist-debug">0</span>
				</li>
				<li class="place" data-latitude="31.314825509660995" data-longitude="34.62225150000006">
					<h4 class="title">אופקים</h4>
					רח יהדות דרום אפריקה מרכז ביג אופקים
					<a target="_blank" href="https://www.google.com/maps/preview?q=31.314825509660995,34.62225150000006">Google Map</a>
					<span class="dist-debug">0</span>
				</li>
				<li class="place" data-latitude="31.999802139714564" data-longitude="34.87930994999999">
					<h4 class="title">איירפורט סיטי</h4>
					מתחם האיירפורט סיטי
					<a target="_blank" href="https://www.google.com/maps/preview?q=31.999802139714564,34.87930994999999">Google Map</a>
					<span class="dist-debug">0</span>
				</li>
				<li class="place" data-latitude="29.55093600900329" data-longitude="34.954519000000005">
					<h4 class="title">אילת</h4>
					קניון מול הים
					<a target="_blank" href="https://www.google.com/maps/preview?q=29.55093600900329,34.954519000000005">Google Map</a>
					<span class="dist-debug">0</span>
				</li>
			</ul>
		</div>
		<?php
		return ob_get_clean();
	}
	
}