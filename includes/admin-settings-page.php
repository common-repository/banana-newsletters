<?php

namespace banana\newsletters;

// Setup settings stuff
add_action( 'admin_init', 'banana\newsletters\initialize_settings' );

// Add the settings page to the admin menu
add_action( 'admin_menu', 'banana\newsletters\options_page' );

function initialize_settings(): void {
	// Register a new section in the "newsletters-settings" page
	add_settings_section(
		'newsletters_section_template',
		__( 'Template', 'banana-newsletters' ),
		'banana\newsletters\render_newsletters_section_template_heading',
		'newsletters-settings'
	);
	// Register options for the "newsletters-settings" group
	register_setting( 'newsletters-settings', 'newsletters-html-template' );
	register_setting( 'newsletters-settings', 'optin-html-template' );
	register_setting( 'newsletters-settings', 'newsletters-required-capability' );
	// Register a new field for this section: html template for newsletters
	add_settings_field(
		'newsletters-html-template',
		__( 'Newsletters Template', 'banana-newsletters' ),
		'banana\newsletters\render_newsletter_template_setting_textarea',
		'newsletters-settings',
		'newsletters_section_template',
		array(
			'label_for' => 'newsletters-html-template',
			'class'     => 'row',
		)
	);
	// Register a new field for this section: html template for opt-in message
	add_settings_field(
		'optin-html-template',
		__( 'Opt-in Template', 'banana-newsletters' ),
		'banana\newsletters\render_optin_template_setting_textarea',
		'newsletters-settings',
		'newsletters_section_template',
		array(
			'label_for' => 'optin-html-template',
			'class'     => 'row',
		)
	);
	// Register a new field for this section: capability to manage newsletters
	add_settings_field(
		'newsletters-required-capability',
		__( 'Required Capability', 'banana-newsletters' ),
		'banana\newsletters\render_capability_setting_dropdown',
		'newsletters-settings',
		'newsletters_section_template',
		array(
			'label_for' => 'newsletters-required-capability',
			'class'     => 'row',
		)
	);
}

function render_newsletters_section_template_heading(): void {
	?>
    <div><?php esc_html_e( 'Setup the look and feel for your newsletters:', 'banana-newsletters' ); ?></div>
	<?php
}

function render_newsletter_template_setting_textarea( array $args ): void {
	$current_textarea_value = get_option( 'newsletters-html-template' );
	$current_textarea_value = ! empty( $current_textarea_value ) ? $current_textarea_value : "{NEWSLETTER_TITLE}\n\n{NEWSLETTER_BODY}";
	?>
    <textarea
            id="<?php echo esc_attr( $args['label_for'] ); ?>"
            name="<?php echo esc_attr( $args['label_for'] ); ?>"><?php echo esc_textarea( $current_textarea_value ); ?></textarea>
    <p class="description">
		<?php esc_html_e( 'Use {NEWSLETTER_TITLE}, {NEWSLETTER_BODY} and {UNSUBSCRIBE_URL} where you want to replace the content with those values.', 'banana-newsletters' ); ?>
    </p>
	<?php
}

function render_optin_template_setting_textarea( array $args ): void {
	$current_textarea_value = get_option( 'optin-html-template' );
	$current_textarea_value = ! empty( $current_textarea_value ) ? $current_textarea_value : '{CONFIRMATION_URL}';
	?>
    <textarea
            id="<?php echo esc_attr( $args['label_for'] ); ?>"
            name="<?php echo esc_attr( $args['label_for'] ); ?>"><?php echo esc_textarea( $current_textarea_value ); ?></textarea>
    <p class="description">
		<?php esc_html_e( 'Use {CONFIRMATION_URL} where you want to replace the confirmation url.', 'banana-newsletters' ); ?>
    </p>
	<?php
}

function render_capability_setting_dropdown( array $args ): void {
	$current_capability_value = get_option( 'newsletters-required-capability' );
	$current_capability_value = ! empty( $current_capability_value ) ? $current_capability_value : 'update_core';
	?>
    <select
            id="<?php echo esc_attr( $args['label_for'] ); ?>"
            name="<?php echo esc_attr( $args['label_for'] ); ?>">
        <option value="manage_options" <?php selected( 'update_core', $current_capability_value ); ?>><?php esc_html_e( 'update_core (Admin)', 'banana-newsletters' ); ?></option>
        <option value="edit_others_posts" <?php selected( 'edit_others_posts', $current_capability_value ); ?>><?php esc_html_e( 'edit_others_posts (Editor)', 'banana-newsletters' ); ?></option>
        <option value="upload_files" <?php selected( 'upload_files', $current_capability_value ); ?>><?php esc_html_e( 'upload_files (Author)', 'banana-newsletters' ); ?></option>
    </select>
    <p class="description">
		<?php esc_html_e( 'Set the required capability to send newsletters.', 'banana-newsletters' ); ?>
    </p>
	<?php
}

function options_page(): void {
	add_options_page(
		__( 'Newsletters settings', 'banana-newsletters' ),
		__( 'Newsletters', 'banana-newsletters' ),
		'manage_options',
		'newsletters-settings',
		'banana\newsletters\options_page_html',
		1,
	);
}

function options_page_html(): void {
	?>
    <div class="wrap newsletters-settings">
        <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
        <form action="options.php" method="post">
			<?php
			// Output security fields for this settings group
			settings_fields( 'newsletters-settings' );
			// Output setting sections and their fields
			do_settings_sections( 'newsletters-settings' );
			// Output save settings button
			submit_button( __( 'Save Settings', 'banana-newsletters' ) );
			?>
        </form>
    </div>
	<?php
}
