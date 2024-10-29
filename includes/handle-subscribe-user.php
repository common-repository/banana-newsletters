<?php

namespace banana\newsletters;

add_action( 'init', 'banana\newsletters\handle_subscribe_user' );

function handle_subscribe_user(): void {
	if ( ! isset( $_GET['subscribe-to-newsletter'] ) || ! is_numeric( $_GET['subscribe-to-newsletter'] ) ) {
		return;
	}
	if ( ! isset( $_GET['key'] ) ) {
		return;
	}
	// Try to get secret key for this subscriber ID
	$secret_key = get_post_meta( $_GET['subscribe-to-newsletter'], 'secret_key', true );
	// Check if the md5 hash of this email matches the received hash
	if ( $secret_key === $_GET['key'] ) {
		// Ok. Subscribe
		$updated_data = array(
			'ID'          => $_GET['subscribe-to-newsletter'],
			'post_status' => 'publish',
		);
		wp_update_post( $updated_data );
		wp_die( 'Your e-mail has been added to the newsletters list. Thanks.', 'banana-newsletters' );
	}
}
