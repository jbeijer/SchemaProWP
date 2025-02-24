<?php
/**
 * Databashantering för SchemaProWP.
 *
 * @link       https://example.com
 * @since      1.0.0
 *
 * @package    SchemaProWP
 * @subpackage SchemaProWP/includes
 */

class SchemaProWP_Database {

    /**
     * Skapa databastabeller för pluginet.
     *
     * @since    1.0.0
     */
    public static function create_tables() {
        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();

        // Organisationer
        $table_name = $wpdb->prefix . 'schemaprowp_organizations';
        $sql = "CREATE TABLE $table_name (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            name varchar(255) NOT NULL,
            parent_id mediumint(9),
            created_at datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NOT NULL,
            PRIMARY KEY  (id)
        ) $charset_collate;";

        // Resurser
        $table_name = $wpdb->prefix . 'schemaprowp_resources';
        $sql .= "CREATE TABLE $table_name (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            organization_id mediumint(9) NOT NULL,
            name varchar(255) NOT NULL,
            type varchar(50) NOT NULL,
            status varchar(50) NOT NULL,
            properties text,
            created_at datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NOT NULL,
            PRIMARY KEY  (id)
        ) $charset_collate;";

        // Bokningar
        $table_name = $wpdb->prefix . 'schemaprowp_bookings';
        $sql .= "CREATE TABLE $table_name (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            resource_id mediumint(9) NOT NULL,
            user_id bigint(20) UNSIGNED NOT NULL,
            start_time datetime NOT NULL,
            end_time datetime NOT NULL,
            status varchar(50) NOT NULL,
            comments text,
            created_at datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NOT NULL,
            PRIMARY KEY  (id)
        ) $charset_collate;";

        // Användarorganisationer
        $table_name = $wpdb->prefix . 'schemaprowp_user_organizations';
        $sql .= "CREATE TABLE $table_name (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            user_id bigint(20) UNSIGNED NOT NULL,
            organization_id mediumint(9) NOT NULL,
            role varchar(50) NOT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NOT NULL,
            PRIMARY KEY  (id),
            UNIQUE KEY user_org (user_id, organization_id)
        ) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }

    /**
     * Ta bort databastabeller för pluginet.
     *
     * @since    1.0.0
     */
    public static function drop_tables() {
        global $wpdb;
        $wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}schemaprowp_organizations");
        $wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}schemaprowp_resources");
        $wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}schemaprowp_bookings");
        $wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}schemaprowp_user_organizations");
    }
}