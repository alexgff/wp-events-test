<?php
/**
 * @package WP Events Test.
 *
 * @since 1.0.0
 */
	
if ( ! class_exists('WP_Widget_Calendar') ) {
	//require_once ABSPATH . WPINC . '/widgets/class-wp-widget-calendar.php';
}

/**
 * Class WPEventsTestWidget.
 */
if ( ! class_exists('WPEventsTestWidget') ) :

	class WPEventsTestWidget extends WP_Widget {

		/**
		 * Constructor.
		 */
		public function __construct() {
			parent::__construct(
				'wp_events_test_widget',
				esc_html__( 'WP Events Test widget', 'wp-events-test' ),
				array(
					'description' => esc_html__( 'Events calendar', 'wp-events-test' )
				)
			);
		}

		/**
		 * Echo the widget content.
		 *
		 * @param array $args     Display arguments including before_title, after_title, before_widget, and after_widget.
		 * @param array $instance The settings for the particular instance of the widget
		 */
		public function widget( $args, $instance ) {

			echo $args['before_widget']; // WPCS: XSS ok.
			
			echo '<div class="wp-events-test-calendar_wrap">';
				$this->get_calendar();
			echo '</div>';
			
			echo $args['after_widget']; // WPCS: XSS ok.

		}

		/**
		 * Echo the settings update form.
		 *
		 * @param array $instance Current settings
		 *
		 * @return string
		 */
		public function form( $instance ) {
			?>
			<p><?php esc_html_e( 'You\'ll see calendar on front-end.', 'wp-events-test' ); ?></p><?php
			return;
		}

		/**
		 * Update a particular instance.
		 * This function should check that $new_instance is set correctly.
		 * The newly calculated value of $instance should be returned.
		 * If "false" is returned, the instance won't be saved/updated.
		 *
		 * @param array $new_instance New settings for this instance as input by the user via form()
		 * @param array $old_instance Old settings for this instance
		 *
		 * @return array Settings to save or bool false to cancel saving
		 */
		public function update( $new_instance, $old_instance ) {
			$instance          = array();
			$instance['type']  = ( ! empty( $new_instance['type'] ) ) ? $new_instance['type'] : '';
			$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? $new_instance['title'] : '';

			return $instance;
		}

		/**
		 * Display calendar with days that have posts as links.
		 *
		 * The calendar is cached, which will be retrieved, if it exists. If there are
		 * no posts for the month, then it will not be displayed.
		 *
		 * @since 1.0.0
		 *
		 * @global wpdb      $wpdb
		 * @global int       $m
		 * @global int       $monthnum
		 * @global int       $year
		 * @global WP_Locale $wp_locale
		 * @global array     $posts
		 *
		 * @param bool $initial Optional, default is true. Use initial calendar names.
		 * @param bool $echo    Optional, default is true. Set to false for return.
		 * @return string|void String when retrieving.
		 */
		function get_calendar( $initial = true, $echo = true ) {
			
			$calendar_output = WPEvents_Test_Functions::get_calendar( $initial, $echo );
			
			if ( $echo ) {
				echo $calendar_output;
				return;
			}
			
			return $calendar_output;
			
		}
			
	}
	
endif;

# --- EOF
