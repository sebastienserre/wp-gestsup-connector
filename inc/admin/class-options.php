<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly.


use function add_action;
use Carbon_Fields\Container;
use Carbon_Fields\Field;
use Carbon_Fields\Helper\Helper;
use function delete_option;
use GestsupAPI;
use function get_option;

/**
 * Class Options
 *
 * @package WPGestSup\Admin\Options
 */
class Options {

	public function __construct() {
		add_action( 'carbon_fields_register_fields', array( $this, 'wpgc_settings' ) );
		add_action( 'admin_init', array( $this, 'wpgc_update_option' ) );
	}

	public function wpgc_settings() {
		$tech = \WPGC\GestSupAPI\GestsupAPI::wpgc_get_tech();
		Container::make( 'theme_options', __( 'Gestsup Connector' ) )
		         ->set_page_parent( 'options-general.php' )
		         ->add_tab( __( 'General', 'wp-gestsup-connector' ),
			         array(
				         Field::make( 'checkbox', 'wpgc_recaptcha', __( 'Activate Google Recaptcha', 'wp-gestsup-connector' ) )
				              ->set_option_value( 'yes' ),
				         Field::make( 'text', 'wpgc_recaptcha_sitekey', __( 'Insert your Google Recaptcha Site Key', 'wp-gestsup-connector' ) )
				              ->set_conditional_logic(
					              array(
						              array(
							              'field' => 'wpgc_recaptcha',
							              'value' => 'yes',
						              ),
					              )
				              ),

				         Field::make( 'text', 'wpgc_recaptcha_secretkey', __( 'Insert your Google Recaptcha Secret Key', 'wp-gestsup-connector' ) )
				              ->set_conditional_logic(
					              array(
						              array(
							              'field' => 'wpgc_recaptcha',
							              'value' => 'yes',
						              ),
					              )
				              ),
				         Field::make( 'select', 'wpgc_tech', __( 'By default technician', 'wp-gestsup-connector' ) )
				              ->set_options( $tech ),

			         )

		         )
		         ->add_tab( __( 'Database', 'wp-gestsup-connector' ),
			         array(
				         Field::make( 'text', 'wpgc_gestsup_host', __( 'Host', 'wp-gestsup-connector' ) ),
				         Field::make( 'text', 'wpgc_gestsup_db', __( 'Database', 'wp-gestsup-connector' ) ),
				         Field::make( 'text', 'wpgc_gestsup_username', __( 'Username', 'wp-gestsup-connector' ) ),
				         Field::make( 'text', 'wpgc_gestsup_passwd', __( 'Password', 'wp-gestsup-connector' ) ),
			         )
		         )
		         ->add_tab( __( 'Help', 'wp-gestsup-connector' ),
			         array(
				         Field::make( 'html', 'wpgc_gestsup_help', __( 'Help', 'wp-gestsup-connector' ) )
				              ->set_html(
					              '<h3>' . __( 'I Need Help!', 'wp-gestsup-connector' ) . '</h3>
<p>' . sprintf( wp_kses( __( 'Come & visit <a href="%s">Thivinfo\'s support & doc pages </a > ', 'wp-gestsup-connector'), array(  'a' => array( 'href' => array() ) ) ), esc_url( 'https://www.thivinfo.com/docs/' )) .'</p>
<h3>' . __( 'If You Enjoy this plugin... encourage me!', 'wp-gestsup-connector' ) . '</h3>
<form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top">
	<input type="hidden" name="cmd" value="_s-xclick">
	<input type="hidden" name="encrypted" value="-----BEGIN PKCS7-----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-----END PKCS7-----
">
	<input type="image" src="https://www.paypalobjects.com/fr_FR/FR/i/btn/btn_donateCC_LG.gif" border="0"
	       name="submit" alt="PayPal - la solution de paiement en ligne la plus simple et la plus sécurisée !">
	<img alt="" border="0" src="https://www.paypalobjects.com/fr_FR/i/scr/pixel.gif" width="1" height="1">
</form>'

				              ),
			         )

		         );
	}

	public function wpgc_update_option() {
		// Get Old Option
		$old_secret    = get_option( 'gestsup_recaptcha_secret_key' );
		$old_site      = get_option( 'gestsup_recaptcha_site_key' );
		$old_recaptcha = get_option( 'gestsup_recaptcha_enable' );

		$old_host   = get_option( 'gestsup_host' );
		$old_db     = get_option( 'gestsup_dbname' );
		$old_user   = get_option( 'gestsup_user' );
		$old_passwd = get_option( 'gestsup_pass' );

		// Update from old to CF

		$new_host = Helper::get_theme_option( 'wpgc_gestsup_host' );
		if ( empty( $new_host ) ) {
			Helper::set_theme_option( 'wpgc_gestsup_host', array( $old_host ) );
		}

		$new_db = Helper::get_theme_option( 'wpgc_gestsup_db' );
		if ( empty( $new_db ) ) {
			Helper::set_theme_option( 'wpgc_gestsup_db', array( $old_db ) );
		}

		$new_user = Helper::get_theme_option( 'wpgc_gestsup_username' );
		if ( empty( $new_user ) ) {
			Helper::set_theme_option( 'wpgc_gestsup_username', array( $old_user ) );
		}

		$new_passwd = Helper::get_theme_option( 'wpgc_gestsup_passwd' );
		if ( empty( $new_passwd ) ) {
			Helper::set_theme_option( 'wpgc_gestsup_passwd', array( $old_passwd ) );
		}

		$new_recaptcha = Helper::get_theme_option( 'wpgc_recaptcha' );
		if ( 'yes' !== $new_recaptcha && 'on' === $old_recaptcha ) {
			Helper::set_theme_option( 'wpgc_recaptcha', array( 'yes' ) );
		}

		$new_secret = Helper::get_theme_option( 'wpgc_recaptcha_secretkey' );
		if ( empty( $new_secret ) ) {
			Helper::set_theme_option( 'wpgc_recaptcha_secretkey', array( $old_secret ) );
		}

		$new_site = Helper::get_theme_option( 'wpgc_recaptcha_sitekey' );
		if ( empty( $new_site ) ) {
			Helper::set_theme_option( 'wpgc_recaptcha_sitekey', array( $old_site ) );
		}

		// Delete old Option
		delete_option( 'gestsup_recaptcha_secret_key' );
		delete_option( 'gestsup_recaptcha_site_key' );
		delete_option( 'gestsup_recaptcha_enable' );

		delete_option( 'gestsup_host' );
		delete_option( 'gestsup_dbname' );
		delete_option( 'gestsup_user' );
		delete_option( 'gestsup_pass' );
	}
}

new Options();
