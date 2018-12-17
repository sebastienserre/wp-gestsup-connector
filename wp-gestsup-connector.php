<?php
/*
Plugin Name: Gestsup WP Connector
Plugin URI: http://www.thivinfo.com
Description: Connect your WordPress site to the helpdesk GestSup
Version: 1.4.2
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
include_once plugin_dir_path( __FILE__ ) . '/inc/admin/class-options.php';
include_once plugin_dir_path( __FILE__ ) . '/inc/blocks/class-basic-block.php';

/**
 * Define Constant
 */
define( 'WPGC_VERSION', '1.5.0' );
define( 'WPGC_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'WPGC_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
define( 'WPGC_PLUGIN_DIR', untrailingslashit( WPGC_PLUGIN_PATH ) );

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

add_action( 'admin_print_styles', 'wpgc_load_style', 11, 1 );
add_action( 'wp_enqueue_scripts', 'wpgc_load_style', 11, 1 );
function wpgc_load_style(){
	wp_enqueue_style( 'wpgc-style', WPGC_PLUGIN_URL . 'inc/blocks/wpgc-style.css' );
}
