<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

final class Pojo_Places_CPT {
	
	const CPT_PLACE = 'pojo_places';

	public function register_post_type() {
		// CPT: pojo_places.
		$labels = array(
			'name'               => __( 'Places', 'pojo-places' ),
			'singular_name'      => __( 'Place', 'pojo-places' ),
			'add_new'            => __( 'Add New', 'pojo-places' ),
			'add_new_item'       => __( 'Add New Place', 'pojo-places' ),
			'edit_item'          => __( 'Edit Place', 'pojo-places' ),
			'new_item'           => __( 'New Place', 'pojo-places' ),
			'all_items'          => __( 'All Places', 'pojo-places' ),
			'view_item'          => __( 'View Place', 'pojo-places' ),
			'search_items'       => __( 'Search Place', 'pojo-places' ),
			'not_found'          => __( 'No Places found', 'pojo-places' ),
			'not_found_in_trash' => __( 'No Places found in Trash', 'pojo-places' ),
			'parent_item_colon'  => '',
			'menu_name'          => __( 'Places', 'pojo-places' ),
		);

		$args = array(
			'labels'             => $labels,
			'public'             => true,
			'publicly_queryable' => true,
			'show_ui'            => true,
			'show_in_menu'       => true,
			'query_var'          => true,
			'rewrite'            => array( 'slug' => 'places' ),
			'capability_type'    => 'post',
			'has_archive'        => true,
			'hierarchical'       => false,
			'menu_position'      => 23,
			'supports'           => array( 'title', 'editor', 'thumbnail' ),
		);
		
		register_post_type(
			self::CPT_PLACE,
			apply_filters( 'pojo_register_post_type_places', $args )
		);

		// Taxonomy: pojo_places_cat.
		$labels = array(
			'name'              => __( 'Place Categories', 'pojo-places' ),
			'singular_name'     => __( 'Place Category', 'pojo-places' ),
			'menu_name'         => _x( 'Categories', 'Admin menu name', 'pojo-places' ),
			'search_items'      => __( 'Search Categories', 'pojo-places' ),
			'all_items'         => __( 'All Categories', 'pojo-places' ),
			'parent_item'       => __( 'Parent Category', 'pojo-places' ),
			'parent_item_colon' => __( 'Parent Category:', 'pojo-places' ),
			'edit_item'         => __( 'Edit Category', 'pojo-places' ),
			'update_item'       => __( 'Update Category', 'pojo-places' ),
			'add_new_item'      => __( 'Add New Category', 'pojo-places' ),
			'new_item_name'     => __( 'New Category Name', 'pojo-places' ),
		);

		$args = array(
			'hierarchical'      => true,
			'labels'            => $labels,
			'show_ui'           => true,
			'show_admin_column' => true,
			'query_var'         => true,
			'rewrite'           => array( 'slug' => 'places-cat' ),
		);

		register_taxonomy(
			'pojo_places_cat',
			apply_filters( 'pojo_taxonomy_objects_places_cat', array( self::CPT_PLACE ) ),
			apply_filters( 'pojo_taxonomy_args_places_cat', $args )
		);

		// Taxonomy: pojo_places_tag.
		$labels = array(
			'name'              => __( 'Place Tags', 'pojo-places' ),
			'singular_name'     => __( 'Place Tag', 'pojo-places' ),
			'menu_name'         => _x( 'Tags', 'Admin menu name', 'pojo-places' ),
			'search_items'      => __( 'Search Tags', 'pojo-places' ),
			'all_items'         => __( 'All Tags', 'pojo-places' ),
			'parent_item'       => __( 'Parent Tag', 'pojo-places' ),
			'parent_item_colon' => __( 'Parent Tag:', 'pojo-places' ),
			'edit_item'         => __( 'Edit Tag', 'pojo-places' ),
			'update_item'       => __( 'Update Tag', 'pojo-places' ),
			'add_new_item'      => __( 'Add New Tag', 'pojo-places' ),
			'new_item_name'     => __( 'New Tag Name', 'pojo-places' ),
		);

		$args = array(
			'hierarchical'      => false,
			'labels'            => $labels,
			'show_ui'           => true,
			'show_admin_column' => true,
			'query_var'         => true,
			'rewrite'           => array( 'slug' => 'places-tag' ),
		);

		register_taxonomy(
			'pojo_places_tag',
			apply_filters( 'pojo_taxonomy_objects_places_tag', array( self::CPT_PLACE ) ),
			apply_filters( 'pojo_taxonomy_args_places_tag', $args )
		);
	}

