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
 */

// Om denna fil anropas direkt, avbryt.
if (!defined('WPINC')) {
    die;
}

// Definiera plugin-konstanter
define('SCHEMAPROWP_VERSION', '1.0.0');
define('SCHEMAPROWP_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('SCHEMAPROWP_PLUGIN_URL', plugin_dir_url(__FILE__));
define('SCHEMAPROWP_PLUGIN_BASENAME', plugin_basename(__FILE__));

// Debug: Skriv ut när vi börjar ladda basklasserna
error_log('SchemaProWP: Laddar basklasser...');

/**
 * Ladda nödvändiga klasser först.
 */
$required_files = array(
    // Basklasser
    'api/class-schema-pro-wp-rest-controller.php',
    'models/class-schema-pro-wp-model.php',
    
    // API Controllers
    'api/class-schema-pro-wp-resources-controller.php',
    'api/class-schema-pro-wp-bookings-controller.php',
    
    // Admin och Public
    'admin/class-schema-pro-wp-admin.php',
    'public/class-schema-pro-wp-public.php',
    
    // Models
    'models/class-schema-pro-wp-resource.php',
    'models/class-schema-pro-wp-booking.php'
);

foreach ($required_files as $file) {
    $file_path = SCHEMAPROWP_PLUGIN_DIR . 'includes/' . $file;
    if (file_exists($file_path)) {
        error_log('SchemaProWP: Laddar fil: ' . $file_path);
        require_once $file_path;
    } else {
        error_log('SchemaProWP: VARNING - Kunde inte hitta filen: ' . $file_path);
    }
}

/**
 * Autoloader för plugin-klasser.
 */
spl_autoload_register(function ($class_name) {
    // Debug: Skriv ut varje klass som försöker laddas
    error_log('SchemaProWP Autoloader: Försöker ladda klass: ' . $class_name);
    
    // Kontrollera om klassen börjar med vårt prefix
    if (strpos($class_name, 'SchemaProWP_') !== 0) {
        return;
    }

    // Ta bort prefixet för att få relativa klassnamnet
    $relative_class = substr($class_name, strlen('SchemaProWP_'));

    // Konvertera klassnamn till filsökväg
    $parts = explode('_', $relative_class);
    $last = array_pop($parts);
    $parts = array_map('strtolower', $parts);
    
    if (!empty($parts)) {
        $file_path = implode('/', $parts) . '/';
    } else {
        $file_path = '';
    }
    
    $file = SCHEMAPROWP_PLUGIN_DIR . 'includes/' . $file_path . 'class-schema-pro-wp-' . strtolower($last) . '.php';
    
    // Debug: Skriv ut sökvägen till filen
    error_log('SchemaProWP Autoloader: Söker fil: ' . $file);

    // Om filen existerar, inkludera den
    if (file_exists($file)) {
        error_log('SchemaProWP Autoloader: Laddar fil: ' . $file);
        require_once $file;
    } else {
        error_log('SchemaProWP Autoloader: VARNING - Kunde inte hitta filen: ' . $file);
    }
});

/**
 * Kör plugin-initieringen.
 */
function run_schema_pro_wp() {
    // Debug: Skriv ut när initieringen börjar
    error_log('SchemaProWP: Startar plugin-initiering...');
    
    // Initiera admin-delen
    add_action('init', function() {
        if (is_admin()) {
            $admin = new SchemaProWP_Admin();
            $admin->init();
        }
    });

    // Initiera frontend-delen
    add_action('init', function() {
        $public = new SchemaProWP_Public();
        $public->init();
    });

    // Initiera REST API controllers
    add_action('rest_api_init', function () {
        error_log('SchemaProWP: Initierar REST API controllers...');
        $resources_controller = new SchemaProWP_Resources_Controller();
        $resources_controller->register_routes();
    });
}

// Starta pluginen
add_action('plugins_loaded', 'run_schema_pro_wp');