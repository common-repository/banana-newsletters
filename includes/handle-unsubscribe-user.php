<?php

namespace banana\newsletters;

add_action( 'init', 'banana\newsletters\handle_unsubscribe_user' );

function handle_unsubscribe_user(): void {
	if ( ! isset( $_GET['unsubscribe-from-newsletter'] ) || ! is_numeric( $_GET['unsubscribe-from-newsletter'] ) ) {
		return;
	}
	if ( ! isset( $_GET['key'] ) ) {
		return;
	}
	// Try to get secret key for this subscriber ID
	$secret_key = get_post_meta( $_GET['unsubscribe-from-newsletter'], 'secret_key', true );
	// Check if the md5 hash of this email matches the received hash
	if ( $secret_key === $_GET['key'] ) {
		// Ok. Unsubscribe
		wp_delete_post( $_GET['unsubscribe-from-newsletter'], true );
		wp_die( 'Your e-mail has been removed from the newsletters list.', 'banana-newsletters' );
	}
}
