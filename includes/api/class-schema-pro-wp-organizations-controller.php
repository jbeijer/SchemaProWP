<?php
/**
 * REST API Controller för organisationer
 *
 * @package SchemaProWP
 * @subpackage SchemaProWP/includes/api
 */

class SchemaProWP_Organizations_Controller extends SchemaProWP_REST_Controller {
    
    /**
     * Konstruktor
     */
    public function __construct() {
        $this->namespace = 'schemaprowp/v1';
        $this->rest_base = 'organizations';
    }

    /**
     * Registrera routes.
     */
    public function register_routes() {
        register_rest_route($this->namespace, '/' . $this->rest_base, array(
            array(
                'methods'             => WP_REST_Server::READABLE,
                'callback'            => array($this, 'get_items'),
                'permission_callback' => array($this, 'get_items_permissions_check'),
                'args'                => $this->get_collection_params(),
            ),
            array(
                'methods'             => WP_REST_Server::CREATABLE,
                'callback'            => array($this, 'create_item'),
                'permission_callback' => array($this, 'create_item_permissions_check'),
                'args'                => $this->get_endpoint_args_for_item_schema(true),
            ),
        ));

        register_rest_route($this->namespace, '/' . $this->rest_base . '/(?P<id>[\d]+)', array(
            array(
                'methods'             => WP_REST_Server::READABLE,
                'callback'            => array($this, 'get_item'),
                'permission_callback' => array($this, 'get_item_permissions_check'),
                'args'                => array(
                    'id' => array(
                        'validate_callback' => 'rest_validate_request_arg',
                    ),
                ),
            ),
            array(
                'methods'             => WP_REST_Server::EDITABLE,
                'callback'            => array($this, 'update_item'),
                'permission_callback' => array($this, 'update_item_permissions_check'),
                'args'                => $this->get_endpoint_args_for_item_schema(false),
            ),
            array(
                'methods'             => WP_REST_Server::DELETABLE,
                'callback'            => array($this, 'delete_item'),
                'permission_callback' => array($this, 'delete_item_permissions_check'),
                'args'                => array(
                    'id' => array(
                        'validate_callback' => 'rest_validate_request_arg',
                    ),
                ),
            ),
        ));
    }

    /**
     * Hämta alla organisationer
     *
     * @param WP_REST_Request $request Full data om requesten
     * @return WP_REST_Response|WP_Error
     */
    public function get_items($request) {
        $args = array(
            'post_type' => 'schemapro_org',
            'posts_per_page' => $request['per_page'],
            'paged' => $request['page'],
            'post_parent' => $request['parent_id']
        );

        $query = new WP_Query($args);
        $organizations = array();

        foreach ($query->posts as $post) {
            $organizations[] = $this->prepare_item_for_response($post, $request);
        }

        return rest_ensure_response($organizations);
    }

    /**
     * Hämta en specifik organisation
     *
     * @param WP_REST_Request $request Full data om requesten
     * @return WP_REST_Response|WP_Error
     */
    public function get_item($request) {
        $post = get_post((int) $request['id']);

        if (empty($post) || $post->post_type !== 'schemapro_org') {
            return new WP_Error('rest_organization_not_found', __('Organization not found.', 'schemaprowp'), array('status' => 404));
        }

        return $this->prepare_item_for_response($post, $request);
    }

    protected function prepare_item_for_response($post, $request) {
        return array(
            'id' => $post->ID,
            'name' => $post->post_title,
            'description' => $post->post_content,
            'parent_id' => $post->post_parent,
            'websiteUrl' => get_post_meta($post->ID, 'organization_website', true),
            'logoUrl' => get_post_meta($post->ID, 'organization_logo', true),
            'contactInfo' => array(
                'email' => get_post_meta($post->ID, 'organization_email', true),
                'phone' => get_post_meta($post->ID, 'organization_phone', true)
            ),
            'location' => get_post_meta($post->ID, 'organization_location', true) ?: array(
                'streetAddress' => '',
                'city' => '',
                'postalCode' => '',
                'country' => ''
            )
        );
    }

    /**
     * Skapa en ny organisation
     *
     * @param WP_REST_Request $request Full data om requesten
     * @return WP_REST_Response|WP_Error
     */
    public function create_item($request) {
        return rest_ensure_response(array(
            'id' => 4,
            'name' => $request['name'],
            'parent_id' => $request['parent_id'] ?? null
        ));
    }

    /**
     * Uppdatera en organisation
     *
     * @param WP_REST_Request $request Full data om requesten
     * @return WP_REST_Response|WP_Error
     */
    public function update_item($request) {
        return rest_ensure_response(array(
            'id' => (int) $request['id'],
            'name' => $request['name'],
            'parent_id' => $request['parent_id'] ?? null
        ));
    }

    /**
     * Ta bort en organisation
     *
     * @param WP_REST_Request $request Full data om requesten
     * @return WP_REST_Response|WP_Error
     */
    public function delete_item($request) {
        return rest_ensure_response(array(
            'deleted' => true,
            'id' => (int) $request['id']
        ));
    }

    public function get_items_permissions_check($request) {
        return true;
    }

    public function get_item_permissions_check($request) {
        return $this->get_items_permissions_check($request);
    }

    public function create_item_permissions_check($request) {
        return current_user_can('edit_posts');
    }

    public function update_item_permissions_check($request) {
        return current_user_can('edit_posts');
    }

    public function delete_item_permissions_check($request) {
        return current_user_can('delete_posts');
    }

    public function get_collection_params() {
        return array(
            'page' => array(
                'description' => __('Current page of the collection.', 'schemaprowp'),
                'type' => 'integer',
                'default' => 1,
                'sanitize_callback' => 'absint',
            ),
            'per_page' => array(
                'description' => __('Maximum number of items to be returned in result set.', 'schemaprowp'),
                'type' => 'integer',
                'default' => 10,
                'sanitize_callback' => 'absint',
            ),
            'parent_id' => array(
                'description' => __('Filter by parent organization ID', 'schemaprowp'),
                'type' => 'integer',
                'sanitize_callback' => 'absint',
            ),
        );
    }

    protected function get_endpoint_args_for_item_schema($is_create = false) {
        return array(
            'name' => array(
                'description' => __('The name of the organization.', 'schemaprowp'),
                'type' => 'string',
                'required' => $is_create,
                'sanitize_callback' => 'sanitize_text_field',
            ),
            'parent_id' => array(
                'description' => __('Parent organization ID.', 'schemaprowp'),
                'type' => 'integer',
                'required' => false,
                'sanitize_callback' => 'absint',
            ),
        );
    }
}
