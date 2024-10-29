<?php

namespace banana\newsletters;

add_filter( 'cron_schedules', 'banana\newsletters\add_cron_schedule' );
function add_cron_schedule( $schedules ) {
	$schedules['every_minute'] = array(
		'interval' => 60, // 60 seconds
		'display'  => __( 'Every Minute', 'banana-newsletters' ),
	);
	return $schedules;
}