	public function post_updated_messages( $messages ) {
		global $post;

		$messages[ self::CPT_PLACE ] = array(
			0  => '', // Unused. Messages start at index 1.
			1  => sprintf( __( 'Place updated. <a href="%s">View place</a>', 'pojo-places' ), esc_url( get_permalink( $post->ID ) ) ),
			2  => __( 'Custom field updated.', 'pojo-places' ),
			3  => __( 'Custom field deleted.', 'pojo-places' ),
			4  => __( 'Place updated.', 'pojo-places' ),
			/* translators: %s: date and time of the revision */
			5  => isset( $_GET['revision'] ) ? sprintf( __( 'Place restored to revision from %s', 'pojo-places' ), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
			6  => sprintf( __( 'Place published. <a href="%s">View post</a>', 'pojo-places' ), esc_url( get_permalink( $post->ID ) ) ),
			7  => __( 'Place saved.', 'pojo-places' ),
			8  => sprintf( __( 'Place submitted. <a target="_blank" href="%s">Preview post</a>', 'pojo-places' ), esc_url( add_query_arg( 'preview', 'true', get_permalink( $post->ID ) ) ) ),
			9  => sprintf( __( 'Place scheduled for: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Preview post</a>', 'pojo-places' ),
				// translators: Publish box date format, see http://php.net/date
				date_i18n( __( 'M j, Y @ G:i', 'pojo-places' ), strtotime( $post->post_date ) ), esc_url( get_permalink( $post->ID ) ) ),
			10 => sprintf( __( 'Place draft updated. <a target="_blank" href="%s">Preview post</a>', 'pojo-places' ), esc_url( add_query_arg( 'preview', 'true', get_permalink( $post->ID ) ) ) ),
		);
		return $messages;
	}

	public function register_address_metabox( $meta_boxes = array() ) {
		$fields = array();

		$fields[] = array(
			'id' => 'street',
			'title' => __( 'Street', 'pojo-places' ),
		);

		$fields[] = array(
			'id' => 'city',
			'title' => __( 'City', 'pojo-places' ),
		);

		$fields[] = array(
			'id' => 'state',
			'title' => __( 'State', 'pojo-places' ),
		);

		$fields[] = array(
			'id' => 'country',
			'title' => __( 'Country', 'pojo-places' ),
		);
		
		$meta_boxes[] = array(
			'id'         => 'pojo-places-address',
			'title'      => __( 'Address', 'pojo-places' ),
			'post_types' => array( self::CPT_PLACE ),
			'context'    => 'normal',
			'priority'   => 'core',
			'prefix'     => 'pl_',
			'fields'     => $fields,
		);
		return $meta_boxes;
	}

	public function register_details_metabox( $meta_boxes = array() ) {
		$fields = array();

		$fields[] = array(
			'id' => 'phone',
			'title' => __( 'Phone', 'pojo-places' ),
		);

		$fields[] = array(
			'id' => 'mobile',
			'title' => __( 'Mobile', 'pojo-places' ),
		);

		$fields[] = array(
			'id' => 'fax',
			'title' => __( 'Fax', 'pojo-places' ),
		);

		$fields[] = array(
			'id' => 'opening_hours',
			'title' => __( 'Opening Hours', 'pojo-places' ),
		);
		
		$fields[] = array(
			'id' => 'description',
			'title' => __( 'Description', 'pojo-places' ),
		);
		
		$meta_boxes[] = array(
			'id'         => 'pojo-places-details',
			'title'      => __( 'Details', 'pojo-places' ),
			'post_types' => array( self::CPT_PLACE ),
			'context'    => 'normal',
			'priority'   => 'core',
			'prefix'     => 'pl_',
			'fields'     => $fields,
		);
		return $meta_boxes;
	}

	public function __construct() {
		$this->register_post_type();

		add_filter( 'post_updated_messages', array( &$this, 'post_updated_messages' ) );
		
		// Metaboxes
		add_filter( 'pojo_meta_boxes', array( &$this, 'register_address_metabox' ), 20 );
		add_filter( 'pojo_meta_boxes', array( &$this, 'register_details_metabox' ), 30 );
	}
	
}