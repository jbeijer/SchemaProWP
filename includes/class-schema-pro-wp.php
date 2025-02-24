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
        if ( defined( 'SCHEMAPROWP_VERSION' ) ) {
            $this->version = SCHEMAPROWP_VERSION;
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
        // Lägg till den nya organizations-controllern
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/api/class-schema-pro-wp-organizations-controller.php';

        $this->loader = new SchemaProWP_Loader();
    }

    private function define_admin_hooks() {
        $this->loader->add_action( 'admin_enqueue_scripts', $this, 'enqueue_admin_scripts' );
        $this->loader->add_action( 'admin_menu', $this, 'add_plugin_admin_menu' );
    }

    private function define_public_hooks() {
        add_action('init', array($this, 'register_shortcodes'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_public_scripts'));
        add_action('init', array($this, 'register_post_types'));
        add_action('init', array($this, 'register_meta_fields'));
    }

    private function define_api_hooks() {
        $this->loader->add_action('rest_api_init', $this, 'register_rest_routes');
    }

    public function register_rest_routes() {
        $resources_controller = new SchemaProWP_Resources_Controller();
        $resources_controller->register_routes();

        $bookings_controller = new SchemaProWP_Bookings_Controller();
        $bookings_controller->register_routes();
        
        // Lägg till den nya controllern för organisationer
        $organizations_controller = new SchemaProWP_Organizations_Controller();
        $organizations_controller->register_routes();
    }

    public function enqueue_admin_scripts($hook) {
        // Only load on our plugin's admin page
        if (strpos($hook, 'schema-pro-wp') === false) {
            return;
        }

        wp_register_script(
            'schema-pro-wp-admin',
            SCHEMAPROWP_PLUGIN_URL . 'dist/admin.js',
            array(),
            $this->version,
            true
        );

        wp_localize_script(
            'schema-pro-wp-admin',
            'schemaProWPAdmin',
            array(
                'apiRoot' => esc_url_raw( rest_url() ),
                'nonce' => wp_create_nonce( 'wp_rest' ),
                'currentUser' => get_current_user_id(),
            )
        );

        wp_enqueue_script('schema-pro-wp-admin');
    }

    /**
     * Registrera och hantera shortcode för att visa bokningskalendern
     *
     * @since 1.0.0
     * @return void
     */
    public function register_shortcodes() {
        add_shortcode('schemaprowp', array($this, 'render_public_app'));
    }

    /**
     * Callback function for the [schemaprowp] shortcode
     * 
     * @param array $atts Shortcode attributes
     * @return string HTML output
     */
    public function render_public_app($atts = array()) {
        // Scripts and styles
        wp_enqueue_script('schemaprowp-public');
        wp_enqueue_style('schemaprowp-public');
        
        // Create attributes for data to pass to JS
        $atts = shortcode_atts(array(
            'view' => 'month',
            'organization' => '',
        ), $atts, 'schemaprowp');
        
        // Prepare data for JS
        $data = array(
            'view' => $atts['view'],
            'organization' => $atts['organization'],
            'restUrl' => rest_url('schemaprowp/v1'),
            'nonce' => wp_create_nonce('wp_rest')
        );
        
        // Make data available globally
        wp_localize_script(
            'schemaprowp-public',
            'schemaProWPData',
            $data
        );
        
        // Return HTML container
        return sprintf(
            '<div class="schemaprowp-calendar" data-wp-data="%s"></div>',
            esc_attr(json_encode($data))
        );
    }

    /**
     * Registrera skript och stilar för frontend
     *
     * @since 1.0.0
     * @return void
     */
    public function enqueue_public_scripts() {
        if (has_shortcode(get_the_content(), 'schemaprowp') || 
            is_active_widget(false, false, 'schemaprowp_widget')) {
            
            wp_enqueue_script(
                'schemaprowp-public',
                plugin_dir_url(dirname(__FILE__)) . 'dist/public.js',
                array(),
                $this->version,
                true
            );
            
            wp_enqueue_style(
                'schemaprowp-public',
                plugin_dir_url(dirname(__FILE__)) . 'dist/public.css',
                array(),
                $this->version
            );
            
            wp_localize_script(
                'schemaprowp-public',
                'schemaProWPData',
                array(
                    'restUrl' => rest_url('schemaprowp/v1'),
                    'nonce' => wp_create_nonce('wp_rest'),
                    'locale' => get_locale()
                )
            );
        }
    }

    public function register_post_types() {
        // Register organizations CPT
        register_post_type('schemapro_org',
            array(
                'labels' => array(
                    'name' => __('Organizations', 'schemaprowp'),
                    'singular_name' => __('Organization', 'schemaprowp')
                ),
                'public' => true,
                'show_in_rest' => true,
                'supports' => array('title', 'editor'),
                'rewrite' => array('slug' => 'organizations'),
                'capability_type' => 'post',
                'has_archive' => true
            )
        );
    }

    public function register_meta_fields() {
        // Contact info
        register_meta('post', 'organization_email', array(
            'show_in_rest' => true,
            'single' => true,
            'type' => 'string',
            'sanitize_callback' => 'sanitize_email',
            'auth_callback' => function() {
                return current_user_can('edit_posts');
            }
        ));

        register_meta('post', 'organization_phone', array(
            'show_in_rest' => true,
            'single' => true,
            'type' => 'string',
            'sanitize_callback' => 'sanitize_text_field',
            'auth_callback' => function() {
                return current_user_can('edit_posts');
            }
        ));

        // Website and logo
        register_meta('post', 'organization_website', array(
            'show_in_rest' => true,
            'single' => true,
            'type' => 'string',
            'sanitize_callback' => 'esc_url_raw',
            'auth_callback' => function() {
                return current_user_can('edit_posts');
            }
        ));

        register_meta('post', 'organization_logo', array(
            'show_in_rest' => true,
            'single' => true,
            'type' => 'string',
            'sanitize_callback' => 'esc_url_raw',
            'auth_callback' => function() {
                return current_user_can('edit_posts');
            }
        ));

        // Location data
        register_meta('post', 'organization_location', array(
            'show_in_rest' => array(
                'schema' => array(
                    'type' => 'object',
                    'properties' => array(
                        'streetAddress' => array('type' => 'string'),
                        'city' => array('type' => 'string'),
                        'postalCode' => array('type' => 'string'),
                        'country' => array('type' => 'string')
                    )
                )
            ),
            'single' => true,
            'type' => 'object',
            'sanitize_callback' => function($value) {
                return array_map('sanitize_text_field', $value);
            },
            'auth_callback' => function() {
                return current_user_can('edit_posts');
            }
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
        require_once SCHEMAPROWP_PLUGIN_DIR . 'admin/partials/schema-pro-wp-admin-display.php';
    }

    public function run() {
        $this->loader->run();
    }
}
