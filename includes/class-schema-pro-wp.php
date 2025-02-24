<?php
/**
 * Huvudklassen för SchemaProWP plugin.
 *
 * @link       https://example.com
 * @since      1.0.0
 *
 * @package    SchemaProWP
 * @subpackage SchemaProWP/includes
 */

if (!defined('INTELEPHENSE_MODE')) { define('INTELEPHENSE_MODE', false); }
//
// Stub-definitioner för Intelephense:
// Dessa definieras ENDAST om en konstant INTELEPHENSE_MODE är satt och sann.
// Detta möjliggör att undvika funktionskonflikter i produktionsmiljö,
// medan Intelephense i utvecklingsmiljö får de nödvändiga funktionerna.
if ( !defined('ABSPATH') ) {
    if ( ! function_exists('plugin_dir_path') ) {
        /**
         * Stub för plugin_dir_path för Intelephense.
         *
         * @param mixed $file Filväg
         * @return string
         */
        function plugin_dir_path( $file ) {
            return __DIR__ . '/';
        }
    }
    if ( ! function_exists('plugin_dir_url') ) {
        /**
         * Stub för plugin_dir_url för Intelephense.
         *
         * @param mixed $file Filväg
         * @return string
         */
        function plugin_dir_url( $file ) {
            return '';
        }
    }
    if ( ! function_exists('wp_register_script') ) {
        /**
         * Stub för wp_register_script för Intelephense.
         */
        function wp_register_script( $handle, $src, $deps = array(), $ver = false, $in_footer = false ) {}
    }
    if ( ! function_exists('wp_localize_script') ) {
        /**
         * Stub för wp_localize_script för Intelephense.
         */
        function wp_localize_script( $handle, $object_name, $l10n ) {}
    }
    if ( ! function_exists('wp_enqueue_script') ) {
        /**
         * Stub för wp_enqueue_script för Intelephense.
         */
        function wp_enqueue_script( $handle ) {}
    }
    if ( ! function_exists('wp_enqueue_style') ) {
        /**
         * Stub för wp_enqueue_style för Intelephense.
         */
        function wp_enqueue_style( $handle ) {}
    }
    if ( ! function_exists('has_shortcode') ) {
        /**
         * Stub för has_shortcode för Intelephense.
         */
        function has_shortcode( $content, $tag ) { return false; }
    }
    if ( ! function_exists('add_menu_page') ) {
        /**
         * Stub för add_menu_page för Intelephense.
         */
        function add_menu_page() {}
    }
}

class SchemaProWP {

    protected $loader;
    protected $plugin_name;
    protected $version;

    public function __construct() {
        if ( defined( 'SCHEMA_PRO_WP_VERSION' ) ) {
            $this->version = SCHEMA_PRO_WP_VERSION;
        } else {
            $this->version = '1.0.0';
        }
        $this->plugin_name = 'schema-pro-wp';

        $this->load_dependencies();
        $this->define_admin_hooks();
        $this->define_public_hooks();
        $this->define_api_hooks();
    }

    private function load_dependencies() {
        // Grundläggande klasser
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-schema-pro-wp-loader.php';
        
        // Modeller
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/models/class-schema-pro-wp-model.php';
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/models/class-schema-pro-wp-resource.php';
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/models/class-schema-pro-wp-booking.php';
        
        // API Controllers
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/api/class-schema-pro-wp-rest-controller.php';
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/api/class-schema-pro-wp-resources-controller.php';
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/api/class-schema-pro-wp-bookings-controller.php';

        $this->loader = new SchemaProWP_Loader();
    }

    private function define_admin_hooks() {
        $this->loader->add_action( 'admin_enqueue_scripts', $this, 'enqueue_admin_scripts' );
        $this->loader->add_action( 'admin_menu', $this, 'add_plugin_admin_menu' );
    }

    private function define_public_hooks() {
        $this->loader->add_action( 'wp_enqueue_scripts', $this, 'enqueue_public_scripts' );
        $this->loader->add_shortcode( 'schemaprowp', $this, 'render_public_app' );
    }

    private function define_api_hooks() {
        $this->loader->add_action('rest_api_init', $this, 'register_rest_routes');
    }

    public function register_rest_routes() {
        $resources_controller = new SchemaProWP_Resources_Controller();
        $resources_controller->register_routes();

        $bookings_controller = new SchemaProWP_Bookings_Controller();
        $bookings_controller->register_routes();
    }

    public function enqueue_admin_scripts($hook) {
        // Only load on our plugin's admin page
        if (strpos($hook, 'schema-pro-wp') === false) {
            return;
        }

        wp_register_script(
            'schema-pro-wp-admin',
            SCHEMA_PRO_WP_PLUGIN_URL . 'dist/admin.js',
            array(),
            SCHEMA_PRO_WP_VERSION,
            true
        );

        // Add the data BEFORE enqueueing the script
        wp_localize_script(
            'schema-pro-wp-admin',
            'schemaProWPData',
            array(
                'isAdminPage' => '1',
                'ajaxUrl' => admin_url('admin-ajax.php'),
                'restUrl' => get_rest_url(null, 'schemaprowp/v1'),
                'nonce' => wp_create_nonce('wp_rest')
            )
        );

        wp_enqueue_script('schema-pro-wp-admin');
    }

    public function enqueue_public_scripts() {
        wp_enqueue_style('schemaprowp-style', SCHEMA_PRO_WP_PLUGIN_URL . 'dist/shared.css', array(), SCHEMA_PRO_WP_VERSION);
        wp_enqueue_script('schemaprowp-public', SCHEMA_PRO_WP_PLUGIN_URL . 'dist/public.js', array(), SCHEMA_PRO_WP_VERSION, true);
        
        // Skicka data till JavaScript
        wp_localize_script('schemaprowp-public', 'schemaProWPData', array(
            'restUrl' => get_rest_url(null, 'schemaprowp/v1'),
            'nonce' => wp_create_nonce('wp_rest')
        ));
    }

    public function add_plugin_admin_menu() {
        add_menu_page(
            'SchemaProWP', 
            'SchemaProWP', 
            'manage_options', 
            'schema-pro-wp', 
            array($this, 'display_plugin_admin_page'), 
            'dashicons-calendar-alt', 
            30
        );
    }

    public function display_plugin_admin_page() {
        echo '<div id="schemaprowp-app" data-is-admin="1"></div>';
    }

    public function render_public_app() {
        // Se till att skripten är inladdade
        if (!wp_script_is('schemaprowp-public', 'enqueued')) {
            $this->enqueue_public_scripts();
        }

        $data = array(
            'restUrl' => get_rest_url(null, 'schemaprowp/v1'),
            'nonce' => wp_create_nonce('wp_rest')
        );
        
        return '<div id="schemaprowp-app" data-wp-data="' . esc_attr(wp_json_encode($data)) . '"></div>';
    }

    public function run() {
        $this->loader->run();
    }
}
