<?php
/**
 * Definiera internationaliseringsfunktionaliteten.
 *
 * Laddar och definierar de internationaliserade filerna för detta plugin
 * så att det är redo för översättning.
 *
 * @link       https://example.com
 * @since      1.0.0
 *
 * @package    SchemaProWP
 * @subpackage SchemaProWP/includes
 */

/**
 * Definiera internationaliseringsfunktionaliteten.
 *
 * Laddar och definierar de internationaliserade filerna för detta plugin
 * så att det är redo för översättning.
 *
 * @since      1.0.0
 * @package    SchemaProWP
 * @subpackage SchemaProWP/includes
 * @author     Ditt Namn <din@email.com>
 */
class SchemaProWP_i18n {

    /**
     * Ladda pluginets textdomän för översättning.
     *
     * @since    1.0.0
     */
    public function load_plugin_textdomain() {

        load_plugin_textdomain(
            'schema-pro-wp',
            false,
            dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
        );

    }

}