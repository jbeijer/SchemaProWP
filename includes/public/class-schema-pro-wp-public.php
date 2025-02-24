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
        add_shortcode('schema_calendar', array($this, 'render_calendar_shortcode'));
        add_shortcode('schema_app', array($this, 'render_app_shortcode'));
    }

    /**
     * Ladda frontend scripts och styles.
     */
    public function enqueue_scripts() {
        wp_enqueue_style(
            'schema-pro-wp-public',
            SCHEMAPROWP_PLUGIN_URL . 'dist/public.css',
            array(),
            SCHEMAPROWP_VERSION
        );

        wp_enqueue_script(
            'schema-pro-wp-public',
            SCHEMAPROWP_PLUGIN_URL . 'dist/public.js',
            array('wp-api', 'wp-element'),
            SCHEMAPROWP_VERSION,
            true
        );

        wp_localize_script('schema-pro-wp-public', 'schemaProWP', array(
            'nonce' => wp_create_nonce('wp_rest'),
            'apiUrl' => rest_url('schemaprowp/v1'),
        ));
    }

    /**
     * Rendera kalender via shortcode.
     *
     * @param array $atts Shortcode attribut.
     * @return string HTML för kalendern.
     */
    public function render_calendar_shortcode($atts) {
        $atts = shortcode_atts(array(
            'id' => '',
            'view' => 'month',
        ), $atts, 'schema_calendar');

        ob_start();
        ?>
        <div class="schema-pro-wp-calendar" 
             data-calendar-id="<?php echo esc_attr($atts['id']); ?>"
             data-view="<?php echo esc_attr($atts['view']); ?>">
        </div>
        <?php
        return ob_get_clean();
    }

    /**
     * Rendera app via shortcode.
     *
     * @return string HTML för appen.
     */
    public function render_app_shortcode() {
        ob_start();
        ?>
        <div id="schemaprowp-app"></div>
        <?php
        return ob_get_clean();
    }
}
