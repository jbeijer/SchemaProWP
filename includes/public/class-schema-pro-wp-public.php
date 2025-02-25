<?php
/**
 * Hanterar frontend-funktionalitet för pluginen.
 */
class SchemaProWP_Public {
    /**
     * Initiera frontend-funktionalitet.
     */
    public function init() {
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
    }

    /**
     * Ladda frontend scripts och styles.
     */
    public function enqueue_scripts() {
        wp_enqueue_style(
            'schema-pro-wp-public',
            SCHEMA_PRO_WP_PLUGIN_URL . 'dist/public.css',
            array(),
            SCHEMA_PRO_WP_VERSION
        );

        wp_enqueue_script(
            'schema-pro-wp-public',
            SCHEMA_PRO_WP_PLUGIN_URL . 'dist/public.js',
            array('wp-api', 'wp-element'),
            SCHEMA_PRO_WP_VERSION,
            true
        );

        wp_localize_script('schema-pro-wp-public', 'schemaProWP', array(
            'nonce' => wp_create_nonce('wp_rest'),
            'apiUrl' => rest_url('schemaprowp/v1'),
            'postId' => get_the_ID(),
            'organizationId' => get_the_ID(),
        ));
    }
}
