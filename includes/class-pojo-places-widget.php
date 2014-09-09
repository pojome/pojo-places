<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Pojo_Places_Widget extends Pojo_Widget_Base {

	public function __construct() {
		$this->_form_fields = array();

		$this->_form_fields[] = array(
			'id' => 'title',
			'title' => __( 'Title:', 'pojo-places' ),
			'std' => '',
		);
		
		/*'category' => '',
		'tags' => '',
		'filter_address' => '',
		'filter_category' => '',
		'filter_tags' => '',*/

		$this->_form_fields[] = array(
			'id' => 'category',
			'title' => __( 'Category:', 'pojo-places' ),
			'type' => 'multi_taxonomy',
			'taxonomy' => 'pojo_places_cat',
			'std' => array(),
		);

		$this->_form_fields[] = array(
			'id' => 'tags',
			'title' => __( 'Tags:', 'pojo-places' ),
			'type' => 'multi_taxonomy',
			'taxonomy' => 'pojo_places_tag',
			'std' => array(),
		);

		$this->_form_fields[] = array(
			'id' => 'filter_address',
			'title' => __( 'Filter Address:', 'pojo-places' ),
			'type' => 'select',
			'std' => 'hide',
			'options' => array(
				'hide' => __( 'Hide', 'pojo-places' ),
				'show' => __( 'Show', 'pojo-places' ),
			),
			'filter' => array( &$this, '_valid_by_options' ),
		);

		$this->_form_fields[] = array(
			'id' => 'filter_category',
			'title' => __( 'Filter Category:', 'pojo-places' ),
			'type' => 'select',
			'std' => 'hide',
			'options' => array(
				'hide' => __( 'Hide', 'pojo-places' ),
				'checkbox' => __( 'Checkbox', 'pojo-places' ),
				'select' => __( 'Select', 'pojo-places' ),
			),
			'filter' => array( &$this, '_valid_by_options' ),
		);

		$this->_form_fields[] = array(
			'id' => 'filter_tags',
			'title' => __( 'Filter Tags:', 'pojo-places' ),
			'type' => 'select',
			'std' => 'hide',
			'options' => array(
				'hide' => __( 'Hide', 'pojo-places' ),
				'checkbox' => __( 'Checkbox', 'pojo-places' ),
				'select' => __( 'Select', 'pojo-places' ),
			),
			'filter' => array( &$this, '_valid_by_options' ),
		);

		$this->_form_fields[] = array(
			'id' => 'load_geolocation',
			'title' => __( 'Load GEO Location:', 'pojo-places' ),
			'type' => 'select',
			'std' => 'no',
			'options' => array(
				'no' => __( 'No', 'pojo-places' ),
				'yes' => __( 'Yes', 'pojo-places' ),
			),
			'filter' => array( &$this, '_valid_by_options' ),
		);
		
		parent::__construct(
			'pojo_places',
			__( 'Places', 'pojo-places' ),
			array( 'description' => __( 'Places', 'pojo-places' ), )
		);
	}

	public function widget( $args, $instance ) {
		$instance = wp_parse_args( $instance, $this->_get_default_values() );

		echo $args['before_widget'];

		if ( ! empty( $instance['title'] ) )
			echo $args['before_title'] . $instance['title'] . $args['after_title'];
		
		echo do_shortcode(
			sprintf(
				'[pojo-places category="%s" tags="%s" filter_address="%s" filter_category="%s" filter_tags="%s" load_geolocation="%s"]',
				implode( ',', (array) $instance['category'] ),
				implode( ',', (array) $instance['tags'] ),
				$instance['filter_address'],
				$instance['filter_category'],
				$instance['filter_tags'],
				$instance['load_geolocation']
			)
		);

		echo $args['after_widget'];
	}
	
}