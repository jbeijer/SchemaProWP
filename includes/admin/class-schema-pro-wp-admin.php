<?php
/**
 * Hanterar admin-funktionalitet för pluginen.
 */
class SchemaProWP_Admin {
    /**
     * Initiera admin-funktionalitet.
     */
    public function init() {
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));
    }

    /**
     * Lägg till admin-meny.
     */
    public function add_admin_menu() {
        add_menu_page(
            __('Schema Pro WP', 'schemaprowp'),
            __('Schema Pro WP', 'schemaprowp'),
            'manage_options',
            'schema-pro-wp',
            array($this, 'render_admin_page'),
            'dashicons-calendar-alt',
            30
        );
    }

    /**
     * Ladda admin scripts och styles.
     *
     * @param string $hook_suffix Den aktuella admin-sidans hook suffix.
     */
    public function enqueue_admin_scripts($hook_suffix) {
        if ('toplevel_page_schema-pro-wp' !== $hook_suffix) {
            return;
        }

        wp_enqueue_style(
            'schema-pro-wp-admin',
            SCHEMAPROWP_PLUGIN_URL . 'assets/css/admin.css',
            array(),
            SCHEMAPROWP_VERSION
        );

        wp_enqueue_script(
            'schema-pro-wp-admin',
            SCHEMAPROWP_PLUGIN_URL . 'assets/js/admin.js',
            array('wp-api', 'wp-element', 'wp-components'),
            SCHEMAPROWP_VERSION,
            true
        );

        wp_localize_script('schema-pro-wp-admin', 'schemaProWPAdmin', array(
            'nonce' => wp_create_nonce('wp_rest'),
            'apiUrl' => rest_url('schemaprowp/v1'),
        ));
    }

    /**
     * Rendera admin-sidan.
     */
    public function render_admin_page() {
        ?>
        <div class="wrap">
            <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
            <div id="schema-pro-wp-admin-app"></div>
        </div>
        <?php
    }
}
