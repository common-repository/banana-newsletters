<?php

namespace banana\newsletters;

add_action( 'init', 'banana\newsletters\cpt_newsletter', 0 );
add_action( 'add_meta_boxes', 'banana\newsletters\metaboxes_for_newsletters_cpt' );

function cpt_newsletter(): void {
	// Get required capability
	$current_capability_value = get_option( 'newsletters-required-capability' );
	$current_capability_value = ! empty( $current_capability_value ) ? $current_capability_value : 'update_core';
	// Set UI labels for this Custom Post Type
	$labels = array(
		'name'               => _x( 'Newsletter', 'Post Type General Name', 'banana-newsletters' ),
		'singular_name'      => _x( 'Newsletter', 'Post Type Singular Name', 'banana-newsletters' ),
		'menu_name'          => __( 'Newsletters', 'banana-newsletters' ),
		'all_items'          => __( 'All newsletters', 'banana-newsletters' ),
		'view_item'          => __( 'View newsletter', 'banana-newsletters' ),
		'add_new_item'       => __( 'Create newsletter', 'banana-newsletters' ),
		'add_new'            => __( 'Add New', 'banana-newsletters' ),
		'edit_item'          => __( 'Edit newsletter', 'banana-newsletters' ),
		'update_item'        => __( 'Update newsletter', 'banana-newsletters' ),
		'search_items'       => __( 'Search newsletter', 'banana-newsletters' ),
		'not_found'          => __( 'Not Found', 'banana-newsletters' ),
		'not_found_in_trash' => __( 'Not found in Trash', 'banana-newsletters' ),
	);
	// Set other options for this Custom Post Type
	$args = array(
		'label'               => __( 'Newsletters', 'banana-newsletters' ),
		'description'         => __( 'Newsletters', 'banana-newsletters' ),
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
		'show_in_rest'        => false,
	);
	// Registering this Custom Post Type
	register_post_type( 'newsletter', $args );
}

// META BOXES
function metaboxes_for_newsletters_cpt(): void {
	add_meta_box(
		'metabox_newsletter_content',
		__( 'Newsletter content', 'banana-newsletters' ),
		'banana\newsletters\metabox_newsletter_content',
		'newsletter',
		'normal',
	);
	add_meta_box(
		'metabox_pending_subscribers',
		__( 'Pending subscribers', 'banana-newsletters' ),
		'banana\newsletters\metabox_pending_subscribers',
		'newsletter',
		'normal',
	);
}

function metabox_newsletter_content(): void {
	global $post;
	echo wp_kses_post( apply_filters( 'the_content', $post->post_content ) );
}

function metabox_pending_subscribers(): void {
	global $post;
	$pending_subscribers = get_post_meta( $post->ID, 'pending-subscriber' );
	if ( 0 === count( $pending_subscribers ) ) {
		echo '<b>' . esc_html_e( 'This newsletter has been sent out to all subscribers', 'banana-newsletters' ) . '</b>';
	} else {
		echo '<b>Pending: </b>' . esc_html( count( $pending_subscribers ) ) . '<ul>';
		foreach ( $pending_subscribers as $pending_subscriber ) {
			$subscriber_email = get_post_meta( $pending_subscriber, 'user_email', true );
			echo '<li>' . esc_html( $subscriber_email ) . '</li>';
		}
		echo '</ul>';
	}
}
