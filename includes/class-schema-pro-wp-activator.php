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
        global $wpdb;

        $charset_collate = $wpdb->get_charset_collate();

        // Create organizations table
        $table_name = $wpdb->prefix . 'schemapro_organizations';
        $sql = "CREATE TABLE $table_name (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            name varchar(255) NOT NULL,
            parent_id mediumint(9) DEFAULT NULL,
            PRIMARY KEY (id)
        ) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);

        self::create_database_tables();
        self::create_default_options();
        self::register_post_types();
        self::setup_roles_and_capabilities();
        flush_rewrite_rules();
    }

    /**
     * Insert test data for development
     *
     * @since  1.0.0
     * @return void
     */
    public static function insert_test_data() {
        global $wpdb;
        
        // Only insert test data if we're in a development environment
        if (!defined('WP_DEBUG') || !WP_DEBUG) {
            return;
        }
        
        $resources_table = $wpdb->prefix . 'schemaprowp_resources';
        
        // Check if we already have test data
        $count = $wpdb->get_var("SELECT COUNT(*) FROM {$resources_table}");
        if ($count > 0) {
            return;
        }
        
        // Insert test resources
        $test_resources = array(
            array(
                'title' => 'Konferensrum A',
                'description' => 'Ett stort konferensrum med plats för 20 personer',
                'type' => 'room',
                'status' => 'active',
                'created_by' => get_current_user_id(),
                'created_at' => current_time('mysql'),
                'updated_at' => current_time('mysql')
            ),
            array(
                'title' => 'Projektor HD-1080',
                'description' => 'Högkvalitativ projektor för presentationer',
                'type' => 'equipment',
                'status' => 'active',
                'created_by' => get_current_user_id(),
                'created_at' => current_time('mysql'),
                'updated_at' => current_time('mysql')
            ),
            array(
                'title' => 'Tjänstebil VW ID.4',
                'description' => 'Elektrisk tjänstebil för företagsresor',
                'type' => 'vehicle',
                'status' => 'active',
                'created_by' => get_current_user_id(),
                'created_at' => current_time('mysql'),
                'updated_at' => current_time('mysql')
            )
        );
        
        foreach ($test_resources as $resource) {
            $wpdb->insert(
                $resources_table,
                $resource,
                array(
                    '%s', // title
                    '%s', // description
                    '%s', // type
                    '%s', // status
                    '%d', // created_by
                    '%s', // created_at
                    '%s'  // updated_at
                )
            );
        }
    }

    /**
     * Create required database tables
     *
     * @since  1.0.0
     * @return void
     */
    public static function create_database_tables() {
        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();
        
        $table_name = $wpdb->prefix . 'schemaprowp_resources';
        $sql = "CREATE TABLE IF NOT EXISTS $table_name (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            title varchar(255) NOT NULL,
            description text,
            type varchar(50) NOT NULL DEFAULT 'room',
            status varchar(20) NOT NULL DEFAULT 'active',
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            KEY idx_type (type),
            KEY idx_status (status)
        ) $charset_collate;";

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        dbDelta($sql);

        // Log any database errors
        if (!empty($wpdb->last_error)) {
            error_log('SchemaProWP: Database table creation error: ' . $wpdb->last_error);
            return;
        }

        // Ensure some test data exists
        $count = $wpdb->get_var("SELECT COUNT(*) FROM $table_name");
        if ($count == 0) {
            $test_resources = [
                [
                    'title' => 'Konferensrum A',
                    'description' => 'Stort konferensrum för upp till 20 personer',
                    'type' => 'room',
                    'status' => 'active'
                ],
                [
                    'title' => 'Projektor HD',
                    'description' => 'Högkvalitativ projektor för presentationer',
                    'type' => 'equipment',
                    'status' => 'active'
                ],
                [
                    'title' => 'Tjänstebil Tesla Model 3',
                    'description' => 'Elektrisk tjänstebil för företagsresor',
                    'type' => 'vehicle',
                    'status' => 'active'
                ]
            ];

            foreach ($test_resources as $resource) {
                $wpdb->insert(
                    $table_name, 
                    array_map('sanitize_text_field', $resource),
                    ['%s', '%s', '%s', '%s']
                );
                
                if (!empty($wpdb->last_error)) {
                    error_log('SchemaProWP: Error inserting test data: ' . $wpdb->last_error);
                }
            }
        }
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
