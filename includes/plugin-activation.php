<?php

namespace banana\newsletters;

register_activation_hook( dirname( __DIR__ ) . '/newsletters.php', '\banana\newsletters\activation' );

function activation(): void {
	if ( ! wp_next_scheduled( 'send_pending_newsletters' ) ) {
		wp_schedule_event( time(), 'every_minute', 'send_pending_newsletters' );
	}
}
