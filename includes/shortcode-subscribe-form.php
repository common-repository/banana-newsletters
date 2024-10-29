<?php

namespace banana\newsletters;

add_shortcode( 'display_subscribe_form', 'banana\newsletters\shortcode_subscribe_form' );

function shortcode_subscribe_form(): string {
	ob_start();
	$show_form = true;
	if (
		isset( $_POST['nonce_subscribe_form'] ) &&
		wp_verify_nonce( $_POST['nonce_subscribe_form'], basename( __FILE__ ) ) &&
		isset( $_POST['subscribe_email'] ) &&
		is_email( $_POST['subscribe_email'] )
	) {
		$sanitized_email = sanitize_email( $_POST['subscribe_email'] );
		// Check if this e-mail is already registered
		$subscriber_with_this_email = get_posts(
			array(
				'post_type'      => 'subscriber',
				'posts_per_page' => 1,
				'meta_key'       => 'user_email',
				'meta_value'     => $sanitized_email,
				'meta_compare'   => '=',
			)
		);
		// Check if we found a result or just create a new one
		if ( count( $subscriber_with_this_email ) == 1 ) {
			$subscriber_id = $subscriber_with_this_email[0]->ID;
			$secret_key    = get_post_meta( $subscriber_id, 'secret_key', true );
		} else {
			// Insert subscriber
			$insert_subscriber_args = array(
				'post_title'  => $sanitized_email,
				'post_status' => 'draft',
				'post_author' => 1,
				'post_type'   => 'subscriber',
			);
			$subscriber_id          = wp_insert_post( $insert_subscriber_args );
			if ( ! is_wp_error( $subscriber_id ) ) {
				$secret_key = md5( uniqid() );
				add_post_meta( $subscriber_id, 'secret_key', $secret_key, true );
				add_post_meta( $subscriber_id, 'user_email', $sanitized_email, true );
			}
		}
		// Send opt-in email if everything is fine
		if ( isset( $subscriber_id ) && isset( $secret_key ) ) {
			// Generate confirmation URL
			$confirmation_url = get_site_url() . '/?subscribe-to-newsletter=' . $subscriber_id . '&key=' . $secret_key;
			// Send email
			$headers             = array( 'Content-Type: text/html; charset=UTF-8' );
			$optin_html_template = get_option( 'optin-html-template' );
			$optin_html_template = ! empty( $optin_html_template ) ? $optin_html_template : '{CONFIRMATION_URL}';
			$optin_html_body     = str_replace( '{CONFIRMATION_URL}', $confirmation_url, $optin_html_template );
			$mail                = wp_mail(
				$sanitized_email,
				__( 'Confirm your e-mail to receive Newsletters', 'newsletters' ),
				$optin_html_body,
				$headers
			);
			if ( $mail ) {
				$show_form = false;
				echo '<div class="newsletter-optin__confirmation">' . esc_html__( 'Thanks for joining, please confirm this action by clicking the link you received in your e-mail.', 'banana-newsletters' ) . '</div>';
			}
		}
	}
	if ( $show_form ) {
		?>
        <form class="newsletter-optin__form" method="post">
			<?php
			wp_nonce_field( basename( __FILE__ ), 'nonce_subscribe_form' );
			?>
            <input type="email" required name="subscribe_email">
            <input type="submit" value="<?php esc_attr_e( 'Subscribe', 'banana-newsletters' ); ?>">
        </form>
		<?php
	}

	return ob_get_clean();
}
