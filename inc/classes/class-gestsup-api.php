<?php
use Carbon_Fields\Container;
use Carbon_Fields\Field;
use Carbon_Fields\Helper\Helper;
use function add_action;
use function carbon_get_post_meta;
use function carbon_get_theme_option;

namespace WPGC\GestSupAPI;

use const ARRAY_A;
use function array_push;
use function get_current_user_id;
use function get_currentuserinfo;
use function get_user_meta;
use function is_object;
use function is_user_admin;
use function is_user_logged_in;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly.

/**
 * Class GestsupAPI
 *
 * @package wpgc\gestsupapi
 */
class GestsupAPI {

	var $db;

	public function __construct() {
		add_action( 'plugins_loaded', array( $this, 'gestsup_mysql' ) );
		$this->db = self::gestsup_mysql();
		add_action( 'wp_loaded', array( $this, 'thfo_add_ticket' ) );
	}

	/**
	 * @description Connect WP to the GestSup DB
	 * @return string|object return Object wpdb
	 */
	public static function gestsup_mysql() {

		$server   = get_option( '_wpgc_gestsup_host' );
		$db       = get_option( '_wpgc_gestsup_db' );
		$user     = get_option( '_wpgc_gestsup_username' );
		$password = get_option( '_wpgc_gestsup_passwd' );

		if ( empty( $server ) || empty( $db ) || empty ( $user ) || empty( $password ) ) {
			$connect = 'nok';
		} else {

			$connect = new \wpdb( $user, $password, $db, $server );

		}

		return $connect;
	}

	public static function wpgc_get_tech() {

		$techs = array();
		$db   = self::gestsup_mysql();
		if ( is_object( $db ) ) {
			$tech = $db->get_results( " SELECT id,firstname, lastname FROM tusers WHERE profile = '4' or profile='0' or profile = '3' ", ARRAY_A );
			foreach ( $tech as $t ){
				$techs[ $t['id'] ] = $t['firstname'] . ' ' .$t['lastname'];
			}
		}

	return $techs;
	}

	/**
	 * @return mixed
	 *
	 */
	public static function get_categories(){
		$db   = self::gestsup_mysql();
		if ( is_object( $db ) ) {
			$categories = $db->get_results( " SELECT * FROM tcategory ", ARRAY_A );
			foreach ( $categories as $cat ){
				$cats[ $cat['id']] = $cat['name'];
			}
		}
		return $cats;

	}

	/**
	 * @since 1.5.2
	 * @return mixed
	 *
	 */
	public static function wpgc_get_state(){
		$db = self::gestsup_mysql();
		if ( is_object( $db ) ){
			$states = $db->get_results( " SELECT * FROM tstates ", ARRAY_A );
			/*foreach ( $s as $state ){
				$states[ $state['id']] = $state['name'];
			}*/
		}
		if ( ! empty( $states ) ) {
			return $states;
		}
	}

	/**
	 * @since 1.5.2
	 * @return mixed
	 *
	 */
	public static function wpgc_get_ticket( $state ){
		$db = self::gestsup_mysql();
		if ( is_object( $db ) ){
			$tickets = $db->get_results( "SELECT * FROM tincidents WHERE state LIKE $state AND disable LIKE 0", ARRAY_A );
		}

		if ( ! empty( $tickets ) ) {
			return $tickets;
		}
	}

	/**
	 * @since 1.5.2
	 * @return mixed
	 */
	public static function wpgc_get_parameters(){
		$db = self::gestsup_mysql();
		if ( $db ){

			$parameters = $db->get_results( "SELECT * FROM tparameters", ARRAY_A );
		}

		if ( !empty( $parameters ) ){
			return $parameters;
		}
	}

	public static function get_user_ID(){
		if ( is_user_logged_in() && is_admin() ){
			$current_user_data = get_userdata( get_current_user_id() );
			$current_user_email = $current_user_data->user_email;
			$db = self::gestsup_mysql();
			if ( $db ){
				$gestsup_user_data = $db->get_results( "SELECT * FROM `tusers` WHERE `mail` LIKE '$current_user_email'", ARRAY_A);
				$user_gestsup_ID = $gestsup_user_data[0]['id'];
			}
		}
		return $user_gestsup_ID;
	}

	public function thfo_add_ticket() {

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
						$this->gestsup_check_and_add();
					} else {
						add_action( 'gestup-before-form', 'gestsup_recaptcha_error' );

					}
				}

			} else {
				$this->gestsup_check_and_add();
			}
		}

	}

	public function gestsup_check_and_add() {
		/**
		 * Is a GestSup Account exists?
		 *
		 * @var $search_mail
		 */
		$search_mail = $this->search_mail();

		if ( ! empty( $search_mail ) ) {
			foreach ( $search_mail as $search ) {
				if ( $search->mail != $_POST['mail'] ) {
					/*
					 * User does'nt exist
					 */
					$this->gestsup_create_user();

				} else {

					$this->add_ticket_db();

				}
			}

		} else {
			//die('create');
			$this->gestsup_create_user();
		}
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
		$v         = self::gestsup_mysql();
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

		$this->add_ticket_db();

	}

	/**
	 * @return mixed
	 */
	public function search_mail() {
		if ( isset( $_POST['add_ticket'] ) && ! empty( $_POST['mail'] ) ) {
			$v       = self::gestsup_mysql();
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

	public function add_ticket_db() {
		if ( isset( $_POST['add_ticket'] ) ) {

			$ticket     = apply_filters( 'the_content', sanitize_text_field( $_POST['ticket'] ) );
			$title      = sanitize_text_field( $_POST['title'] );
			$cat        = sanitize_text_field( $_POST['cat'] );
			$date       = current_time( 'Y-m-d H:m:s' );
			$data_users = $this->search_mail();
			foreach ( $data_users as $data_user ) {
				$user = $data_user->id;
			}
			$tech = get_option( 'gestsup_tech' );

			$v = self::gestsup_mysql();
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



}

new GestsupAPI();
