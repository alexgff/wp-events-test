<?php
/**
 * @package WP Events Test.
 *
 * @since 1.0.0
 */

if ( ! class_exists( 'WPEventsTest' ) ) :

	/**
	 * Class WPEventsTest.
	 */
	class WPEventsTest {

		/**
		 * Initialized at plugin loader
		 *
		 * @var string
		 */
		public static $PLUGIN_DIR_PATH = '';

		/**
		 * Initialized at plugin loader
		 *
		 * @var string
		 */
		public static $PLUGIN_DIR_URL = '';
		
		/**
		 * Event date meta key.
		 *
		 * @var string
		 */
		public $event_date_field = 'wp_events__event_date';

		/** 
		 * Constructor.
		 */
		public function __construct() {

			add_action( 'init', array( $this, 'on__register_cpt' ) );

			add_action( 'widgets_init', array( $this, 'on__register_widget' ) );
	
			add_action( 'wp_ajax_' . __CLASS__ . '_process_ajax', array( $this, 'on__process_ajax' ) );
			add_action( 'wp_ajax_nopriv_' . __CLASS__ . '_process_ajax', array( $this, 'on__process_ajax' ) );

			if ( is_admin() ) {

				add_action( 'add_meta_boxes', array( $this, 'on__register_meta_box' ) );
				
				add_action( 'save_post', array( $this, 'on__save_post' ) );
				
				add_action( 'admin_enqueue_scripts', array( $this, 'on__admin_script' ) ); 

			} else {
				
				/**
				 * Front-end.
				 */
				add_action( 'wp_print_styles', array( $this, 'on__front_end_styles' ) );

				add_action( 'wp_print_scripts', array( $this, 'on__front_end_script' ) ); 

			}
		}

		/**
		 * Register a meta box.
		 *
		 * @since 1.0.0
		 */					
		public function on__register_meta_box() {
			add_meta_box( 'wp_events_test_metabox', esc_html__( 'Additional options', 'wp-events-test' ), array( $this, 'on__meta_box_callback' ) );
		}
		
		/**
		 * Meta box callback.
		 *
		 * @since 1.0.0
		 */
		public function on__meta_box_callback( $post ) {
			
			$event_date = get_post_meta( $post->ID, $this->event_date_field, true );
			?>
			Event date:
			<input type="text" readonly="readonly" name="<?php echo $this->event_date_field; ?>" id="<?php echo $this->event_date_field; ?>" value="<?php echo $event_date; ?>" size="15" />
			<?php
			wp_nonce_field( 'wp_events_test', 'wp_events_test_nonce' );
		}

		/**
		 * Save metas.
		 *
		 * @since 1.0.0
		 */		
		public function on__save_post( $post_id ) {

			global $post;

			if ( empty($post) ) {
				return;
			}

			if ( ! isset( $_POST['wp_events_test_nonce'] ) || ! wp_verify_nonce( $_POST['wp_events_test_nonce'], 'wp_events_test' ) ) {
				print 'Sorry, your nonce did not verify.';
				exit;
			}
			
			error_log(print_r('HERE: '.$_POST[$this->event_date_field], true));

			
			if ( isset( $_POST[$this->event_date_field] ) ) {
				update_post_meta( $post_id, $this->event_date_field, $_POST[$this->event_date_field]  );
			}
			
		}
		
		/**
		 * Register a `event` post type.
		 *
		 * @since 1.0.0
		 */
		public function on__register_cpt() {
			
			$labels = array(
				'name'               => esc_html__( 'Events', 'wp-events-test' ),
				'singular_name'      => esc_html__( 'Event', 'wp-events-test' ),
				'menu_name'          => esc_html__( 'Events', 'wp-events-test' ),
				'name_admin_bar'     => esc_html__( 'Event', 'wp-events-test' ),
				'add_new'            => esc_html__( 'Add New', 'wp-events-test' ),
				'add_new_item'       => esc_html__( 'Add New Event', 'wp-events-test' ),
				'new_item'           => esc_html__( 'New Event', 'wp-events-test' ),
				'edit_item'          => esc_html__( 'Edit Event', 'wp-events-test' ),
				'view_item'          => esc_html__( 'View Event', 'wp-events-test' ),
				'all_items'          => esc_html__( 'All Events', 'wp-events-test' ),
				'search_items'       => esc_html__( 'Search Events', 'wp-events-test' ),
				'parent_item_colon'  => esc_html__( 'Parent Events:', 'wp-events-test' ),
				'not_found'          => esc_html__( 'No Events found.', 'wp-events-test' ),
				'not_found_in_trash' => esc_html__( 'No Events found in Trash.', 'wp-events-test' )
			);

			$args = array(
				'labels'             => $labels,
				'description'        => esc_html__( 'Description.', 'wp-events-test' ),
				'public'             => true,
				'publicly_queryable' => true,
				'show_ui'            => true,
				'show_in_menu'       => true,
				'query_var'          => true,
				'rewrite'            => array( 'slug' => 'event' ),
				'capability_type'    => 'post',
				'taxonomies'    	 => array( 'post_tag' ),
				'has_archive'        => true,
				'hierarchical'       => false,
				'menu_position'      => null,
				'supports'           => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments' )
			);

			register_post_type( 'event', $args );
		}
		
		/**
		 * Register the widget.
		 *
		 * @since 1.0.0
		 */
		public function on__register_widget() {
			register_widget( 'WPEventsTestWidget' );
		}

		/**
		 * Enqueue script in admin.
		 *
		 * @since 1.0.0
		 */		
		public function on__admin_script() {
			
			wp_register_style(
				'jquery-ui',
				'https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css',
				false,
				WP_EVENTS_TEST
			);
			wp_enqueue_style( 'jquery-ui' );
			
			wp_register_script(
				'wp-events-test-admin',
				WPEvents_Test_Asset::url_js( 'wp-events-test-admin' ),
				array( 'jquery', 'jquery-ui-datepicker' ),
				WP_EVENTS_TEST,
				true
			);
			wp_enqueue_script( 'wp-events-test-admin' );
			wp_localize_script(
				'wp-events-test-admin',
				'WPEventsTestAdmin',
				array(
					'version' => WP_EVENTS_TEST,
					'datePicker' => $this->event_date_field
				)
			);	
		}
		
		/**
		 * Enqueue script on front.
		 *
		 * @since 1.0.0
		 */	
		public function on__front_end_script() {
			
			wp_register_script(
				'wp-events-test',
				WPEvents_Test_Asset::url_js( 'wp-events-test' ),
				array( 'jquery' ),
				WP_EVENTS_TEST,
				true
			);
			wp_enqueue_script( 'wp-events-test' );
			wp_localize_script(
				'wp-events-test',
				'WPEventsTest',
				array(
					'version' 	 	=> WP_EVENTS_TEST,
					'ajaxurl' 		=> admin_url( 'admin-ajax.php' ),
					'parentClass' 	=> __CLASS__,
					'process_ajax' 	=> __CLASS__ . '_process_ajax',
				)
			);			
		}
		
		/**
		 * Enqueue styles on front.
		 *
		 * @since 1.0.0
		 */
		public function on__front_end_styles() {
			
			wp_register_style(
				'wp-events-test',
				WPEvents_Test_Asset::url_css( 'wp-events-test' ),
				array(),
				WP_EVENTS_TEST,
				'all'
			);
			wp_enqueue_style( 'wp-events-test' );
		
		}
		
		/**
		 * Handle ajax process.
		 *
		 * @since 1.0.0
		 */
		function on__process_ajax() {

			$response = array();

			$order = $_POST['order'];
			
			if ( $order['action'] == 'get-calendar' ) {
				
				$calendar = WPEvents_Test_Functions::get_calendar(true, false, $order['get-month']);

				$response = array( 
					'order' 	=> $_POST['order'], 
					'success' 	=> true, 
					'calendar'	=> $calendar
				);
				wp_send_json_success( $response );
			}
			
			die();	
		}
	}
	
endif;

# --- EOF