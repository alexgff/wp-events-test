<?php
/**
 * Plugin Name: WP Events Test
 * Plugin URI: https://github.com/alexgff/wp-events-test
 * Description: WP Events Test plugin.
 * Text Domain: wp-events-test
 * Domain Path: /languages/
 * Version: 1.0.0
 * Author: Alex Gor
 * Author URI: https://github.com/alexgff
 * Network: false
 * Credits: Alex Gor (alexgff).
 * Copyright 2019 Alex Gor.
 * License: GPL-3.0-or-later
 * License URI: https://spdx.org/licenses/GPL-3.0-or-later.html
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License, version 3, as
 * published by the Free Software Foundation.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'WP_EVENTS_TEST', '1.0.0' );

require_once dirname( __FILE__ ) . '/includes/class-wp-events-test-asset.php';
require_once dirname( __FILE__ ) . '/includes/class-wp-events-test-functions.php';
require_once dirname( __FILE__ ) . '/includes/class-wp-events-test-widget.php';
require_once dirname( __FILE__ ) . '/includes/class-wp-events-test.php';

/**
 * Initialize
 */
// @codingStandardsIgnoreStart
WPEventsTest::$PLUGIN_DIR_PATH = plugin_dir_path( __FILE__ );
WPEventsTest::$PLUGIN_DIR_URL  = plugin_dir_url( __FILE__ );
// @codingStandardsIgnoreEnd

new WPEventsTest();

# --- EOF