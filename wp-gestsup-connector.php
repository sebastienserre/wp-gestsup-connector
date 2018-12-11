<?php
/*
Plugin Name: Gestsup WP Connector
Plugin URI: http://www.thivinfo.com
Description: Connect your WordPress site to the helpdesk GestSup
Version: 1.4.1
Author: Sébastien Serre
Author URI: http://www.thivinfo.com
License: GPL2
Text Domain: wp-gestsup-connector
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly.

/* Include needed files*/
include_once plugin_dir_path( __FILE__ ) . '/inc/admin/gestsup_options.php';
include_once plugin_dir_path( __FILE__ ) . '/inc/shortcode/gestsup-add-ticket-shortcode.php';


/**
 * TODO: load conditionally on shortcode page
 * Include Google Repactcha
 *
 */

add_action( 'init', 'enable_recaptcha' );
function enable_recaptcha() {
	$recaptcha_enable = get_option( 'gestsup_recaptcha_enable' );

	if ( $recaptcha_enable == 'on' ) {
		add_action( 'wp_enqueue_scripts', 'gestsup_include_google_repatcha' );
	}
}

function gestsup_include_google_repatcha() {
	wp_enqueue_script( 'google-recaptcha', 'https://www.google.com/recaptcha/api.js' );
}
