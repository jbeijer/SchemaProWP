<?php
/**
 * Fired during plugin activation
 *
 * @package    SchemaProWP
 * @subpackage SchemaProWP/includes
 * @since      1.0.0
 */

/**
 * Handles plugin activation tasks.
 *
 * This class defines all code necessary to run during the plugin's activation,
 * including database table creation and initial setup.
 *
 * @since      1.0.0
 * @package    SchemaProWP
 * @subpackage SchemaProWP/includes
 */
class SchemaProWP_Activator {

    /**
     * Create the necessary database tables and register post types
     *
     * @since  1.0.0
     * @return void
     */
    public static function activate() {
        self::create_database_tables();
        self::create_default_options();
        self::register_post_types();
        self::setup_roles_and_capabilities();
        flush_rewrite_rules();
    }

    /**
     * Create required database tables
     *
     * @since  1.0.0
     * @return void
     */
    private static function create_database_tables() {
        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();
        
        // Resources table
        $table_name = $wpdb->prefix . 'schemapro_resources';
        $sql = "CREATE TABLE IF NOT EXISTS $table_name (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            title varchar(255) NOT NULL,
            description text,
            type varchar(50) NOT NULL,
            status varchar(20) NOT NULL DEFAULT 'active',
            metadata longtext,
            created_by bigint(20) unsigned NOT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            KEY idx_{$table_name}_title (title),
            KEY idx_{$table_name}_type (type),
            KEY idx_{$table_name}_status (status),
            KEY idx_{$table_name}_created_by (created_by)
        ) $charset_collate;";

        // Bookings table
        $table_name = $wpdb->prefix . 'schemapro_bookings';
        $sql .= "CREATE TABLE IF NOT EXISTS $table_name (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            resource_id bigint(20) unsigned NOT NULL,
            user_id bigint(20) unsigned NOT NULL,
            start_time datetime NOT NULL,
            end_time datetime NOT NULL,
            status varchar(20) NOT NULL DEFAULT 'pending',
            notes text,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            KEY idx_{$table_name}_resource (resource_id),
            KEY idx_{$table_name}_user (user_id),
            KEY idx_{$table_name}_status (status),
            KEY idx_{$table_name}_timespan (start_time,end_time)
        ) $charset_collate;";

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        dbDelta( $sql );
    }

    /**
     * Create default plugin options
     *
     * @since  1.0.0
     * @return void
     */
    private static function create_default_options() {
        $defaults = array(
            'version' => SCHEMAPROWP_VERSION,
            'db_version' => '1.0.0',
            'initialized' => true,
            'booking_settings' => array(
                'min_booking_length' => 30, // minutes
                'max_booking_length' => 480, // minutes
                'advance_booking_days' => 30,
                'cancellation_period' => 24, // hours
            ),
            'notification_settings' => array(
                'email_notifications' => true,
                'admin_email' => get_option('admin_email'),
            ),
        );

        add_option( 'schemaprowp_settings', $defaults );
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
