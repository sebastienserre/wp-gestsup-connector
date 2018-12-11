<?php
/**
 * Created by PhpStorm.
 * User: sebastien
 * Date: 09/04/17
 * Time: 14:26
 */

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
	} elseif ( isset( $_POST['mail'] ) ) {
		$mail = $_POST['mail'];
	} else {
		$mail = '';
	}
	do_action( 'gestup-before-form' );
	$form = '<form method="post" action="#">';
	$form .= '<label for="mail">' . __( "Your email:", 'wp-gestsup-connector' ) . '</label>';
	$form .= '<input type="email" name="mail" value="' . $mail . '" />';
	$form .= '<p> <a href="' . wp_login_url( get_permalink() ) . '"> ' . __( 'Login', 'wp-gestsup-connector' ) . '</a></p>';
	$form .= '<p>' . __( 'Let\'s creating a Gestsup account by choosing a password(if it does\'nt exist)', 'wp-gestsup-connector' ) . '</p>';
	$form .= '<label for="password">' . __( "Choose your password", 'wp-gestsup-connector' ) . '</label>';
	$form .= '<input type="password" name="password" >';
	$form .= '<label for="firstname">' . __( "Firstname", 'wp-gestsup-connector' ) . '</label>';
	$form .= '<input type="text" name="firstname" >';
	$form .= '<label for="lastname">' . __( "lastname", 'wp-gestsup-connector' ) . '</label>';
	$form .= '<input type="text" name="lastname" >';
	$form .= '<label for="title">' . __( "Title:", 'wp-gestsup-connector' ) . '</label>';
	$form .= '<input type="text" name="title" /><label for="ticket">' . __( "Ticket:", 'wp-gestsup-connector' ) . '</label>';
	$form .= '<textarea name="ticket" cols="50" rows="10"></textarea>';

	/**
	 * If recaptcha is enabled in options, we add the integration code
	 */

	$recaptcha_enable = get_option( 'gestsup_recaptcha_enable' );
	if ( $recaptcha_enable == 'on' ) {
		$sitekey = get_option( 'gestsup_recaptcha_site_key' );
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
		$v       = gestsup_options::gestsup_mysql();
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
		$date       = current_time( 'Y-m-d H:m:s' );
		$data_users = search_mail();
		foreach ( $data_users as $data_user ) {
			$user = $data_user->id;
		}
		$tech = get_option( 'gestsup_tech' );

		$v = gestsup_options::gestsup_mysql();
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
	$v         = gestsup_options::gestsup_mysql();
	$v->insert( 'tusers',
		array(
			'login'     => $mail,
			'password'  => $passwd,
			'mail'      => $mail,
			'lastname'  => $lastname,
			'firstname' => $firstname,
			'profile'   => 2,
		)

	);

	add_ticket_db();

}
