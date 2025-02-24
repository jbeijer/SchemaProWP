<?php
/**
 * Settings admin page template
 *
 * @package    SchemaProWP
 * @subpackage SchemaProWP/admin/partials
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die;
}

$settings = get_option( 'schemaprowp_settings', array() );
$booking_settings = isset( $settings['booking_settings'] ) ? $settings['booking_settings'] : array();
$notification_settings = isset( $settings['notification_settings'] ) ? $settings['notification_settings'] : array();
?>

<div class="wrap schemaprowp-admin">
    <h1><?php esc_html_e( 'Inställningar', 'schemaprowp' ); ?></h1>

    <form method="post" action="options.php" class="schemaprowp-settings-form">
        <?php settings_fields( 'schemaprowp_settings' ); ?>
        
        <div class="schemaprowp-settings-section">
            <h2><?php esc_html_e( 'Bokningsinställningar', 'schemaprowp' ); ?></h2>
            <table class="form-table">
                <tr>
                    <th scope="row">
                        <label for="min_booking_length">
                            <?php esc_html_e( 'Minsta bokningstid (minuter)', 'schemaprowp' ); ?>
                        </label>
                    </th>
                    <td>
                        <input type="number" 
                               id="min_booking_length" 
                               name="schemaprowp_settings[booking_settings][min_booking_length]" 
                               value="<?php echo esc_attr( $booking_settings['min_booking_length'] ?? 30 ); ?>" 
                               min="15" 
                               step="15">
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="max_booking_length">
                            <?php esc_html_e( 'Längsta bokningstid (minuter)', 'schemaprowp' ); ?>
                        </label>
                    </th>
                    <td>
                        <input type="number" 
                               id="max_booking_length" 
                               name="schemaprowp_settings[booking_settings][max_booking_length]" 
                               value="<?php echo esc_attr( $booking_settings['max_booking_length'] ?? 480 ); ?>" 
                               min="60" 
                               step="30">
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="advance_booking_days">
                            <?php esc_html_e( 'Förbokningstid (dagar)', 'schemaprowp' ); ?>
                        </label>
                    </th>
                    <td>
                        <input type="number" 
                               id="advance_booking_days" 
                               name="schemaprowp_settings[booking_settings][advance_booking_days]" 
                               value="<?php echo esc_attr( $booking_settings['advance_booking_days'] ?? 30 ); ?>" 
                               min="1">
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="cancellation_period">
                            <?php esc_html_e( 'Avbokningstid (timmar)', 'schemaprowp' ); ?>
                        </label>
                    </th>
                    <td>
                        <input type="number" 
                               id="cancellation_period" 
                               name="schemaprowp_settings[booking_settings][cancellation_period]" 
                               value="<?php echo esc_attr( $booking_settings['cancellation_period'] ?? 24 ); ?>" 
                               min="1">
                    </td>
                </tr>
            </table>
        </div>

        <div class="schemaprowp-settings-section">
            <h2><?php esc_html_e( 'Aviseringsinställningar', 'schemaprowp' ); ?></h2>
            <table class="form-table">
                <tr>
                    <th scope="row">
                        <?php esc_html_e( 'E-postaviseringar', 'schemaprowp' ); ?>
                    </th>
                    <td>
                        <label>
                            <input type="checkbox" 
                                   name="schemaprowp_settings[notification_settings][email_notifications]" 
                                   value="1" 
                                   <?php checked( isset( $notification_settings['email_notifications'] ) ? $notification_settings['email_notifications'] : true ); ?>>
                            <?php esc_html_e( 'Aktivera e-postaviseringar', 'schemaprowp' ); ?>
                        </label>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="admin_email">
                            <?php esc_html_e( 'Admin e-post', 'schemaprowp' ); ?>
                        </label>
                    </th>
                    <td>
                        <input type="email" 
                               id="admin_email" 
                               name="schemaprowp_settings[notification_settings][admin_email]" 
                               value="<?php echo esc_attr( $notification_settings['admin_email'] ?? get_option( 'admin_email' ) ); ?>" 
                               class="regular-text">
                    </td>
                </tr>
            </table>
        </div>

        <?php submit_button(); ?>
    </form>
</div>
