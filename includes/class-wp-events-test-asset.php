<?php
/**
 * Assets.
 *
 * @package WP Events Test.
 */

/**
 * Asset management.
 */
if ( ! class_exists('WPEvents_Test_Asset') ) : 

	class WPEvents_Test_Asset {

		protected static $_SCRIPT_SUFFIX = '';
		
		/**
		 * @return string
		 */
		public static function SCRIPT_SUFFIX() {
			return self::$_SCRIPT_SUFFIX;
		}
		
		/**
		 * URL to the JS script.
		 *
		 * @param string $script_name [optional] The script name without extension.
		 *                            If not passed, will return the JS root URL.
		 *
		 * @return string The URL.
		 * @since 1.0.0
		 */
		public static function url_js( $script_name = '' ) {
			$url = WPEventsTest::$PLUGIN_DIR_URL . 'assets/js';
			if ( $script_name ) {
				$url .= '/' . $script_name . self::SCRIPT_SUFFIX() . '.js';
			}

			return $url;
		}

		/**
		 * URL to the CSS sheet.
		 *
		 * @param string $sheet_name  [optional] The script name without extension.
		 *                            If not passed, will return the CSS root URL.
		 *
		 * @return string The URL.
		 * @since 1.0.0
		 */
		public static function url_css( $sheet_name = '' ) {
			$url = WPEventsTest::$PLUGIN_DIR_URL . 'assets/css';
			if ( $sheet_name ) {
				$url .= '/' . $sheet_name . '.css';
			}

			return $url;
		}
	}

endif;

# --- EOF