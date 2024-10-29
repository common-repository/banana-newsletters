<?php

namespace banana\newsletters;

function admin_style(): void {
	wp_enqueue_style( 'admin-styles', plugin_dir_url( __DIR__ ) . 'assets/css/admin.css' );
}

add_action( 'admin_enqueue_scripts', 'banana\newsletters\admin_style' );
