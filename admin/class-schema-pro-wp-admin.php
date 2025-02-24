<?php
/**
 * Admin-specific functionality for the plugin.
 *
 * @package    SchemaProWP
 * @subpackage SchemaProWP/admin
 * @since      1.0.0
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and hooks for the admin area.
 *
 * @package    SchemaProWP
 * @subpackage SchemaProWP/admin
 */
class SchemaProWP_Admin {

    /**
     * The ID of this plugin.
     *
     * @since  1.0.0
     * @access private
     * @var    string
     */
    private $plugin_name;

    /**
     * The version of this plugin.
     *
     * @since  1.0.0
     * @access private
     * @var    string
     */
    private $version;

    /**
     * Initialize the class and set its properties.
     *
     * @since 1.0.0
     * @param string $plugin_name The name of this plugin.
     * @param string $version     The version of this plugin.
     */
    public function __construct( $plugin_name, $version ) {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
    }

    /**
     * Register all of the hooks related to the admin area functionality
     *
     * @since  1.0.0
     * @return void
     */
    public function init() {
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_styles' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
        add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );
        add_action( 'admin_init', array( $this, 'register_settings' ) );
    }

    /**
     * Register the stylesheets for the admin area.
     *
     * @since  1.0.0
     * @return void
     */
    public function enqueue_styles() {
        $screen = get_current_screen();
        if ( ! $this->is_plugin_page( $screen ) ) {
            return;
        }

        wp_enqueue_style(
            $this->plugin_name,
            SCHEMAPROWP_PLUGIN_URL . 'admin/css/schema-pro-wp-admin.css',
            array(),
            $this->version,
            'all'
        );
    }

    /**
     * Register the JavaScript for the admin area.
     *
     * @since  1.0.0
     * @return void
     */
    public function enqueue_scripts() {
        $screen = get_current_screen();
        if ( ! $this->is_plugin_page( $screen ) ) {
            return;
        }

        // Enqueue the main JavaScript bundle
        wp_enqueue_script(
            $this->plugin_name,
            SCHEMAPROWP_PLUGIN_URL . 'admin/dist/js/main.js',
            array(),
            $this->version,
            true
        );

        // Add WordPress data to the page
        wp_localize_script(
            $this->plugin_name,
            'schemaProWPAdmin',
            array(
                'apiRoot' => esc_url_raw( rest_url() ),
                'nonce' => wp_create_nonce( 'wp_rest' ),
                'currentUser' => get_current_user_id(),
                'pluginUrl' => SCHEMAPROWP_PLUGIN_URL,
            )
        );
    }

    /**
     * Add menu items to the admin area.
     *
     * @since  1.0.0
     * @return void
     */
    public function add_admin_menu() {
        add_menu_page(
            __( 'Schema Pro WP', 'schemaprowp' ),
            __( 'Schema Pro', 'schemaprowp' ),
            'manage_schemapro',
            'schema-pro-wp',
            array( $this, 'render_admin_page' ),
            'dashicons-calendar-alt',
            30
        );

        add_submenu_page(
            'schema-pro-wp',
            __( 'Resurser', 'schemaprowp' ),
            __( 'Resurser', 'schemaprowp' ),
            'manage_schemapro',
            'schema-pro-wp-resources',
            array( $this, 'render_resources_page' )
        );

        add_submenu_page(
            'schema-pro-wp',
            __( 'Inställningar', 'schemaprowp' ),
            __( 'Inställningar', 'schemaprowp' ),
            'manage_schemapro',
            'schema-pro-wp-settings',
            array( $this, 'render_settings_page' )
        );
    }

    /**
     * Register plugin settings
     *
     * @since  1.0.0
     * @return void
     */
    public function register_settings() {
        register_setting(
            'schemaprowp_settings',
            'schemaprowp_settings',
            array(
                'type' => 'array',
                'sanitize_callback' => array( $this, 'sanitize_settings' ),
            )
        );
    }

    /**
     * Sanitize settings before saving
     *
     * @since  1.0.0
     * @param  array $input The input array to sanitize.
     * @return array
     */
    public function sanitize_settings( $input ) {
        if ( ! is_array( $input ) ) {
            return array();
        }

        $output = array();

        // Booking settings
        if ( isset( $input['booking_settings'] ) ) {
            $output['booking_settings'] = array(
                'min_booking_length' => absint( $input['booking_settings']['min_booking_length'] ),
                'max_booking_length' => absint( $input['booking_settings']['max_booking_length'] ),
                'advance_booking_days' => absint( $input['booking_settings']['advance_booking_days'] ),
                'cancellation_period' => absint( $input['booking_settings']['cancellation_period'] ),
            );
        }

        // Notification settings
        if ( isset( $input['notification_settings'] ) ) {
            $output['notification_settings'] = array(
                'email_notifications' => (bool) $input['notification_settings']['email_notifications'],
                'admin_email' => sanitize_email( $input['notification_settings']['admin_email'] ),
            );
        }

        return $output;
    }

    /**
     * Check if current page is a plugin page
     *
     * @since  1.0.0
     * @param  WP_Screen $screen The current screen object.
     * @return bool
     */
    private function is_plugin_page( $screen ) {
        if ( ! $screen ) {
            return false;
        }

        $plugin_pages = array(
            'toplevel_page_schema-pro-wp',
            'schema-pro_page_schema-pro-wp-resources',
            'schema-pro_page_schema-pro-wp-settings',
        );

        return in_array( $screen->id, $plugin_pages, true );
    }

    /**
     * Render the main admin page.
     *
     * @since  1.0.0
     * @return void
     */
    public function render_admin_page() {
        if ( ! current_user_can( 'manage_schemapro' ) ) {
            wp_die( esc_html__( 'Du har inte behörighet att visa denna sida.', 'schemaprowp' ) );
        }

        require_once SCHEMAPROWP_PLUGIN_DIR . 'admin/partials/schema-pro-wp-admin-display.php';
    }

    /**
     * Render the resources page.
     *
     * @since  1.0.0
     * @return void
     */
    public function render_resources_page() {
        if ( ! current_user_can( 'manage_schemapro' ) ) {
            wp_die( esc_html__( 'Du har inte behörighet att visa denna sida.', 'schemaprowp' ) );
        }

        require_once SCHEMAPROWP_PLUGIN_DIR . 'admin/partials/schema-pro-wp-resources-display.php';
    }

    /**
     * Render the settings page.
     *
     * @since  1.0.0
     * @return void
     */
    public function render_settings_page() {
        if ( ! current_user_can( 'manage_schemapro' ) ) {
            wp_die( esc_html__( 'Du har inte behörighet att visa denna sida.', 'schemaprowp' ) );
        }

        require_once SCHEMAPROWP_PLUGIN_DIR . 'admin/partials/schema-pro-wp-settings-display.php';
    }
}