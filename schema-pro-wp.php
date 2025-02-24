<?php
/**
 * Plugin Name: SchemaProWP
 * Plugin URI: https://example.com/schema-pro-wp
 * Description: Advanced WordPress plugin for scheduling personnel and resources.
 * Version: 1.0.0
 * Author: Your Name
 * Author URI: https://example.com
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: schema-pro-wp
 * Domain Path: /languages
 *
 * @package SchemaProWP
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

// Define plugin constants
define('SCHEMA_PRO_WP_VERSION', '1.0.0');
define('SCHEMA_PRO_WP_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('SCHEMA_PRO_WP_PLUGIN_URL', plugin_dir_url(__FILE__));

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-schema-pro-wp-activator.php
 */
function activate_schema_pro_wp() {
    require_once SCHEMA_PRO_WP_PLUGIN_DIR . 'includes/class-schema-pro-wp-activator.php';
    Schema_Pro_WP_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-schema-pro-wp-deactivator.php
 */
function deactivate_schema_pro_wp() {
    require_once SCHEMA_PRO_WP_PLUGIN_DIR . 'includes/class-schema-pro-wp-deactivator.php';
    Schema_Pro_WP_Deactivator::deactivate();
}

register_activation_hook(__FILE__, 'activate_schema_pro_wp');
register_deactivation_hook(__FILE__, 'deactivate_schema_pro_wp');

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require SCHEMA_PRO_WP_PLUGIN_DIR . 'includes/class-schema-pro-wp.php';

/**
 * Begins execution of the plugin.
 *
 * @since    1.0.0
 */
function run_schema_pro_wp() {
    $plugin = new SchemaProWP();
    $plugin->run();
}

// Kör pluginen
run_schema_pro_wp();