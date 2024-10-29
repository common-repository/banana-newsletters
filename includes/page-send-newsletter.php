<?php

namespace banana\newsletters;

add_action( 'admin_menu', 'banana\newsletters\add_newsletter_page_to_admin_area' );

function add_newsletter_page_to_admin_area(): void {
	$current_capability_value = get_option( 'newsletters-required-capability' );
	$current_capability_value = ! empty( $current_capability_value ) ? $current_capability_value : 'update_core';
	add_menu_page(
		__( 'Send Newsletter', 'banana-newsletters' ),
		__( 'Send Newsletter', 'banana-newsletters' ),
		$current_capability_value,
		'send-newsletter',
		'banana\newsletters\render_newsletter_page',
		'dashicons-megaphone',
		3
	);
}

function render_newsletter_page(): void {

	// Flag to display form or not, default true
	$display_form = true;

	// Check if POST data exists
	if (
		isset( $_POST['nonce_submit_newsletter'] ) &&
		wp_verify_nonce( $_POST['nonce_submit_newsletter'], basename( __FILE__ ) ) &&
		isset( $_POST['newsletter-submit'] )
	) {

		// Get send mode (test or subscribers)
		$send_mode = isset( $_POST['newsletter-sending-mode'] ) ? sanitize_text_field( $_POST['newsletter-sending-mode'] ) : 'test';

		if ( $send_mode === 'subscribers' ) {
			// Handle newsletter submission
			$insert_newsletter_args = array(
				'post_title'   => wp_strip_all_tags( $_POST['newsletter-title'] ),
				'post_content' => wp_kses_post( $_POST['newsletter-content'] ),
				'post_status'  => 'publish',
				'post_author'  => 1,
				'post_type'    => 'newsletter',
			);
			$newsletter_id          = wp_insert_post( $insert_newsletter_args );
			if ( ! is_wp_error( $newsletter_id ) ) {
				echo '<h1>' . esc_html__( 'Great! Newsletter is on its way.', 'banana-newsletters' ) . '</h1>';
				// Get list of all subscribers
				$all_subscribers = get_posts(
					array(
						'post_type'      => 'subscriber',
						'post_status'    => 'publish',
						'posts_per_page' => - 1,
					)
				);
				// Add as pending emails to the newsletter
				echo '<p>' . esc_html__( 'Subscribers added to sending queue:', 'banana-newsletters' ) . '</p>
            <ul>';
				foreach ( $all_subscribers as $subscriber ) {
					$subscriber_email = get_post_meta( $subscriber->ID, 'user_email', true );
					echo '<li>Subscriber #' . esc_html( $subscriber->ID . ' (' . $subscriber_email . ')' ) . '</li>';
					add_post_meta( $newsletter_id, 'pending-subscriber', $subscriber->ID );
				}
				echo '</ul>';
			}
		} else {
			// Newsletters HTML template
			$newsletters_html_template = get_option( 'newsletters-html-template' );
			$newsletters_html_template = ! empty( $newsletters_html_template ) ? $newsletters_html_template : "{NEWSLETTER_TITLE}\n\n{NEWSLETTER_BODY}";
			$newsletters_html_body     = str_replace(
				array( '{NEWSLETTER_TITLE}', '{NEWSLETTER_BODY}' ),
				array(
					wp_strip_all_tags( $_POST['newsletter-title'] ),
					apply_filters( 'the_content', wp_kses_post( $_POST['newsletter-content'] ) ),
				),
				$newsletters_html_template
			);
			// Send email
			$headers     = array( 'Content-Type: text/html; charset=UTF-8' );
			$test_emails = $_POST['newsletter-test-emails'];
			$test_emails = explode( ',', $test_emails );
			echo '<h1>' . esc_html__( 'Send as test:', 'banana-newsletters' ) . '</h1>
            <ul>';
			foreach ( $test_emails as $test_email ) {
				$test_email = trim( $test_email );
				$mail       = wp_mail(
					$test_email,
					wp_strip_all_tags( $_POST['newsletter-title'] ),
					$newsletters_html_body,
					$headers
				);
				if ( $mail ) {
					echo '<li>' . esc_html__( 'Email sent to', 'banana-newsletters' ) . ' ' . esc_html( $test_email ) . '</li>';
				} else {
					echo '<li>' . esc_html__( 'Error trying to send to', 'banana-newsletters' ) . ' ' . esc_html( $test_email ) . '</li>';
				}
			}
			echo '</ul>';
		}
		$display_form = false;
	}

	if ( ! $display_form ) {
		return;
	}
	?>
    <div class="wrap send-newsletter">
        <h1 class="send-newsletter__heading"><?php esc_html_e( 'Send Newsletter', 'banana-newsletters' ); ?></h1>
        <form method="post">
			<?php
			wp_nonce_field( basename( __FILE__ ), 'nonce_submit_newsletter' );
			?>
            <input required type="text" name="newsletter-title"
                   class="send-newsletter__input-text"
                   placeholder="<?php esc_attr_e( 'Newsletter title', 'banana-newsletters' ); ?>">
			<?php wp_editor( '', 'newsletter-content' ); ?>
            <p>
                <input type="radio" name="newsletter-sending-mode"
                       value="subscribers" id="send-to-subscribers"> <label
                        for="send-to-subscribers"><?php esc_html_e( 'Send to all subscribers', 'banana-newsletters' ); ?></label><br>
                <input type="radio" name="newsletter-sending-mode" value="test"
                       checked id="send-to-test"> <label
                        for="send-to-test"><?php esc_html_e( 'Send to test emails', 'banana-newsletters' ); ?></label>
                <input type="text" name="newsletter-test-emails" class="send-newsletter__input-test-emails"
                       placeholder="Test emails (comma separated)">
            </p>
            <div class="send-newsletter__input-submit">
                <input type="submit" name="newsletter-submit"
                       class="button button-primary"
                       value="<?php esc_attr_e( 'Send', 'banana-newsletters' ); ?>">
            </div>
        </form>
    </div>
	<?php
}
