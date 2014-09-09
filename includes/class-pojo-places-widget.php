<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Pojo_Places_Widget extends Pojo_Widget_Base {
	
	protected $_metadata = array();

	public function __construct() {
		$this->_form_fields = array();

		$this->_form_fields[] = array(
			'id' => 'title',
			'title' => __( 'Title:', 'pojo-places' ),
			'std' => '',
		);
		
		/*$this->_form_fields[] = array(
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
		);*/

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
		
		// Metadata
		$this->_form_fields[] = array(
			'id' => 'custom_wrapper',
			'title' => __( 'Advanced Options', 'pojo-places' ),
			'type' => 'button_collapse',
			'mode' => 'start',
		);
		
		$this->_metadata = array(
			'thumbnail' => __( 'Thumbnail', 'pojo-places' ),
			'meta_address' => __( 'Address', 'pojo-places' ),
			'meta_city' => __( 'City', 'pojo-places' ),
			'meta_state' => __( 'State', 'pojo-places' ),
			'meta_zip' => __( 'Zip', 'pojo-places' ),
			'meta_phone' => __( 'Phone', 'pojo-places' ),
			'meta_mobile' => __( 'Mobile', 'pojo-places' ),
			'meta_fax' => __( 'Fax', 'pojo-places' ),
			'meta_opening_hours' => __( 'Opening_hours', 'pojo-places' ),
			'meta_description' => __( 'Description', 'pojo-places' ),
			'meta_category' => __( 'Category', 'pojo-places' ),
			'meta_tags' => __( 'Tags', 'pojo-places' ),
			'link_google' => __( 'Link Google', 'pojo-places' ),
			'link_waze' => __( 'Link Waze', 'pojo-places' ),
		);

		foreach ( $this->_metadata as $key => $title ) {
			$this->_form_fields[] = array(
				'id' => $key,
				'title' => $title,
				'type' => 'select',
				'std' => 'show',
				'options' => array(
					'show' => __( 'Show', 'pojo-places' ),
					'hide' => __( 'Hide', 'pojo-places' ),
				),
				'filter' => array( &$this, '_valid_by_options' ),
			);
		}

		$this->_form_fields[] = array(
			'id' => 'custom_wrapper',
			'title' => __( 'Custom', 'pojo-places' ),
			'type' => 'button_collapse',
			'mode' => 'end',
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
		
		$metadata_attrs = array();
		foreach ( $this->_metadata as $key => $title ) {
			$metadata_attrs[] = sprintf( '%s="%s"', $key, $instance[ $key ] );
		}
		
		echo do_shortcode(
			sprintf(
				'[pojo-places category="" tags="" filter_address="%s" filter_category="%s" filter_tags="%s" load_geolocation="%s" %s]',
				//implode( ',', (array) $instance['category'] ),
				//implode( ',', (array) $instance['tags'] ),
				$instance['filter_address'],
				$instance['filter_category'],
				$instance['filter_tags'],
				$instance['load_geolocation'],
				implode( ' ', $metadata_attrs )
			)
		);

		echo $args['after_widget'];
	}
	
}