<?php
/**
 * Main admin page template
 *
 * @package    SchemaProWP
 * @subpackage SchemaProWP/admin/partials
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die;
}
?>

<div class="wrap schemaprowp-admin">
    <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
    
    <div class="schemaprowp-admin-overview">
        <div class="schemaprowp-admin-stats">
            <h2><?php esc_html_e( 'Översikt', 'schemaprowp' ); ?></h2>
            <?php
            global $wpdb;
            if (current_user_can('manage_options')) {
                $resources_count = $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->prefix}schemaprowp_resources" );
                $bookings_count = $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->prefix}schemapro_bookings" );
            }
            ?>
            <div class="schemaprowp-stats-grid">
                <div class="stat-box">
                    <h3><?php esc_html_e( 'Resurser', 'schemaprowp' ); ?></h3>
                    <p class="stat-number"><?php echo esc_html( $resources_count ); ?></p>
                </div>
                <div class="stat-box">
                    <h3><?php esc_html_e( 'Bokningar', 'schemaprowp' ); ?></h3>
                    <p class="stat-number"><?php echo esc_html( $bookings_count ); ?></p>
                </div>
            </div>
        </div>
    </div>

    <div id="schemaprowp-app" 
         data-nonce="<?php echo esc_attr( wp_create_nonce( 'schemaprowp_admin_nonce' ) ); ?>"
         data-api-root="<?php echo esc_url_raw( rest_url() ); ?>"
         data-current-user="<?php echo esc_attr( wp_get_current_user()->ID ); ?>">
        <!-- Svelte app will be mounted here -->
    </div>
</div>
