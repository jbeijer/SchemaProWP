<?php
/**
 * Resources admin page template
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
    <h1 class="wp-heading-inline"><?php esc_html_e( 'Resurser', 'schemaprowp' ); ?></h1>
    <a href="#" class="page-title-action" id="add-new-resource"><?php esc_html_e( 'Lägg till ny', 'schemaprowp' ); ?></a>
    <hr class="wp-header-end">

    <div id="schemaprowp-resources-app" 
         data-nonce="<?php echo esc_attr( wp_create_nonce( 'schemaprowp_resources_nonce' ) ); ?>"
         data-api-root="<?php echo esc_url_raw( rest_url() ); ?>"
         data-current-user="<?php echo esc_attr( wp_get_current_user()->ID ); ?>">
        <!-- Svelte resources app will be mounted here -->
    </div>
</div>
