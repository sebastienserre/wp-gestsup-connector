<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly.



		add_action( 'wp_dashboard_setup', 'dashboard_widget' );


	function dashboard_widget() {
		wp_add_dashboard_widget(
			'gestsup_dashboard_widget',
			__('My GestSup Ticket', 'wp-gestsup-connector'),
			'dashboard_render'
			);
	}

	function dashboard_render(){
		echo 'Hello World';
	}
