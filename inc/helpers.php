<?php

use WPGC\GestSupAPI\GestsupAPI;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly.

add_action('show_user_profile', 'wpgc_add_id_to_profile');
function wpgc_add_id_to_profile(){
	?>
	<div><?php _e( 'GestSup ID : ', 'wp-gestsup-connector'); echo GestsupAPI::get_user_ID(); ?></div>
<?php
}
