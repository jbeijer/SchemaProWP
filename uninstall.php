<?php
/**
 * Fired when the plugin is uninstalled.
 *
 * @link       https://example.com
 * @since      1.0.0
 *
 * @package    SchemaProWP
 */

// If uninstall not called from WordPress, then exit.
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

// Access WordPress database object
global $wpdb;

// Define table names
$tables = array(
    $wpdb->prefix . 'schema_pro_wp_schemas',
    $wpdb->prefix . 'schema_pro_wp_settings'
);

// Drop tables
foreach ($tables as $table) {
    $wpdb->query("DROP TABLE IF EXISTS $table");
}

// Delete options
delete_option('schema_pro_wp_version');

// Clear scheduled hook
wp_clear_scheduled_hook('schema_pro_wp_daily_maintenance');

// Clear rewrite rules
flush_rewrite_rules();