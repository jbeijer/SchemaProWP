<?php
/**
 * REST API Controller för resurshantering.
 */
class SchemaProWP_Resources_Controller extends SchemaProWP_REST_Controller {
    /**
     * Constructor.
     */
    public function __construct() {
        $this->namespace = 'schemaprowp/v1';
        $this->rest_base = 'resources';
    }

    /**
     * Registrera routes.
     */
    public function register_routes() {
        register_rest_route($this->namespace, '/' . $this->rest_base, array(
            array(
                'methods' => WP_REST_Server::READABLE,
                'callback' => array($this, 'get_items'),
                'permission_callback' => array($this, 'get_items_permissions_check'),
                'args' => $this->get_collection_params(),
            ),
            array(
                'methods' => WP_REST_Server::CREATABLE,
                'callback' => array($this, 'create_item'),
                'permission_callback' => array($this, 'create_item_permissions_check'),
                'args' => $this->get_endpoint_args_for_item_schema(true),
            ),
        ));

        register_rest_route($this->namespace, '/' . $this->rest_base . '/(?P<id>[\d]+)', array(
            array(
                'methods' => WP_REST_Server::READABLE,
                'callback' => array($this, 'get_item'),
                'permission_callback' => array($this, 'get_item_permissions_check'),
                'args' => array(
                    'id' => array(
                        'validate_callback' => 'rest_validate_request_arg',
                    ),
                ),
            ),
            array(
                'methods' => WP_REST_Server::EDITABLE,
                'callback' => array($this, 'update_item'),
                'permission_callback' => array($this, 'update_item_permissions_check'),
                'args' => $this->get_endpoint_args_for_item_schema(false),
            ),
            array(
                'methods' => WP_REST_Server::DELETABLE,
                'callback' => array($this, 'delete_item'),
                'permission_callback' => array($this, 'delete_item_permissions_check'),
                'args' => array(
                    'id' => array(
                        'validate_callback' => 'rest_validate_request_arg',
                    ),
                ),
            ),
        ));
    }

    /**
     * Check if a given request has access to get items
     *
     * @param WP_REST_Request $request Full data about the request.
     * @return WP_Error|bool
     */
    public function get_items_permissions_check($request) {
        // Allow public access to view resources
        return true;
    }

    /**
     * Check if a given request has access to get a specific item
     *
     * @param WP_REST_Request $request Full data about the request.
     * @return WP_Error|bool
     */
    public function get_item_permissions_check($request) {
        // Allow public access to view individual resources
        return true;
    }

    /**
     * Check if a given request has access to create items
     *
     * @param WP_REST_Request $request Full data about the request.
     * @return WP_Error|bool
     */
    public function create_item_permissions_check($request) {
        // Only administrators can create resources
        if (!current_user_can('manage_options')) {
            return new WP_Error(
                'rest_forbidden',
                __('Du har inte behörighet att skapa resurser.', 'schemaprowp'),
                array('status' => rest_authorization_required_code())
            );
        }

        // Verify nonce for admin users
        if (!wp_verify_nonce($request->get_header('X-WP-Nonce'), 'wp_rest')) {
            return new WP_Error(
                'rest_forbidden_nonce',
                __('Ogiltig säkerhetstoken.', 'schemaprowp'),
                array('status' => rest_authorization_required_code())
            );
        }

        return true;
    }

    /**
     * Check if a given request has access to update a specific item
     *
     * @param WP_REST_Request $request Full data about the request.
     * @return WP_Error|bool
     */
    public function update_item_permissions_check($request) {
        // Only administrators can update resources
        if (!current_user_can('manage_options')) {
            return new WP_Error(
                'rest_forbidden',
                __('Du har inte behörighet att uppdatera resurser.', 'schemaprowp'),
                array('status' => rest_authorization_required_code())
            );
        }

        // Verify nonce for admin users
        if (!wp_verify_nonce($request->get_header('X-WP-Nonce'), 'wp_rest')) {
            return new WP_Error(
                'rest_forbidden_nonce',
                __('Ogiltig säkerhetstoken.', 'schemaprowp'),
                array('status' => rest_authorization_required_code())
            );
        }

        return true;
    }

    /**
     * Check if a given request has access to delete a specific item
     *
     * @param WP_REST_Request $request Full data about the request.
     * @return WP_Error|bool
     */
    public function delete_item_permissions_check($request) {
        // Only administrators can delete resources
        if (!current_user_can('manage_options')) {
            return new WP_Error(
                'rest_forbidden',
                __('Du har inte behörighet att ta bort resurser.', 'schemaprowp'),
                array('status' => rest_authorization_required_code())
            );
        }

        // Verify nonce for admin users
        if (!wp_verify_nonce($request->get_header('X-WP-Nonce'), 'wp_rest')) {
            return new WP_Error(
                'rest_forbidden_nonce',
                __('Ogiltig säkerhetstoken.', 'schemaprowp'),
                array('status' => rest_authorization_required_code())
            );
        }

        return true;
    }

