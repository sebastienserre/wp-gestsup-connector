<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly.


class gestsup_options {

	public function __construct() {
		add_action( 'admin_init', array( $this, 'register_setting' ) );
		add_action( 'admin_init', array( $this, 'gestsup_mysql' ) );
		add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );

	}

	public static function gestsup_reset() { ?>
		<form method="post" action="options.php">
			<?php settings_fields( 'gestsup_delete' ) ?>
			<?php do_settings_fields( 'gestsup_delete_data' ) ?>
		</form>
	<?php }

	public static function menu_html() {
		echo '<h1>' . get_admin_page_title() . '</h1>';
		echo self::credentials();
		echo self::check_sql();
		echo self::gestsup_options();
		echo self::del_option_html();

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

	public static function check_sql() {
		$gestsup_mysql = self::gestsup_mysql();
		$connexion     = 0;
		if ( $gestsup_mysql === 'nok' ) {
			return $connexion = __( 'Failed to connect to MySQL, please check your login credentials', 'wp-gestsup-connector' );
		} else {
			return $connexion = __( 'Succesfully connected to the database', 'wp-gestsup-connector' );
		}
	}

	public static function gestsup_options() { ?>
		<form method="post" action="options.php">
			<?php settings_fields( 'gestsup_API' ) ?>
			<?php do_settings_sections( 'gestsup_options_API' ) ?>
			<?php submit_button( __( 'Save', 'wp-gestsup-connector' ), '', 'tech' ) ?>
		</form>
	<?php }

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

	public function add_admin_menu() {
		add_menu_page( 'WP Gestsup', 'WP Gestsup', 'manage_options', 'wp-gestsup', array(
			$this,
			'main_menu_html'
		), dirname( dirname( plugin_dir_url( __FILE__ ) ) ) . '/assets/img/icon.png' );
		add_submenu_page( 'wp-gestsup', 'Options', 'Options', 'manage_options', 'option', array(
			'gestsup_options',
			'menu_html'
		) );

	}

	public function main_menu_html() {
		echo '<h1>' . get_admin_page_title() . '</h1>';
		echo '<h2>' . _e( 'Hello, Many thanx to use this plugin', 'wp-gestsup-connector' ); ?> </h2>

		<h2><?php _e( 'FAQ', 'wp-gestsup-connector' ) ?> </h2>
		<ul>
			<strong>
				<li><?php _e( 'How-to add a form in my website to create a ticket?', 'wp-gestsup-connector' ) ?></li>
			</strong>
			<ul>
				<li><?php _e( 'Add the shortcode [gestsup_add_ticket] in a page you\'ve created', 'wp-gestsup-connector' ) ?></li>
			</ul>
			<strong>
				<li><?php _e( 'Where can I find the site & secret key asked in options page?', 'wp-gestsup-connector' ) ?></li>
			</strong>
			<ul>
				<li><?php _e( 'You can find it (only Recaptcha V2) on ', 'wp-gestsup-connector' ) ?><a
							href="https://www.google.com/recaptcha"><?php _e( 'Google recaptcha Website ', 'wp-gestsup-connector' ) ?></a>
				</li>
			</ul>
		</ul>
		<h2><?php _e( 'If You Enjoy this plugin... encourage me!', 'wp-gestsup-connector' ) ?></h2>
		<form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top">
			<input type="hidden" name="cmd" value="_s-xclick">
			<input type="hidden" name="encrypted" value="-----BEGIN PKCS7-----MIIHLwYJKoZIhvcNAQcEoIIHIDCCBxwCAQExggEwMIIBLAIBADCBlDCBjjELMAkGA1UEBhMCVVMxCzAJBgNVBAgTAkNBMRYwFAYDVQQHEw1Nb3VudGFpbiBWaWV3MRQwEgYDVQQKEwtQYXlQYWwgSW5jLjETMBEGA1UECxQKbGl2ZV9jZXJ0czERMA8GA1UEAxQIbGl2ZV9hcGkxHDAaBgkqhkiG9w0BCQEWDXJlQHBheXBhbC5jb20CAQAwDQYJKoZIhvcNAQEBBQAEgYCH+XB1NYR7SSgmbUaG0VxFTaR3FBaSjkdPUPMq3VvEm9M+CS1M3vNEY76GFO3NrYIWu8mi7wsASGcLNFEgDZ5Y9Y/3aKGTPLBG/iiPc4H+fj29GlFsuyRPyK7KToMy17bW/ZyovFKqVNNsoqInH5Ac/PrMp8R3XDkGNs5hS2YTCTELMAkGBSsOAwIaBQAwgawGCSqGSIb3DQEHATAUBggqhkiG9w0DBwQIAO8KuyjrpZeAgYiZKtJ4v/a1m7L5iPUQEJKWGENots0+vY7SGwKY4BzXwZXjIkq4kG4nsy3ijSAru70ubT0op2jQzK5QnsIJoAtyg3+rS3/P+MWIoN1L0HIKzww+wcA7xB6GuqYRScEYdjObTuY3rlCVGg8xfNUTJGjirzkdSdIbPIzTnpBIE57mTxqb6k3uDJKEoIIDhzCCA4MwggLsoAMCAQICAQAwDQYJKoZIhvcNAQEFBQAwgY4xCzAJBgNVBAYTAlVTMQswCQYDVQQIEwJDQTEWMBQGA1UEBxMNTW91bnRhaW4gVmlldzEUMBIGA1UEChMLUGF5UGFsIEluYy4xEzARBgNVBAsUCmxpdmVfY2VydHMxETAPBgNVBAMUCGxpdmVfYXBpMRwwGgYJKoZIhvcNAQkBFg1yZUBwYXlwYWwuY29tMB4XDTA0MDIxMzEwMTMxNVoXDTM1MDIxMzEwMTMxNVowgY4xCzAJBgNVBAYTAlVTMQswCQYDVQQIEwJDQTEWMBQGA1UEBxMNTW91bnRhaW4gVmlldzEUMBIGA1UEChMLUGF5UGFsIEluYy4xEzARBgNVBAsUCmxpdmVfY2VydHMxETAPBgNVBAMUCGxpdmVfYXBpMRwwGgYJKoZIhvcNAQkBFg1yZUBwYXlwYWwuY29tMIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQDBR07d/ETMS1ycjtkpkvjXZe9k+6CieLuLsPumsJ7QC1odNz3sJiCbs2wC0nLE0uLGaEtXynIgRqIddYCHx88pb5HTXv4SZeuv0Rqq4+axW9PLAAATU8w04qqjaSXgbGLP3NmohqM6bV9kZZwZLR/klDaQGo1u9uDb9lr4Yn+rBQIDAQABo4HuMIHrMB0GA1UdDgQWBBSWn3y7xm8XvVk/UtcKG+wQ1mSUazCBuwYDVR0jBIGzMIGwgBSWn3y7xm8XvVk/UtcKG+wQ1mSUa6GBlKSBkTCBjjELMAkGA1UEBhMCVVMxCzAJBgNVBAgTAkNBMRYwFAYDVQQHEw1Nb3VudGFpbiBWaWV3MRQwEgYDVQQKEwtQYXlQYWwgSW5jLjETMBEGA1UECxQKbGl2ZV9jZXJ0czERMA8GA1UEAxQIbGl2ZV9hcGkxHDAaBgkqhkiG9w0BCQEWDXJlQHBheXBhbC5jb22CAQAwDAYDVR0TBAUwAwEB/zANBgkqhkiG9w0BAQUFAAOBgQCBXzpWmoBa5e9fo6ujionW1hUhPkOBakTr3YCDjbYfvJEiv/2P+IobhOGJr85+XHhN0v4gUkEDI8r2/rNk1m0GA8HKddvTjyGw/XqXa+LSTlDYkqI8OwR8GEYj4efEtcRpRYBxV8KxAW93YDWzFGvruKnnLbDAF6VR5w/cCMn5hzGCAZowggGWAgEBMIGUMIGOMQswCQYDVQQGEwJVUzELMAkGA1UECBMCQ0ExFjAUBgNVBAcTDU1vdW50YWluIFZpZXcxFDASBgNVBAoTC1BheVBhbCBJbmMuMRMwEQYDVQQLFApsaXZlX2NlcnRzMREwDwYDVQQDFAhsaXZlX2FwaTEcMBoGCSqGSIb3DQEJARYNcmVAcGF5cGFsLmNvbQIBADAJBgUrDgMCGgUAoF0wGAYJKoZIhvcNAQkDMQsGCSqGSIb3DQEHATAcBgkqhkiG9w0BCQUxDxcNMTUwOTA3MjIxMDIzWjAjBgkqhkiG9w0BCQQxFgQUj5y5YF0IcDpFgH2jCvS9Ip99IkwwDQYJKoZIhvcNAQEBBQAEgYAqdLe45cqnzU74zEmKYg3I0Akjc87aoQYczzFVoUG0DMtNABriV9HVoIUR/yXI4aTI+Soy3h42ojqRYUGVBAhQ9p7+xi7vnoe0nY3evBkXQN0tgk16cSuuG6yy3QYiuEuqytDuY46L8y8aSdtd33XHzzZtVyeFnXCzg1I/Va6cWg==-----END PKCS7-----
">
			<input type="image" src="https://www.paypalobjects.com/fr_FR/FR/i/btn/btn_donateCC_LG.gif" border="0"
			       name="submit" alt="PayPal - la solution de paiement en ligne la plus simple et la plus sécurisée !">
			<img alt="" border="0" src="https://www.paypalobjects.com/fr_FR/i/scr/pixel.gif" width="1" height="1">
		</form>
		<?php

	}

	public function register_setting() {
		register_setting( 'gestsup_options_settings', 'gestsup_host' );
		register_setting( 'gestsup_options_settings', 'gestsup_user' );
		register_setting( 'gestsup_options_settings', 'gestsup_dbname' );
		register_setting( 'gestsup_options_settings', 'gestsup_pass' );

		register_setting( 'gestsup_API', 'gestsup_tech' );
		register_setting( 'gestsup_API', 'gestsup_admin_support' );
		register_setting( 'gestsup_API', 'gestsup_admin_mail' );
		register_setting( 'gestsup_API', 'gestsup_recaptcha_enable' );
		register_setting( 'gestsup_API', 'gestsup_recaptcha_site_key' );
		register_setting( 'gestsup_API', 'gestsup_recaptcha_secret_key' );

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

		$db = self::gestsup_mysql();
		if ( $db === 'nok' ) {
			return false;
		} else {
			add_settings_field( 'gestsup_tech', __( 'By default technician', 'wp-gestsup-connector' ), array(
				$this,
				'tech_html'
			), 'gestsup_options_API', 'gestsup_API_options' );

		}

		add_settings_field( 'gestsup_recaptcha_enable', __( 'Activate Google Recaptcha', 'wp-gestsup-connector' ), array(
			$this,
			'gestsup_recaptcha_enable'
		), 'gestsup_options_API', 'gestsup_API_options' );

		$recaptcha_enable = get_option( 'gestsup_recaptcha_enable' );

		if ( $recaptcha_enable == 'on' ) {
			add_settings_field( 'gestsup_recaptcha_site_key', __( 'Insert your Google Recaptcha Site Key', 'wp-gestsup-connector' ), array(
				$this,
				'gestsup_recaptcha_site_key'
			), 'gestsup_options_API', 'gestsup_API_options' );
			add_settings_field( 'gestsup_recaptcha_secret_key', __( 'Insert your Google Recaptcha Secret Key', 'wp-gestsup-connector' ), array(
				$this,
				'gestsup_recaptcha_secret_key'
			), 'gestsup_options_API', 'gestsup_API_options' );
		}

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

			$connect = new wpdb( $user, $password, $db, $server );

			return $connect;
		}
	}

	public function gestsup_recaptcha_enable() {
		$recaptcha_enable = get_option( 'gestsup_recaptcha_enable' );
		$recaptcha        = '<input type="checkbox" name="gestsup_recaptcha_enable"';
		if ( $recaptcha_enable == 'on' ) {
			$recaptcha .= 'checked';
		}
		$recaptcha .= '>';
		echo $recaptcha;
	}

	public function gestsup_recaptcha_site_key() {
		$sitekey       = get_option( 'gestsup_recaptcha_site_key' );
		$sitekey_input = '<input name="gestsup_recaptcha_site_key" type="text" value="' . $sitekey . '">';
		echo $sitekey_input;
	}

	public function gestsup_recaptcha_secret_key() {
		$sitesecret       = get_option( 'gestsup_recaptcha_secret_key' );
		$sitesecret_input = '<input name="gestsup_recaptcha_secret_key" type="text" value="' . $sitesecret . '">';
		echo $sitesecret_input;
	}

	public function section_html() {
		_e( 'Save your GestSup db credentials', 'wp-gestsup-connector' );
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
		       value="<?php echo get_option( 'gestsup_pass' ); ?>"/>
		<?php
	}

	public function tech_html() {
		$db          = $this->gestsup_mysql();
		$t           = $db->get_results( " SELECT id,firstname, lastname FROM tusers WHERE profile = '4' or profile='0' or profile = '3' " );
		$option_tech = get_option( 'gestsup_tech' );

		?>
		<select name="gestsup_tech">
			<?php
			foreach ( $t as $tech ) { ?>
				<option name="gestsup_tech" value="<?php echo $tech->id; ?>"
					<?php

					if ( get_option( 'gestsup_tech' ) && $option_tech == $tech->id ) {
						echo "selected";
					}
					?>><?php echo $tech->firstname . ' ' . $tech->lastname; ?></option>
				<?php
			} ?>
		</select>
		<?php
	}


}

new gestsup_options();
