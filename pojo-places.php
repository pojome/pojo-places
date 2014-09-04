<?php
/*
Plugin Name: Pojo Places
Plugin URI: http://pojo.me/
Description: ...
Author: Pojo Team
Author URI: http://pojo.me/
Version: 1.0.0
Text Domain: pojo-places
Domain Path: /languages/
License: GPLv2 or later


This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*/

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

define( 'POJO_PLACES__FILE__', __FILE__ );
define( 'POJO_PLACES_BASE', plugin_basename( POJO_PLACES__FILE__ ) );
define( 'POJO_PLACES_URL', plugins_url( '/', POJO_PLACES__FILE__ ) );
define( 'POJO_PLACES_ASSETS_URL', POJO_PLACES_URL . 'assets/' );

final class Pojo_Places {

	/**
	 * @var Pojo_Places The one true Pojo_Places
	 * @since 1.0.0
	 */
	private static $_instance = null;
	
	/** @var Pojo_Places_Shortcode */
	public $shortcode;
	
	/** @var Pojo_Places_CPT */
	public $cpt;
	
	/** @var Pojo_Places_Settings */
	public $settings;

	public function load_textdomain() {
		load_plugin_textdomain( 'pojo-places', false, basename( dirname( POJO_PLACES__FILE__ ) ) . '/languages' );
	}

	/**
	 * Throw error on object clone
	 *
	 * The whole idea of the singleton design pattern is that there is a single
	 * object therefore, we don't want the object to be cloned.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function __clone() {
		// Cloning instances of the class is forbidden
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'pojo-places' ), '1.0.0' );
	}

	/**
	 * Disable unserializing of the class
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function __wakeup() {
		// Unserializing instances of the class is forbidden
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'pojo-places' ), '1.0.0' );
	}

	/**
	 * @since 1.0.0
	 * @return Pojo_Places
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) )
			self::$_instance = new Pojo_Places();

		return self::$_instance;
	}

	public function admin_notices() {
		echo '<div class="error"><p>' . sprintf( __( '<a href="%s" target="_blank">Pojo Framework</a> is not active. Please activate any theme by Pojo before you are using "Pojo Places" plugin.', 'pojo-places' ), 'http://pojo.me/' ) . '</p></div>';
	}

	public function is_need_google_maps() {
		if ( is_admin() ) {
			global $pagenow;
			return in_array( $pagenow, array( 'edit.php', 'post.php', 'post-new.php' ) ) || 'pojo_places_page_pojo-places' === get_current_screen()->id;
		}
		
		return true;
	}

	public function print_google_maps() {
		if ( ! $this->is_need_google_maps() )
			return;
		
		$map_lang = pojo_get_option( 'places_map_language' );
		if ( empty( $map_lang ) )
			$map_lang = 'en';
		?>
		<script type="text/javascript" src="//maps.google.com/maps/api/js?sensor=true&v=3&libraries=geometry,places&language=<?php echo esc_attr( $map_lang ); ?>"></script>
	<?php
	}

	public function enqueue_scripts() {
		wp_register_script( 'pojo-places', POJO_PLACES_ASSETS_URL . 'js/app.min.js', array( 'jquery' ), false, true );
		
		wp_enqueue_script( 'pojo-places' );
	}

	public function admin_enqueue_scripts() {
		wp_enqueue_style( 'pojo-places', POJO_PLACES_ASSETS_URL . 'css/style.css' );
		wp_register_script( 'pojo-places', POJO_PLACES_ASSETS_URL . 'admin/js/app.min.js', array( 'jquery' ), false, true );
		
		if ( ! $this->is_need_google_maps() )
			return;
		
		wp_enqueue_script( 'pojo-places' );
	}

	public function add_localize_script_array( $array ) {
		$places = array();
		$places['address_line'] = (string) pojo_get_option( 'places_start_point_text' );
		$geo_location = explode( ';', pojo_get_option( 'places_start_point_geo' ) );
		if ( empty( $geo_location[0] ) || empty( $geo_location[1] ) ) {
			$geo_location = array( 32.066157, 34.777821 );
		}
		$places['lat'] = $geo_location[0];
		$places['lng'] = $geo_location[1];

		$array['places'] = $places;
		return $array;
	}

	public function include_settings() {
		include( 'includes/class-pojo-places-settings.php' );
		$this->settings = new Pojo_Places_Settings( 80 );
	}

	public function bootstrap() {
		// This plugin for Pojo Themes..
		if ( ! class_exists( 'Pojo_Core' ) ) {
			add_action( 'admin_notices', array( &$this, 'admin_notices' ) );
			return;
		}

		include( 'includes/class-pojo-places-cpt.php' );
		include( 'includes/class-pojo-places-shortcode.php' );
		
		$this->cpt       = new Pojo_Places_CPT();
		$this->shortcode = new Pojo_Places_Shortcode();

		add_action( 'wp_head', array( &$this, 'print_google_maps' ), 9 );
		add_action( 'admin_head', array( &$this, 'print_google_maps' ), 9 );
		add_action( 'wp_enqueue_scripts', array( &$this, 'enqueue_scripts' ), 100 );
		add_action( 'admin_enqueue_scripts', array( &$this, 'admin_enqueue_scripts' ), 100 );

		add_action( 'pojo_framework_base_settings_included', array( &$this, 'include_settings' ) );
	}

	private function __construct() {
		add_action( 'init', array( &$this, 'bootstrap' ) );
		add_action( 'plugins_loaded', array( &$this, 'load_textdomain' ) );
		
		add_action( 'pojo_admin_localize_scripts_array', array( &$this, 'add_localize_script_array' ) );
		add_action( 'pojo_localize_scripts_array', array( &$this, 'add_localize_script_array' ) );
	}

}

Pojo_Places::instance();

// EOF/ EOF/ EOF/ EOF