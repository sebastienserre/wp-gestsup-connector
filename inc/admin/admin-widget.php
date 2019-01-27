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
		'dashboard_render'
	);
}

function dashboard_render() {
	$states     = GestsupAPI::wpgc_get_state();
	$parameters = GestsupAPI::wpgc_get_parameters();
	?>
	<div class="wpgc-dashboard">
		<p><?php _e( 'Many Thanks for choosing WP GestSup Connector. You\'ll find below your tickets preview', 'wp-gestsup-connector' ); ?></p>
		<ul>
			<?php
			foreach ( $states as $state ) {
				$tickets = GestsupAPI::wpgc_get_ticket( intval( $state['id'] ) );
				$nb      = sizeof( $tickets );
				$url     = $parameters[0]['server_url'];
				if ( 0 == ! $nb ) {
					ob_start();
					?>
					<li class="label label label-sm arrowed arrowed-right arrowed-left <?php echo $state['display']; ?>">
						<?php echo $state['name'] ?>
						: <?php printf( _n( '%d  ticket', '%d tickets', $nb, 'wp-gestsup-connector' ), $nb ); ?>

					</li>
					<?php
					echo ob_get_clean();
				}
			}
			?>
		</ul>
		<a class="button button-primary togestsup"
		   href="<?php echo $url; ?>"><?php _e( 'Read Tickets on Gestsup', 'wp-gestsup-connector' ); ?></a>
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
