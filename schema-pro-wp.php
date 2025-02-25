<?php
/**
 * Plugin Name: Schema Pro WP
 * Plugin URI: https://example.com/schema-pro-wp
 * Description: Ett plugin för att hantera schemaläggning och resurser i WordPress
 * Version: 1.0.0
 * Author: Johan
 * Author URI: https://example.com
 * Text Domain: schemaprowp
 * Domain Path: /languages
 *
 * @package SchemaProWP
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die;
}

// Enable debug mode for development
if (!defined('WP_DEBUG')) {
    define('WP_DEBUG', true);
}

/**
 * Current plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 */
define( 'SCHEMAPROWP_VERSION', '1.0.0' );
define( 'SCHEMAPROWP_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'SCHEMAPROWP_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'SCHEMAPROWP_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );

/**
 * Load the plugin core class.
 */
require_once SCHEMAPROWP_PLUGIN_DIR . 'includes/class-schema-pro-wp.php';

/**
 * Begins execution of the plugin.
 *
 * @since 1.0.0
 * @return void
 */
function schemapro_run() {
    $plugin = new SchemaProWP();
    $plugin->run();
}

// Initialize the plugin
add_action( 'plugins_loaded', 'schemapro_run' );