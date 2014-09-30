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
			'public'             => false,
			'publicly_queryable' => false,
			'show_ui'            => true,
			'show_in_menu'       => true,
			'query_var'          => false,
			'rewrite'            => false,
			'capability_type'    => 'post',
			'has_archive'        => false,
			'hierarchical'       => false,
			'menu_position'      => 23,
			'supports'           => array( 'title', 'thumbnail' ),
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
			'query_var'         => false,
			'rewrite'           => false,
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
			'query_var'         => false,
			'rewrite'           => false,
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
			1  => __( 'Place updated.', 'pojo-places' ),
			2  => __( 'Custom field updated.', 'pojo-places' ),
			3  => __( 'Custom field deleted.', 'pojo-places' ),
			4  => __( 'Place updated.', 'pojo-places' ),
			/* translators: %s: date and time of the revision */
			5  => isset( $_GET['revision'] ) ? sprintf( __( 'Place restored to revision from %s', 'pojo-places' ), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
			6  => __( 'Place published.', 'pojo-places' ),
			7  => __( 'Place saved.', 'pojo-places' ),
			8  => __( 'Place submitted.', 'pojo-places' ),
			9  => sprintf( __( 'Place scheduled for: <strong>%1$s</strong>.', 'pojo-places' ),
				// translators: Publish box date format, see http://php.net/date
				date_i18n( __( 'M j, Y @ G:i', 'pojo-places' ), strtotime( $post->post_date ) ) ),
			10 => __( 'Place draft updated.', 'pojo-places' ),
		);
		return $messages;
	}

	public function register_address_metabox( $meta_boxes = array() ) {
		$fields = array();

		$fields[] = array(
			'id' => 'address',
			'title' => __( 'Address', 'pojo-places' ),
			'desc' => __( 'Street name and number your place', 'pojo-places' ),
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
			'id' => 'zipcode',
			'title' => __( 'Zip', 'pojo-places' ),
		);

		$fields[] = array(
			'id' => 'country',
			'title' => __( 'Country', 'pojo-places' ),
		);

		$fields[] = array(
			'id' => 'latitude',
			'title' => __( 'Latitude', 'pojo-places' ),
		);

		$fields[] = array(
			'id' => 'longitude',
			'title' => __( 'Longitude', 'pojo-places' ),
		);
		
		$meta_boxes[] = array(
			'id'         => 'pojo-places-address',
			'title'      => __( 'Place Details', 'pojo-places' ),
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
			'id' => 'email',
			'title' => __( 'Email', 'pojo-places' ),
		);

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
			'type' => 'textarea',
		);
		
		$fields[] = array(
			'id' => 'description',
			'title' => __( 'Description', 'pojo-places' ),
			'type' => 'textarea',
		);
		
		$meta_boxes[] = array(
			'id'         => 'pojo-places-details',
			'title'      => __( 'Extra Details', 'pojo-places' ),
			'post_types' => array( self::CPT_PLACE ),
			'context'    => 'normal',
			'priority'   => 'core',
			'prefix'     => 'pl_',
			'fields'     => $fields,
		);
		return $meta_boxes;
	}

	public function dashboard_glance_items( $elements ) {
		$post_type = self::CPT_PLACE;
		$num_posts = wp_count_posts( $post_type );
		if ( $num_posts && $num_posts->publish ) {
			$text = _n( '%s Place', '%s Places', $num_posts->publish, 'pojo-places' );
			$text = sprintf( $text, number_format_i18n( $num_posts->publish ) );
			printf( '<li class="%1$s-count"><a href="edit.php?post_type=%1$s">%2$s</a></li>', $post_type, $text );
		}
	}

	public function admin_head() {
		// Icons in dashboard
		?><style type="text/css">#adminmenu #menu-posts-pojo_places div.wp-menu-image:before, #dashboard_right_now .pojo_places-count a:before { content: "\f230"; }</style>
	<?php
	}

	public function __construct() {
		$this->register_post_type();

		add_filter( 'post_updated_messages', array( &$this, 'post_updated_messages' ) );

		add_action( 'dashboard_glance_items', array( &$this, 'dashboard_glance_items' ), 60 );
		add_action( 'admin_head', array( &$this, 'admin_head' ) );
		
		// Metaboxes
		add_filter( 'pojo_meta_boxes', array( &$this, 'register_address_metabox' ), 20 );
		add_filter( 'pojo_meta_boxes', array( &$this, 'register_details_metabox' ), 30 );
	}
	
}