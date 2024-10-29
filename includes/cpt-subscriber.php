<?php

namespace banana\newsletters;

add_action( 'init', 'banana\newsletters\cpt_subscriber', 0 );
add_action( 'add_meta_boxes', 'banana\newsletters\metaboxes_for_subscriber_cpt' );
add_action( 'save_post_subscriber', 'banana\newsletters\save_metaboxes_for_subcriber_cpt', 1, 2 );

function cpt_subscriber(): void {
	// Get required capability
	$current_capability_value = get_option( 'newsletters-required-capability' );
	$current_capability_value = ! empty( $current_capability_value ) ? $current_capability_value : 'update_core';
	// Set UI labels for this Custom Post Type
	$labels = array(
		'name'               => _x( 'Subscriber', 'Post Type General Name', 'banana-newsletters' ),
		'singular_name'      => _x( 'Subscriber', 'Post Type Singular Name', 'banana-newsletters' ),
		'menu_name'          => __( 'Subscribers', 'banana-newsletters' ),
		'all_items'          => __( 'All subscribers', 'banana-newsletters' ),
		'view_item'          => __( 'View subscriber', 'banana-newsletters' ),
		'add_new_item'       => __( 'Create subscriber', 'banana-newsletters' ),
		'add_new'            => __( 'Add New', 'banana-newsletters' ),
		'edit_item'          => __( 'Edit subscriber', 'banana-newsletters' ),
		'update_item'        => __( 'Update subscriber', 'banana-newsletters' ),
		'search_items'       => __( 'Search subscriber', 'banana-newsletters' ),
		'not_found'          => __( 'Not Found', 'banana-newsletters' ),
		'not_found_in_trash' => __( 'Not found in Trash', 'banana-newsletters' ),
	);
	// Set other options for this Custom Post Type
	$args = array(
		'label'               => __( 'Subscribers', 'banana-newsletters' ),
		'description'         => __( 'Subscribers', 'banana-newsletters' ),
		'labels'              => $labels,
		'supports'            => array( 'title' ),
		'hierarchical'        => false,
		'public'              => false,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'show_in_nav_menus'   => true,
		'show_in_admin_bar'   => true,
		'menu_position'       => 5,
		'can_export'          => true,
		'has_archive'         => true,
		'exclude_from_search' => false,
		'publicly_queryable'  => true,
		'capabilities' => array(
			'edit_post'          => $current_capability_value,
			'read_post'          => $current_capability_value,
			'delete_post'        => $current_capability_value,
			'edit_posts'         => $current_capability_value,
			'edit_others_posts'  => $current_capability_value,
			'publish_posts'      => $current_capability_value,
			'read_private_posts' => $current_capability_value,
			'create_posts'       => $current_capability_value,
		),
		'show_in_rest'        => true,
	);
	// Register this Custom Post Type
	register_post_type( 'subscriber', $args );
}

// META BOXES
function metaboxes_for_subscriber_cpt(): void {
	add_meta_box(
		'metabox_subscriber_data',
		__( 'Data', 'banana-newsletters' ),
		'banana\newsletters\metabox_subscriber_data',
		'subscriber',
		'normal',
	);
}

function metabox_subscriber_data(): void {
	global $post;
	// Nonce
	wp_nonce_field( basename( __FILE__ ), 'nonce_subscriber_data' );
	$user_email = get_post_meta( $post->ID, 'user_email', true );
	$secret_key = get_post_meta( $post->ID, 'secret_key', true );
	echo '
    <p><b>' . esc_html__( 'E-mail', 'banana-newsletters' ) . '</b>: <input type="email" name="user_email" value="' . esc_attr( $user_email ) . '"></p>
    <p><b>' . esc_html__( 'Secret key', 'banana-newsletters' ) . '</b>: <input type="text" name="secret_key" value="' . esc_attr( $secret_key ) . '"></p>
    ';
}

// Handle metabox save action
function save_metaboxes_for_subcriber_cpt( $post_id ): int {
	// Return if doing an autosave
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return $post_id;
	}
	// Return if not enough permissions
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return $post_id;
	}
	// Handle metabox: subscriber data
	if ( isset( $_POST['nonce_subscriber_data'] ) && wp_verify_nonce( $_POST['nonce_subscriber_data'], basename( __FILE__ ) ) ) {
		update_post_meta( $post_id, 'user_email', sanitize_email( $_POST['user_email'] ) );
		update_post_meta( $post_id, 'secret_key', sanitize_text_field( $_POST['secret_key'] ) );
	}

	return $post_id;
}
