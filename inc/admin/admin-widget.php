<?php
use WPGC\GestSupAPI\GestsupAPI;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly.

add_action( 'wp_dashboard_setup', 'dashboard_widget' );


function dashboard_widget() {
	wp_add_dashboard_widget(
		'gestsup_dashboard_widget',
		__( 'My GestSup Tickets', 'wp-gestsup-connector' ),
		'dashboard_render',
		'dashboard_render_handle'
	);
}

function dashboard_render() {
	$states     = GestsupAPI::wpgc_get_state();
	$parameters = GestsupAPI::wpgc_get_parameters();
	$user_ID = GestsupAPI::get_user_ID();
	?>
	<div class="wpgc-dashboard">
		<p><?php _e( 'Many Thanks for choosing WP GestSup Connector. You\'ll find below your tickets preview', 'wp-gestsup-connector' ); ?></p>
		<ul>
			<?php
			foreach ( $states as $state ) {
				$option_name = 'wpgc_admin_dashboard_settings[' . $state['name'] . ']';
				$option      = get_option( $option_name );
				$noticket    = get_option( 'wpgc_admin_dashboard_settings[notickets]' );
				if ( null !== $option['state'] ) {
					$tickets = GestsupAPI::wpgc_get_ticket( intval( $state['id'] ) );
					
					if (is_array($tickets)) {
    						$nb = sizeof($tickets);
					} else {
    						$nb = 0; // Set a default value if $tickets is not an array
					}

					$url = $parameters[0]['server_url'];
					$url = add_query_arg(
							array(
									'page' => 'dashboard',
									'userid' => $user_ID,
									'state' => $state['id'],
							),
							$url
					);

					if ( 0 !== $nb || 'yes' === $noticket['state'] ) {
						ob_start();
						?>
						<li class="label label label-sm arrowed arrowed-right arrowed-left <?php echo $state['display']; ?>">
							<a href="<?php echo $url; ?>" target="_blank">
							<?php echo $state['name'] ?>
							: <?php printf( _n( '%d  ticket', '%d tickets', $nb, 'wp-gestsup-connector' ), $nb ); ?>
							</a>
						</li>
						<?php
						echo ob_get_clean();
					}
				}
			}
			?>
		</ul>
		<div class="wpgc-stars">
        <span id="wpgc-footer-credits">
                <span class="dashicons dashicons-wordpress"></span>
	        <?php _e( "You like WP GestSup Connector ? Don't forget to rate it 5 stars !", "wp-gestsup-connector" ) ?>

            <span class="wporg-ratings rating-stars">
                    <a href="//wordpress.org/support/view/plugin-reviews/wp-gestsup-connector?rate=1#postform"
                       data-rating="1" title="" target="_blank"><span class="dashicons dashicons-star-filled"
                                                                      style="color:#FFDE24 !important;"></span></a>
                    <a href="//wordpress.org/support/view/plugin-reviews/wp-gestsup-connector?rate=2#postform"
                       data-rating="2" title="" target="_blank"><span class="dashicons dashicons-star-filled"
                                                                      style="color:#FFDE24 !important;"></span></a>
                    <a href="//wordpress.org/support/view/plugin-reviews/wp-gestsup-connector?rate=3#postform"
                       data-rating="3" title="" target="_blank"><span class="dashicons dashicons-star-filled"
                                                                      style="color:#FFDE24 !important;"></span></a>
                    <a href="//wordpress.org/support/view/plugin-reviews/wp-gestsup-connector?rate=4#postform"
                       data-rating="4" title="" target="_blank"><span class="dashicons dashicons-star-filled"
                                                                      style="color:#FFDE24 !important;"></span></a>
                    <a href="//wordpress.org/support/view/plugin-reviews/wp-gestsup-connector?rate=5#postform"
                       data-rating="5" title="" target="_blank"><span class="dashicons dashicons-star-filled"
                                                                      style="color:#FFDE24 !important;"></span></a>
                </span>
                <script>
                    jQuery(document).ready(function ($) {
                        $(".rating-stars").find("a").hover(
                            function () {
                                $(this).nextAll("a").children("span").removeClass("dashicons-star-filled").addClass("dashicons-star-empty");
                                $(this).prevAll("a").children("span").removeClass("dashicons-star-empty").addClass("dashicons-star-filled");
                                $(this).children("span").removeClass("dashicons-star-empty").addClass("dashicons-star-filled");
                            }, function () {
                                var rating = $("input#rating").val();
                                if (rating) {
                                    var list = $(".rating-stars a");
                                    list.children("span").removeClass("dashicons-star-filled").addClass("dashicons-star-empty");
                                    list.slice(0, rating).children("span").removeClass("dashicons-star-empty").addClass("dashicons-star-filled");
                                }
                            }
                        );
                    });
                </script>
            </span>
		</div>
	</div>
	<?php
}

function dashboard_render_handle() {
	if ( ! $widget_options = get_option( 'wpgc_admin_dashboard_settings' ) ) {
		$widget_options = array();
	}

	$states = GestsupAPI::wpgc_get_state();

	?><h3><?php _e( 'Display these Tickets state:', 'wp-gestsup-connector' ); ?></h3>
	<?php
	foreach ( $states as $state ) {
		$option_name = 'wpgc_admin_dashboard_settings[' . $state['name'] . ']';
		$option      = get_option( $option_name );
		?>
		<p><input name="<?php echo $option_name; ?>]" type="checkbox"
		          value="<?php echo $state['name']; ?>" <?php checked( $option['state'], $state['name'] ); ?>>
			<?php echo $state['name'];
			?>
		</p>
		<?php
		# process update
		if ( isset( $_POST['wpgc_admin_dashboard_settings'] ) ) {
			$widget_options['state'] = $_POST['wpgc_admin_dashboard_settings'][ $state['name'] ];
			# save update

			$update = update_option( $option_name, $widget_options );
		}
		unset( $checked );
	}

	$noticket = get_option( 'wpgc_admin_dashboard_settings[notickets]' );
	?><h3><?php _e( 'Display state without tickets ?', 'wp-gestsup-connector' ); ?></h3>
	<p>
		<input name="wpgc_admin_dashboard_settings[notickets]" type="checkbox"
		       value="yes" <?php checked( $noticket['state'], 'yes' ); ?>> <?php _e( 'Yes', 'wp-gestsup-connector' ); ?>
	</p>
	<?php

	# process update
	if ( isset( $_POST['wpgc_admin_dashboard_settings'] ) ) {
		$widget_options['state'] = $_POST['wpgc_admin_dashboard_settings']['notickets'];
		# save update

		$update = update_option( 'wpgc_admin_dashboard_settings[notickets]', $widget_options );
	}

}
