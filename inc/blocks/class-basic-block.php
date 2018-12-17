<?php

namespace WPGestsup\Blocks\Basic;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly.

use function add_action;
use Carbon_Fields\Container;
use Carbon_Fields\Field;
use function var_dump;
use function wp_enqueue_style;
use function wp_register_style;

/**
 * Class BasicBlock
 *
 * @package WPGestsup\Blocks\Basic
 */

class BasicBlock {

	public function __construct() {
		add_action( 'plugins_loaded', array( $this, 'wpgc_basic_block' ) );
	}

	public function wpgc_block_fields(){
		$fields = array();

		$fields[] =  Field::make( 'html', 'crb_html', __( 'Section Description' ) )
		                  ->set_html( '<p>WP Gestsup Connector Basic Block</p>' );

		$fields[] = Field::make( 'checkbox', 'wpgc_recaptcha', __( 'Show ReCaptcha' ) )
		                 ->set_option_value( 'yes' );
		return $fields;
	}

	public function wpgc_basic_block(){

		$fields = $this->wpgc_block_fields();

		Container::make( 'block', __( 'WP GestSup Connector Basic Block' ) )
		         ->add_fields( $fields )
		         ->set_render_callback( array( $this, 'render' ) )
		         ->set_category( 'custom-category', __( 'Thivinfo\'s block' ), 'smiley' );
	}

	public function render( $block ){
		thfo_disable_com();
		if ( is_user_logged_in() ) {
			$user_info = wp_get_current_user();
			$mail      = $user_info->user_email;
			$firstname = $user_info->user_firstname;
			$lastname  = $user_info->user_lastname;

			/**
			 * Check languages
			 */
			if ( function_exists( 'get_user_locale' ) ) {
				/**
				 * From WP4.7
				 */
				$lang = get_user_locale( $user_info->ID );
			} else {
				$lang = $_POST['lang'];
			}
		} elseif ( isset( $_POST['mail'] ) ) {
			$mail = $_POST['mail'];
		} else {
			$mail = '';
		}

		if ( ! empty( $_POST['firstname'] ) ) {
			$firstname = $_POST['firstname'];
		}

		if ( ! empty( $_POST['lastname'] ) ) {
			$lastname = $_POST['lastname'];
		}

		do_action( 'gestup-before-form' );

		ob_start();
		?>
		<form class="wp-gestsup-add-ticket-shortcode" method="post" action="#">

		<p class="gestsup-mail"><label for="mail"><?php _e( "Your email:", 'wp-gestsup-connector' ) ?></label>
			<input type="email" name="mail" value="<?php echo $mail ?>"/></p>
		<?php
		if ( ! is_user_logged_in() ) {
			?>
			<p class="gestsup-login"><a
						href="<?php echo wp_login_url( get_permalink() ) ?>"> <?php _e( 'Login:', 'wp-gestsup-connector' ) ?></a>
			</p>
			<p class="gestsup-info"><?php _e( 'Let\'s creating a Gestsup account by choosing a password(if it does\'nt exist)', 'wp-gestsup-connector' ) ?></p>
			<p class="wp-gestsup-password"><label
						for="password"><?php _e( "Choose your password:", 'wp-gestsup-connector' ) ?></label>
				<input type="password" name="password"></p>
		<?php } ?>
		<p class="wp-gestsup-firstname"><label for="firstname"><?php _e( "Firstname:", 'wp-gestsup-connector' ) ?></label>
			<input type="text" name="firstname" value="<?php echo $firstname; ?>"></p>
		<p class="wp-gestsup-lastname"><label for="lastname"><?php _e( "lastname:", 'wp-gestsup-connector' ) ?></label>
			<input type="text" name="lastname" value="<?php echo $lastname; ?>"></p>

		<p class="wp-gestsup-lang"><label for="lang"><?php _e( "Prefered language:", 'wp-gestsup-connector' ) ?></label>
			<select name="lang">
				<option value="fr_FR" <?php selected( 'fr_FR', $lang ); ?> ><?php _e( "French", 'wp-gestsup-connector' ) ?></option>
				<option value="en_US" <?php selected( 'en_US', $lang ); ?>><?php _e( "English", 'wp-gestsup-connector' ) ?></option>
				<option value="de_DE" <?php selected( 'de_DE', $lang ); ?>><?php _e( "German", 'wp-gestsup-connector' ) ?></option>
				<option value="es_ES" <?php selected( 'es_ES', $lang ); ?>><?php _e( "Spanish", 'wp-gestsup-connector' ) ?></option>
			</select>
		</p>
		<p class="wp-gestsup-title">
			<label for="title"><?php _e( "Title:", 'wp-gestsup-connector' ) ?></label>
			<input type="text" name="title"/>
		</p>
		<p class="wp-gestsup-ticket">
			<label for="ticket"><?php _e( "Ticket:", 'wp-gestsup-connector' ) ?></label>
			<textarea name="ticket" cols="50" rows="10"></textarea>
		</p>
		<?php


		$form = ob_get_clean();


		/**
		 * If recaptcha is enabled in options, we add the integration code
		 */

		$recaptcha_enable = get_option( 'gestsup_recaptcha_enable' );
		if ( $recaptcha_enable === 'on' && $block['wpgc_recaptcha'] === 'yes' ) {
			$sitekey = get_option( 'gestsup_recaptcha_site_key' );
			$form    .= '<div class="g-recaptcha gestsup-recaptcha" data-sitekey=" ' . $sitekey . ' "></div>';
		}

		$form .= '<input type="submit" value=" ' . __( 'Send', 'wp-gestsup-connector' ) . '" name="add_ticket" ></form>';
		do_action( 'thfo-form' );

		echo $form;
	}



}
new BasicBlock();
