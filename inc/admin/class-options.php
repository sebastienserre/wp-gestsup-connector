<?php

namespace WPGestSup\Admin\Options;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly.


use Carbon_Fields\Container;
use Carbon_Fields\Field;

/**
 * Class Options
 *
 * @package WPGestSup\Admin\Options
 */
class Options {

	public function __construct() {
		add_action( 'carbon_fields_register_fields', array( $this, 'wpgc_settings' ) );
		add_action( 'plugins_loaded', array( $this, 'wpgc_load' ) );
	}

	public function wpgc_settings() {
		Container::make( 'theme_options', __( 'Gestsup Connector' ) )
		         ->set_page_parent( 'options-general.php' )
		         ->add_fields(
			         array(
				         Field::make( 'text', 'crb_text', 'Text Field' ),
			         )
		         );
	}

	public function wpgc_load() {
		require_once WPGC_PLUGIN_PATH . '/inc/vendor/autoload.php';
		\Carbon_Fields\Carbon_Fields::boot();
	}

}

new Options();
