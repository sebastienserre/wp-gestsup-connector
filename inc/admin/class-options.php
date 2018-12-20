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
		$tech = GestsupAPI::wpgc_get_tech();
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
				              ->set_options( $tech )
			         )

		         )
		->add_tab( __( 'GestSup Database', 'wp-gestsup-connector' ),
			array(
				Field::make( 'text', 'wpgc_gestsup_host', __( 'Host', 'wp-gestsup-connector' ) ),
				Field::make( 'text', 'wpgc_gestsup_db', __( 'Database', 'wp-gestsup-connector' ) ),
				Field::make( 'text', 'wpgc_gestsup_username', __( 'Username', 'wp-gestsup-connector' ) ),
				Field::make( 'text', 'wpgc_gestsup_passwd', __( 'Password', 'wp-gestsup-connector' ) ),
			)
		);
	}

	public function wpgc_update_option() {
		// Get Old Option
		$old_secret    = get_option( 'gestsup_recaptcha_secret_key' );
		$old_site      = get_option( 'gestsup_recaptcha_site_key' );
		$old_recaptcha = get_option( 'gestsup_recaptcha_enable' );

		$old_host = get_option( 'gestsup_host' );
		$old_db = get_option( 'gestsup_dbname' );
		$old_user = get_option( 'gestsup_user' );
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
