<?php
/**
 * Fired during plugin activation
 *
 * @link       https://example.com
 * @since      1.0.0
 *
 * @package    SchemaProWP
 * @subpackage SchemaProWP/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    SchemaProWP
 * @subpackage SchemaProWP/includes
 * @author     Your Name
 */
class Schema_Pro_WP_Activator {

    /**
     * Create the necessary database tables and register post types
     *
     * @since    1.0.0
     */
    public static function activate() {
        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();

        // Registrera custom post type för organisationer
        self::register_post_types();

        // Resources table - behåller egen tabell för specifik resurshantering
        $table_resources = $wpdb->prefix . 'schemaprowp_resources';
        $sql_resources = "CREATE TABLE IF NOT EXISTS $table_resources (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            post_id bigint(20) NOT NULL,  -- Koppling till organization som wp_posts
            name varchar(255) NOT NULL,
            type varchar(50) NOT NULL,
            status varchar(50) NOT NULL DEFAULT 'active',
            properties text,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            KEY post_id (post_id),
            KEY type (type),
            KEY status (status)
        ) $charset_collate;";

        // Bookings table - behåller egen tabell för bokningsspecifik data
        $table_bookings = $wpdb->prefix . 'schemaprowp_bookings';
        $sql_bookings = "CREATE TABLE IF NOT EXISTS $table_bookings (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            resource_id mediumint(9) NOT NULL,
            user_id bigint(20) NOT NULL,  -- Koppling till wp_users
            start_time datetime NOT NULL,
            end_time datetime NOT NULL,
            status varchar(50) NOT NULL DEFAULT 'pending',
            comments text,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            KEY resource_id (resource_id),
            KEY user_id (user_id),
            KEY status (status),
            KEY booking_time (start_time, end_time)
        ) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        
        // Skapa/uppdatera tabellerna
        dbDelta($sql_resources);
        dbDelta($sql_bookings);

        // Spara databasversionen
        add_option('schemaprowp_db_version', '1.0.0');

        // Skapa standardroller och capabilities
        self::setup_roles_and_capabilities();
    }

    /**
     * Registrera custom post types
     */
    private static function register_post_types() {
        // Registrera 'organization' post type
        register_post_type('schemaprowp_org', 
            array(
                'labels' => array(
                    'name' => __('Organizations', 'schema-pro-wp'),
                    'singular_name' => __('Organization', 'schema-pro-wp')
                ),
                'public' => true,
                'hierarchical' => true, // Tillåter parent/child relationer
                'supports' => array('title', 'editor', 'thumbnail', 'custom-fields'),
                'has_archive' => true,
                'show_in_rest' => true, // Aktivera Gutenberg/REST API support
                'menu_icon' => 'dashicons-groups',
                'capability_type' => 'organization',
                'map_meta_cap' => true
            )
        );

        // Spola om rewrite rules
        flush_rewrite_rules();
    }

    /**
     * Sätt upp roller och capabilities
     */
    private static function setup_roles_and_capabilities() {
        // Hämta admin-rollen
        $admin = get_role('administrator');

        // Lägg till capabilities för organizations
        $capabilities = array(
            'edit_organization',
            'read_organization',
            'delete_organization',
            'edit_organizations',
            'edit_others_organizations',
            'publish_organizations',
            'read_private_organizations',
            'delete_organizations',
            'delete_private_organizations',
            'delete_published_organizations',
            'delete_others_organizations',
            'edit_private_organizations',
            'edit_published_organizations',
            'manage_schema_bookings'
        );

        // Lägg till capabilities till admin-rollen
        foreach ($capabilities as $cap) {
            $admin->add_cap($cap);
        }

        // Skapa en ny roll för organisationsadministratörer
        add_role(
            'organization_admin',
            __('Organization Admin', 'schema-pro-wp'),
            array(
                'read' => true,
                'edit_organization' => true,
                'read_organization' => true,
                'delete_organization' => true,
                'edit_organizations' => true,
                'publish_organizations' => true,
                'manage_schema_bookings' => true
            )
        );
    }
}
