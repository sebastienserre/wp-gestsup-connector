<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly.


class gestsup_options {

	public function __construct() {
		add_action( 'admin_init', array( $this, 'register_setting' ) );
		add_action( 'admin_init', array( $this, 'gestsup_mysql' ) );

	}

	public static function credentials() { ?>
		<form method="post" action="options.php">
			<?php settings_fields( 'gestsup_options_settings' ) ?>
			<?php do_settings_sections( 'gestsup_options_settings' ) ?>

			<?php submit_button( __( 'Save', 'wp-gestsup-connector' ) );
			?>
		</form>
		<?php
	}

	public static function gestsup_options() { ?>
		<form method="post" action="options.php">
			<?php settings_fields( 'gestsup_API' ) ?>
			<?php do_settings_sections( 'gestsup_options_API' ) ?>
			<?php submit_button( __( 'Save', 'wp-gestsup-connector' ), '', 'tech' ) ?>
		</form>
	<?php }

	public static function gestsup_reset() { ?>
		<form method="post" action="options.php">
			<?php settings_fields( 'gestsup_delete' ) ?>
			<?php do_settings_fields( 'gestsup_delete_data' ) ?>
		</form>
	<?php }

	public function register_setting() {
		register_setting( 'gestsup_options_settings', 'gestsup_host' );
		register_setting( 'gestsup_options_settings', 'gestsup_user' );
		register_setting( 'gestsup_options_settings', 'gestsup_dbname' );
		register_setting( 'gestsup_options_settings', 'gestsup_pass' );

		register_setting( 'gestsup_API', 'gestsup_tech' );
		register_setting( 'gestsup_API', 'gestsup_admin_support' );
		register_setting( 'gestsup_API', 'gestsup_admin_mail' );

		add_settings_section( 'gestsup_options', __( 'Save your GestSup db credentials', 'wp-gestsup-connector' ), array(
			$this,
			'section_html'
		), 'gestsup_options_settings' );


		add_settings_section( 'gestsup_API_options', __( 'Save your GestSup defaults infos', 'wp-gestsup-connector' ), array(
			$this,
			'section_API_html'
		), 'gestsup_options_API' );

		add_settings_section( 'reset', __( 'reset options', 'wp-gestsup-connector' ), array(
			$this,
			'del_option_html'
		), 'gestsup_delete' );

		add_settings_field( 'gestsup_host', __( 'Host Name', 'wp-gestsup-connector' ), array(
			$this,
			'host_html'
		), 'gestsup_options_settings', 'gestsup_options' );


		add_settings_field( 'gestsup_dbname', __( 'DB Name', 'wp-gestsup-connector' ), array(
			$this,
			'dbname_html'
		), 'gestsup_options_settings', 'gestsup_options' );
		add_settings_field( 'gestsup_user', __( 'DB UserName', 'wp-gestsup-connector' ), array(
			$this,
			'dbuser_html'
		), 'gestsup_options_settings', 'gestsup_options' );
		add_settings_field( 'gestsup_pass', __( 'DB Password', 'wp-gestsup-connector' ), array(
			$this,
			'dbpass_html'
		), 'gestsup_options_settings', 'gestsup_options' );

		add_settings_field( 'gestsup_admin_mail', __( 'Do you allow not registered?', 'wp-gestsup-connector' ), array(
			$this,
			'support_allow_not_registred_html'
		), 'gestsup_options_API', 'gestsup_API_options' );

		$allow_unregistred = get_option( 'gestsup_admin_mail' );
		if ( isset( $allow_unregistred ) && $allow_unregistred === '1' ) {
			add_settings_field( 'gestsup_admin_support', __( 'Support mail address', 'wp-gestsup-connector' ), array(
				$this,
				'support_mail_html'
			), 'gestsup_options_API', 'gestsup_API_options' );
		}


		$db = self::gestsup_mysql();
		if ( $db === 'nok' ) {
			return false;
		} else {
			add_settings_field( 'gestsup_tech', __( 'By default technician', 'wp-gestsup-connector' ), array(
				$this,
				'tech_html'
			), 'gestsup_options_API', 'gestsup_API_options' );

		}

	}

	public function section_html() {
		_e( 'Save your GestSup db credentials', 'wp-gestsup-connector' );
	}

