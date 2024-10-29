<?php

namespace banana\newsletters;

register_deactivation_hook( __FILE__, '\banana\newsletters\deactivation' );
function deactivation(): void {
	wp_clear_scheduled_hook( 'send_pending_newsletters' );
}
