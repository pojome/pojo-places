<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

final class Pojo_Places_Settings extends Pojo_Settings_Page_Base {

	public function section_settings( $sections = array() ) {
		$fields = array();

		$fields[] = array(
			'id' => 'places_map_language',
			'title' => __( 'Map Language', 'pojo-places' ),
			'type' => Pojo_Settings::FIELD_SELECT,
			'options' => array(
				'en' => __( 'English', 'pojo-places' ),
				'ar' => __( 'Arabic', 'pojo-places' ),
				'eu' => __( 'Basque', 'pojo-places' ),
				'bg' => __( 'Bulgarian', 'pojo-places' ),
				'bn' => __( 'Bengali', 'pojo-places' ),
				'ca' => __( 'Catalan', 'pojo-places' ),
				'cs' => __( 'Czech', 'pojo-places' ),
				'da' => __( 'Danish', 'pojo-places' ),
				'de' => __( 'German', 'pojo-places' ),
				'el' => __( 'Greek', 'pojo-places' ),
				'en-AU' => __( 'English (Australian)', 'pojo-places' ),
				'en-GB' => __( 'English (Great Britain)', 'pojo-places' ),
				'es' => __( 'Spanish', 'pojo-places' ),
				'fa' => __( 'Farsi', 'pojo-places' ),
				'fi' => __( 'Finnish', 'pojo-places' ),
				'fil' => __( 'Filipino', 'pojo-places' ),
				'fr' => __( 'French', 'pojo-places' ),
				'gl' => __( 'Galician', 'pojo-places' ),
				'gu' => __( 'Gujarati', 'pojo-places' ),
				'hi' => __( 'Hindi', 'pojo-places' ),
				'hr' => __( 'Croatian', 'pojo-places' ),
				'hu' => __( 'Hungarian', 'pojo-places' ),
				'id' => __( 'Indonesian', 'pojo-places' ),
				'it' => __( 'Italian', 'pojo-places' ),
				'iw' => __( 'Hebrew', 'pojo-places' ),
				'ja' => __( 'Japanese', 'pojo-places' ),
				'kn' => __( 'Kannada', 'pojo-places' ),
				'ko' => __( 'Korean', 'pojo-places' ),
				'lt' => __( 'Lithuanian', 'pojo-places' ),
				'lv' => __( 'Latvian', 'pojo-places' ),
				'ml' => __( 'Malayalam', 'pojo-places' ),
				'mr' => __( 'Marathi', 'pojo-places' ),
				'nl' => __( 'Dutch', 'pojo-places' ),
				'no' => __( 'Norwegian', 'pojo-places' ),
				'nn' => __( 'Norwegian Nynorsk', 'pojo-places' ),
				'pl' => __( 'Polish', 'pojo-places' ),
				'pt' => __( 'Portuguese', 'pojo-places' ),
				'pt-BR' => __( 'Portuguese (Brazil)', 'pojo-places' ),
				'pt-PT' => __( 'Portuguese (Portugal)', 'pojo-places' ),
				'ro' => __( 'Romanian', 'pojo-places' ),
				'ru' => __( 'Russian', 'pojo-places' ),
				'sk' => __( 'Slovak', 'pojo-places' ),
				'sl' => __( 'Slovenian', 'pojo-places' ),
				'sr' => __( 'Serbian', 'pojo-places' ),
				'sv' => __( 'Swedish', 'pojo-places' ),
				'tl' => __( 'Tagalog', 'pojo-places' ),
				'ta' => __( 'Tamil', 'pojo-places' ),
				'te' => __( 'Telugu', 'pojo-places' ),
				'th' => __( 'Thai', 'pojo-places' ),
				'tr' => __( 'Turkish', 'pojo-places' ),
				'uk' => __( 'Ukrainian', 'pojo-places' ),
				'vi' => __( 'Vietnamese', 'pojo-places' ),
				'zh-CN' => __( 'Chinese (Simplified)', 'pojo-places' ),
				'zh-TW' => __( 'Chinese (Traditional)', 'pojo-places' ),
			),
			'std' => 'en',
		);
		
		/*$fields[] = array(
			'id' => 'places_start_point_text',
			'title' => __( 'Start Point', 'pojo-places' ),
			'classes' => 'geo-autocomplete',
			'std' => '',
		);
		
		$fields[] = array(
			'id' => 'places_start_point_geo',
			'title' => __( 'Start Point (GEO)', 'pojo-places' ),
			'std' => '',
		);*/
		
		
		
		
		
		$sections[] = array(
			'id' => 'section-pojo-places',
			'page' => $this->_page_id,
			'title' => __( 'Settings:', 'pojo-places' ),
			'intro' => '',
			'fields' => $fields,
		);

		return $sections;
	}

	public function __construct( $priority = 10 ) {
		$this->_page_id = 'pojo-places';
		$this->_page_title = __( 'Pojo Places Settings', 'pojo-places' );
		$this->_page_menu_title = __( 'Settings', 'pojo-places' );
		$this->_page_type = 'submenu';
		$this->_page_parent = 'edit.php?post_type=pojo_places';

		add_filter( 'pojo_register_settings_sections', array( &$this, 'section_settings' ), 100 );

		parent::__construct( $priority );
	}
	
}