	public static function del_option_html() { ?>
		<form method="post" action="#">
			<input name="reset" type="submit" value="<?php _e( 'Reset Settings', 'wp-gestsup-connector' ) ?>">
		</form>


		<?php if ( isset( $_POST['reset'] ) && $_POST['reset'] == 'Reset Settings' ) {
			delete_option( 'gestsup_host' );
			delete_option( 'gestsup_dbname' );
			delete_option( 'gestsup_user' );
			delete_option( 'gestsup_pass' );
			delete_option( 'gestsup_admin_mail' );
		}
	}


	public function section_API_html() {
		_e( 'Save your GestSup by defaults infos', 'wp-gestsup-connector' );

	}

	public function host_html() { ?>
		<input type="text" name="gestsup_host" placeholder="db host"
		       value="<?php echo get_option( 'gestsup_host' ); ?>"/>
	<?php }

	public function dbname_html() {
		?>
		<input type="text" name="gestsup_dbname" placeholder="db name"
		       value="<?php echo get_option( 'gestsup_dbname' ); ?>"/>
		<?php
	}

	public function dbuser_html() { ?>
		<input type="text" name="gestsup_user" placeholder="db user"
		       value="<?php echo get_option( 'gestsup_user' ); ?>"/>
	<?php }


	public function dbpass_html() {
		?>
		<input type="password" name="gestsup_pass" placeholder="db password"
		       value="<?php //echo get_option('gestsup_pass'); ?>"/>
		<?php
	}

	public function support_allow_not_registred_html() {
		$allow_unregistred = get_option( 'gestsup_admin_mail' ); ?>
		<input type="checkbox" name="gestsup_admin_mail"
		       value="1" <?php if ( isset( $allow_unregistred ) && $allow_unregistred === '1' ) {
			echo 'checked';
		} ?>>
	<?php }

	public function tech_html() {
		$db = $this->gestsup_mysql();
		$t = $db->query( " SELECT id,firstname, lastname FROM tusers WHERE profile = '4' or profile='0' or profile = '3' " );
		$option_tech = get_option( 'gestsup_tech' );

		?>
		<select name="gestsup_tech">
			<?php
			while ( $tech = $t->fetch_assoc() ) { ?>
				<option name="gestsup_tech" value="<?php echo $tech['id']; ?>"
					<?php

					if ( get_option( 'gestsup_tech' ) && $option_tech == $tech['id'] ) {
						echo "selected";
					}
					?>><?php echo $tech['firstname'] . ' ' . $tech['lastname']; ?></option>
				<?php
			} ?>
		</select>
		<?php
	}

	public function support_mail_html() {
		$allow_unregistred = get_option( 'gestsup_admin_mail' );
		if ( isset( $allow_unregistred ) && $allow_unregistred === '1' ) { ?>
			<input type="email" name="gestsup_admin_support"
			       placeholder="<?php _e( 'Mail to support team', 'wp-gestsup-connector' ) ?>"
			       value="<?php echo get_option( 'gestsup_admin_support' ); ?>"/>

		<?php }
	}

	public static function menu_html() {
		echo '<h1>' . get_admin_page_title() . '</h1>';
		echo self::credentials();
		echo self::check_sql();
		echo self::gestsup_options();
		echo self::del_option_html();

	}


	public static function gestsup_mysql() {

		$server = get_option( 'gestsup_host' );
		$db     = get_option( 'gestsup_dbname' );
		$user   = get_option( 'gestsup_user' );
		//$password = wp_hash_password(get_option( 'gestsup_pass' ));
		$password = get_option( 'gestsup_pass' );
		$connect  = 'nok';

		if ( empty( $server ) || empty( $db ) || empty ( $user ) || empty( $password ) ) {
			return $connect;
		} else {

			$connect = new Mysqli( $server, $user, $password, $db );

			return $connect;
		}
	}

	public static function check_sql() {
		$gestsup_mysql = self::gestsup_mysql();
		$connexion     = 0;
		if ( $gestsup_mysql === 'nok' ) {
			return $connexion = __( 'Failed to connect to MySQL, please check your login credentials', 'wp-gestsup-connector' );
		} else {
			return $connexion = __( 'Succesfully connected to the database', 'wp-gestsup-connector' );
		}
	}


}