    /**
     * Hämta resurser.
     *
     * @param WP_REST_Request $request Request object.
     * @return WP_REST_Response|WP_Error Response object eller WP_Error.
     */
    public function get_items($request) {
        try {
            // Test data for development
            $test_resources = array(
                array(
                    'id' => 1,
                    'name' => 'Konferensrum A',
                    'type' => 'room',
                    'status' => 'available'
                ),
                array(
                    'id' => 2,
                    'name' => 'Projektor',
                    'type' => 'equipment',
                    'status' => 'available'
                ),
                array(
                    'id' => 3,
                    'name' => 'Tjänstebil',
                    'type' => 'vehicle',
                    'status' => 'available'
                )
            );

            // For development, return test data
            if (defined('WP_DEBUG') && WP_DEBUG) {
                return rest_ensure_response($test_resources);
            }

            // Real implementation
            $resource_model = new SchemaProWP_Resource();
            $args = array();

            if (!empty($request['per_page'])) {
                $args['per_page'] = absint($request['per_page']);
            }

            $resources = $resource_model->get_resources($args);
            
            if (is_wp_error($resources)) {
                return new WP_Error(
                    'schemaprowp_db_error',
                    __('Ett fel uppstod vid hämtning av resurser.', 'schemaprowp'),
                    array('status' => 500)
                );
            }

            return rest_ensure_response($resources);

        } catch (Exception $e) {
            return new WP_Error(
                'schemaprowp_error',
                $e->getMessage(),
                array('status' => 500)
            );
        }
    }

    /**
     * Skapa resurs.
     *
     * @param WP_REST_Request $request Request object.
     * @return WP_REST_Response|WP_Error Response object eller WP_Error.
     */
    public function create_item($request) {
        $resource_model = new SchemaProWP_Resource();
        
        // Sanitera input
        $params = $this->sanitize_request_params($request->get_params());
        
        // Validera input
        $validation = $this->validate_request_params($params, $request);
        if (is_wp_error($validation)) {
            return $validation;
        }
        
        $resource = $resource_model->create($params);
        
        if (is_wp_error($resource)) {
            return $resource;
        }
        
        return rest_ensure_response($this->prepare_response_for_collection($resource, $request));
    }

    /**
     * Förbereder ett item för response.
     *
     * @param mixed           $item    Item att förbereda.
     * @param WP_REST_Request $request Request object.
     * @return array Förberett item.
     */
    protected function prepare_response_for_collection($item, $request = null) {
        if (!is_object($item)) {
            return $item;
        }

        return array(
            'id' => absint($item->id),
            'title' => esc_html($item->title),
            'description' => wp_kses_post($item->description),
            'status' => esc_attr($item->status),
            'created_at' => mysql_to_rfc3339($item->created_at),
            'updated_at' => mysql_to_rfc3339($item->updated_at),
        );
    }

    /**
     * Sanitera request parametrar.
     *
     * @param array $params Request parametrar.
     * @return array Saniterade parametrar.
     */
    protected function sanitize_request_params($params) {
        $sanitized = array();
        
        if (isset($params['title'])) {
            $sanitized['title'] = sanitize_text_field($params['title']);
        }
        if (isset($params['description'])) {
            $sanitized['description'] = wp_kses_post($params['description']);
        }
        if (isset($params['status'])) {
            $sanitized['status'] = sanitize_key($params['status']);
        }
        
        return $sanitized;
    }

    /**
     * Validera request parametrar.
     *
     * @param array           $params  Request parametrar.
     * @param WP_REST_Request $request Request object.
     * @return true|WP_Error True om valid, WP_Error annars.
     */
    protected function validate_request_params($params, $request) {
        if (empty($params['title'])) {
            return new WP_Error(
                'rest_missing_title',
                __('Titel är obligatorisk.', 'schemaprowp'),
                array('status' => 400)
            );
        }

        if (!empty($params['status']) && !in_array($params['status'], array('active', 'inactive'), true)) {
            return new WP_Error(
                'rest_invalid_status',
                __('Ogiltig status.', 'schemaprowp'),
                array('status' => 400)
            );
        }

        return true;
    }

    /**
     * Hämta collection parametrar.
     *
     * @return array Collection parametrar.
     */
    protected function get_collection_params() {
        return array(
            'page' => array(
                'description' => __('Aktuell sida av resultatet.', 'schemaprowp'),
                'type' => 'integer',
                'default' => 1,
                'minimum' => 1,
                'sanitize_callback' => 'absint',
            ),
            'per_page' => array(
                'description' => __('Antal resultat per sida.', 'schemaprowp'),
                'type' => 'integer',
                'default' => 10,
                'minimum' => 1,
                'maximum' => 100,
                'sanitize_callback' => 'absint',
            ),
            'search' => array(
                'description' => __('Sökterm att filtrera resultat med.', 'schemaprowp'),
                'type' => 'string',
                'sanitize_callback' => 'sanitize_text_field',
            ),
        );
    }

    /**
     * Hämta endpoint argument för item schema.
     *
     * @param bool $is_create Om detta är för create operation.
     * @return array Endpoint argument.
     */
    protected function get_endpoint_args_for_item_schema($is_create = false) {
        return array(
            'title' => array(
                'description' => __('Resursens titel.', 'schemaprowp'),
                'type' => 'string',
                'required' => $is_create,
                'sanitize_callback' => 'sanitize_text_field',
            ),
            'description' => array(
                'description' => __('Resursens beskrivning.', 'schemaprowp'),
                'type' => 'string',
                'sanitize_callback' => 'wp_kses_post',
            ),
            'status' => array(
                'description' => __('Resursens status.', 'schemaprowp'),
                'type' => 'string',
                'enum' => array('active', 'inactive'),
                'default' => 'active',
                'sanitize_callback' => 'sanitize_key',
            ),
        );
    }
}
