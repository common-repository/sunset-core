<?php
/**
 * Plugin Name: Sunset Core
 * Description: This plugin was made for the Sunset Theme and adds extra functionality to it.
 * Author URI:  https://bogdan.kyiv.ua
 * Author:      Bogdan Bendziukov
 * Version:     1.0
 *
 * Text Domain: sunset-core
 * Domain Path: /languages
 *
 * License:     GNU GPL v3
 * License URI: https://www.gnu.org/licenses/gpl-3.0.html
 *
 * Network:     true
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'plugins_loaded', 'sunset_core_load_textdomain' );
/**
 * Load plugin textdomain
 */
function sunset_core_load_textdomain() {
	load_plugin_textdomain( 'sunset-core', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
}

/**
 * Block Initializer.
 */
require_once plugin_dir_path( __FILE__ ) . 'src/init.php';
