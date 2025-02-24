<?php
/**
 * Admin-specifik funktionalitet för pluginet.
 *
 * @link       https://example.com
 * @since      1.0.0
 *
 * @package    SchemaProWP
 * @subpackage SchemaProWP/admin
 */

/**
 * Admin-specifik funktionalitet för pluginet.
 *
 * Definiera plugin-namn, version och två exempel-krokar för hur man
 * enqueue:ar den admin-specifika stylesheet:en och JavaScript:et.
 *
 * @package    SchemaProWP
 * @subpackage SchemaProWP/admin
 * @author     Ditt Namn <din@email.com>
 */
class SchemaProWP_Admin {

    /**
     * Plugin-namnet.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $plugin_name    Plugin-namnet.
     */
    private $plugin_name;

    /**
     * Version av pluginet.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $version    Den aktuella versionen av pluginet.
     */
    private $version;

    /**
     * Initiera klassen och sätt dess egenskaper.
     *
     * @since    1.0.0
     * @param    string    $plugin_name       Plugin-namnet.
     * @param    string    $version    Versionen av pluginet.
     */
    public function __construct( $plugin_name, $version ) {

        $this->plugin_name = $plugin_name;
        $this->version = $version;

        add_action('admin_menu', array($this, 'add_plugin_admin_menu'));

    }

    /**
     * Registrera menyalternativ för admin-panelen
     */
    public function add_plugin_admin_menu() {
        add_menu_page(
            'SchemaProWP', 
            'SchemaProWP', 
            'manage_options', 
            'schemaprowp', 
            array($this, 'display_plugin_admin_page'), 
            'dashicons-calendar-alt', 
            30
        );
    }

    /**
     * Visa admin-sidan för pluginet
     */
    public function display_plugin_admin_page() {
        echo '<div id="schemaprowp-app" data-is-admin="true"></div>';
    }

    /**
     * Registrera stylesheet:en för admin-området.
     *
     * @since    1.0.0
     */
    public function enqueue_styles() {
        wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'dist/assets/index.css', array(), $this->version, 'all' );
    }

    /**
     * Registrera JavaScript för admin-området.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts() {
        wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'dist/js/main.js', array(), $this->version, false );
    }

}