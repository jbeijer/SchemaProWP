<?php
/**
 * Fired during plugin deactivation
 *
 * @link       https://example.com
 * @since      1.0.0
 *
 * @package    SchemaProWP
 * @subpackage SchemaProWP/includes
 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 * @package    SchemaProWP
 * @subpackage SchemaProWP/includes
 * @author     Your Name
 */
class Schema_Pro_WP_Deactivator {

    /**
     * Deactivation handler.
     *
     * Long Description.
     *
     * @since    1.0.0
     */
    public static function deactivate() {
        // För närvarande behåller vi all data vid avaktivering
        // Data rensas endast vid avinstallation (uninstall.php)
        
        // Lägg till eventuell cleanup-kod här om det behövs i framtiden
    }
}
