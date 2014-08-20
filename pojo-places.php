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

	public function load_textdomain() {
		load_plugin_textdomain( 'pojo-places', false, basename( dirname( __FILE__ ) ) . '/languages' );
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
		echo '<div class="error"><p>' . sprintf( __( '<a href="%s" target="_blank">Pojo Framework</a> is not active. Please activate any theme by Pojo before you are using "Pojo Places" plugin.', 'pojo-lightbox' ), 'http://pojo.me/' ) . '</p></div>';
	}

	public function print_google_maps() {
		?>
		<script type="text/javascript" src="https://maps.google.com/maps/api/js?sensor=true&v=3&libraries=geometry,places&language=iw"></script>
	<?php
	}

	public function enqueue_scripts() {
		wp_register_script( 'pojo-places', POJO_PLACES_ASSETS_URL . 'js/app.min.js', array( 'jquery', 'underscore', 'backbone' ), false, true );
		
		wp_enqueue_script( 'underscore' );
		wp_enqueue_script( 'backbone' );
		wp_enqueue_script( 'pojo-places' );
	}

	public function bootstrap() {
		// This plugin for Pojo Themes..
		if ( ! class_exists( 'Pojo_Core' ) ) {
			add_action( 'admin_notices', array( &$this, 'admin_notices' ) );
			return;
		}
		
		include( 'includes/class-pojo-places-shortcode.php' );
		$this->shortcode = new Pojo_Places_Shortcode();

		add_action( 'wp_head', array( &$this, 'print_google_maps' ), 9 );
		add_action( 'wp_enqueue_scripts', array( &$this, 'enqueue_scripts' ), 100 );
	}

	private function __construct() {
		add_action( 'init', array( &$this, 'bootstrap' ) );
		add_action( 'plugins_loaded', array( &$this, 'load_textdomain' ) );
	}

}

Pojo_Places::instance();

// EOF/ EOF