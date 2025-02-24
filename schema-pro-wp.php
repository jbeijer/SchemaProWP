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
 */
function activate_schema_pro_wp() {
    // Activation logic here
}

/**
 * The code that runs during plugin deactivation.
 */
function deactivate_schema_pro_wp() {
    // Deactivation logic here
}

register_activation_hook(__FILE__, 'activate_schema_pro_wp');
register_deactivation_hook(__FILE__, 'deactivate_schema_pro_wp');

/**
 * Börjar exekvering av pluginen.
 */
function run_schema_pro_wp() {
    // Ladda huvudklassen
    require_once plugin_dir_path(__FILE__) . 'includes/class-schema-pro-wp.php';
    
    // Skapa en instans av huvudklassen
    $plugin = new SchemaProWP();
    $plugin->run();
}

// Kör pluginen
run_schema_pro_wp();