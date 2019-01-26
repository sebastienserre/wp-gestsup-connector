<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly.

use Elementor\Settings;

add_shortcode( 'gestsup_add_ticket', 'add_ticket' );
add_action( 'wp_loaded', 'thfo_add_ticket' );

/**
 * Shortcode [gestup_add_ticket]
 *
 * @return string
 *
 */

/**
 * Form to add a ticket
 *
 * @return string
 */
function add_ticket() {
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

	$recaptcha_enable = get_option( '_wpgc_recaptcha' );
	if ( $recaptcha_enable === 'yes' ) {
		$sitekey = get_option( '_wpgc_recaptcha_sitekey' );
		$form    .= '<div class="g-recaptcha gestsup-recaptcha" data-sitekey=" ' . $sitekey . ' "></div>';
	}

	$form .= '<input type="submit" value=" ' . __( 'Send', 'wp-gestsup-connector' ) . '" name="add_ticket" ></form>';
	do_action( 'thfo-form' );

	return $form;
}

/**
 * Disable comments on Shortcode page
 */
function thfo_disable_com() {
	global $post;
	if ( $post->comment_status == "open" ) {
		$args = array(
			'ID'             => $post->ID,
			'comment_status' => 'close',
		);
		wp_update_post( $args, true );
	}
}

function search_mail() {
	if ( isset( $_POST['add_ticket'] ) && ! empty( $_POST['mail'] ) ) {
		$v       = \WPGC\GestSupAPI\GestsupAPI::gestsup_mysql();
		$results = $v->get_results( "SELECT * FROM tusers WHERE mail = '$_POST[mail]'" );
		if ( ! empty( $results ) ) {
			foreach ( $results as $m ) {
				$mail = $m->mail;
				if ( $mail === $_POST['mail'] ) {
					return $results;
				}
			}
		}
	}
}

function thfo_add_ticket() {

	if ( isset( $_POST['add_ticket'] ) && ! empty( $_POST['mail'] ) ) {
		/**
		 * We're checking if Google recaptcha is OK (if enabled in options)
		 */

		$recaptcha_enable = get_option( 'gestsup_recaptcha_enable' );

		if ( $recaptcha_enable == 'on' ) {

			if ( isset( $_POST['g-recaptcha-response'] ) && ! empty( $_POST['g-recaptcha-response'] ) ) {
				//your site secret key
				$secret = get_option( 'gestsup_recaptcha_secret_key' );
				//get verify response data
				$verifyResponse = file_get_contents( 'https://www.google.com/recaptcha/api/siteverify?secret=' . $secret . '&response=' . $_POST['g-recaptcha-response'] );
				$responseData   = json_decode( $verifyResponse );
				if ( $responseData->success ) {
					gestsup_check_and_add();
				} else {
					add_action( 'gestup-before-form', 'gestsup_recaptcha_error' );

				}
			}

		} else {
			gestsup_check_and_add();
		}
	}

}

function gestsup_recaptcha_error() {
	echo '<div class="error gestsup-recaptcha">' . __( 'An Error Occured with Recaptcha', 'wp-gestsup-connector' ) . '</div>';
}

function gestsup_check_and_add() {
	/**
	 * Is a GestSup Account exists?
	 *
	 * @var $search_mail
	 */
	$search_mail = search_mail();

	if ( ! empty( $search_mail ) ) {
		foreach ( $search_mail as $search ) {
			if ( $search->mail != $_POST['mail'] ) {
				/*
				 * User does'nt exist
				 */
				gestsup_create_user();

			} else {

				add_ticket_db();

			}
		}

	} else {
		//die('create');
		gestsup_create_user();
	}
}

function add_ticket_db() {
	if ( isset( $_POST['add_ticket'] ) ) {

		$ticket     = apply_filters( 'the_content', sanitize_text_field( $_POST['ticket'] ) );
		$title      = sanitize_text_field( $_POST['title'] );
		$cat        = sanitize_text_field( $_POST['cat'] );
		$date       = current_time( 'Y-m-d H:m:s' );
		$data_users = search_mail();
		foreach ( $data_users as $data_user ) {
			$user = $data_user->id;
		}
		$tech = get_option( 'gestsup_tech' );

		$v = \WPGC\GestSupAPI\GestsupAPI::gestsup_mysql();
		$v->insert( 'tincidents',
			array(
				'technician'  => $tech,
				'user'        => $user,
				'title'       => $title,
				'description' => $ticket,
				'state'       => '1',
				'date_create' => $date,
				'creator'     => $user,
				'criticality' => '4',
				'techread'    => '0',
				'category'    => $cat,

			) );

	}

	add_action( 'gestup-before-form', 'gestup_ticket_creation_confirmation' );

}

function gestup_ticket_creation_confirmation() {
	echo '<div class="success ticket-creation">' . __( 'Your Ticket is Succesfully Created', 'wp-gestsup-connector' ) . '</div>';

}


/**
 * User is created in gestsup db
 */
function gestsup_create_user() {
	$mail      = $_POST['mail'];
	$passwd    = $_POST['password'];
	$firstname = $_POST['firstname'];
	$lastname  = $_POST['lastname'];
	$lang      = $_POST['lang'];
	$v         = gestsup_options::gestsup_mysql();
	$v->insert( 'tusers',
		array(
			'login'     => $mail,
			'password'  => $passwd,
			'mail'      => $mail,
			'lastname'  => $lastname,
			'firstname' => $firstname,
			'profile'   => 2,
			'language'  => $lang,
		)

	);

	add_ticket_db();

}
