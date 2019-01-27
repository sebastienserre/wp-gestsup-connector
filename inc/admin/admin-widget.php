<?php

use WPGC\GestSupAPI\GestsupAPI;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly.



		add_action( 'wp_dashboard_setup', 'dashboard_widget' );


	function dashboard_widget() {
		wp_add_dashboard_widget(
			'gestsup_dashboard_widget',
			__('My GestSup Tickets', 'wp-gestsup-connector'),
			'dashboard_render'
			);
	}

	function dashboard_render(){
		$states = GestsupAPI::wpgc_get_state();
		?>
<ul>
	<?php
		foreach ( $states as $state ){
			$tickets = GestsupAPI::wpgc_get_ticket( intval( $state['id'] ) );
			$nb = sizeof( $tickets );
			if ( 0 ==! $nb ) {
				echo '<li class="label label label-sm arrowed arrowed-right arrowed-left '. $state['display'] .'">' . $state['name'] . ': ' . sprintf(_n('%d  ticket', '%d tickets', $nb, 'wp-gestsup-connector'),$nb) . '</li>';
			}
		}
		?>
</ul>
<?php
	}
