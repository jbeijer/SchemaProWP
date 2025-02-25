<?php
/**
 * REST API Controller för resurshantering.
 *
 * @package SchemaProWP
 * @since 1.0.0
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
            global $wpdb;

            // Debug database connection
            if (!$wpdb->check_connection()) {
                return new WP_Error(
                    'db_connection_error',
                    __('Kunde inte ansluta till databasen.', 'schemaprowp'),
                    array('status' => 500)
                );
            }

            // Debug table existence
            $table_name = $wpdb->prefix . 'schemapro_resources';
            $table_exists = $wpdb->get_var("SHOW TABLES LIKE '{$table_name}'") === $table_name;
            
            if (!$table_exists) {
                // Try to create tables
                require_once plugin_dir_path(dirname(dirname(__FILE__))) . 'includes/class-schema-pro-wp-activator.php';
                SchemaProWP_Activator::create_database_tables();
                
                // Check again
                $table_exists = $wpdb->get_var("SHOW TABLES LIKE '{$table_name}'") === $table_name;
                
                if (!$table_exists) {
                    return new WP_Error(
                        'table_not_found',
                        __('Resurstabellen existerar inte. Vänligen återaktivera pluginet.', 'schemaprowp'),
                        array('status' => 500)
                    );
                }
            }

            // Get and validate query parameters
            $args = $this->prepare_query_args($request);
            if (is_wp_error($args)) {
                return $args;
            }

            // Get resources from database
            $resource_model = new SchemaProWP_Resource();
            $result = $resource_model->get_all($args);
            
            if (is_wp_error($result)) {
                return $result;
            }

            // Prepare items for response
            $items = array();
            foreach ($result['items'] as $item) {
                $items[] = $this->prepare_response_for_collection($item);
            }

            // Create the response
            $response = rest_ensure_response($items);

            // Add pagination headers
            $response->header('X-WP-Total', $result['total']);
            $response->header('X-WP-TotalPages', $result['pages']);

            return $response;

        } catch (Exception $e) {
            return new WP_Error(
                'schemaprowp_error',
                $e->getMessage(),
                array('status' => 500)
            );
        }
    }

    /**
     * Prepare query arguments for get_items.
     *
     * @param WP_REST_Request $request Request object.
     * @return array|WP_Error Prepared arguments or WP_Error.
     */
    protected function prepare_query_args($request) {
        $args = array();
        $params = $request->get_params();

        // Validate and sanitize pagination parameters
        if (!empty($params['per_page'])) {
            $per_page = absint($params['per_page']);
            if ($per_page < 1 || $per_page > 100) {
                return new WP_Error(
                    'rest_invalid_param',
                    __('Antal per sida måste vara mellan 1 och 100.', 'schemaprowp'),
                    array('status' => 400)
                );
            }
            $args['per_page'] = $per_page;
        } else {
            $args['per_page'] = 10;
        }

        if (!empty($params['page'])) {
            $page = absint($params['page']);
            if ($page < 1) {
                return new WP_Error(
                    'rest_invalid_param',
                    __('Sidnummer måste vara större än 0.', 'schemaprowp'),
                    array('status' => 400)
                );
            }
            $args['page'] = $page;
        } else {
            $args['page'] = 1;
        }

        // Validate and sanitize filter parameters
        if (!empty($params['type'])) {
            $valid_types = array('room', 'equipment', 'vehicle');
            $type = sanitize_text_field($params['type']);
            if (!in_array($type, $valid_types)) {
                return new WP_Error(
                    'rest_invalid_param',
                    __('Ogiltig resurstyp.', 'schemaprowp'),
                    array('status' => 400)
                );
            }
            $args['type'] = $type;
        }

        if (!empty($params['status'])) {
            $valid_statuses = array('available', 'booked', 'maintenance');
            $status = sanitize_text_field($params['status']);
            if (!in_array($status, $valid_statuses)) {
                return new WP_Error(
                    'rest_invalid_param',
                    __('Ogiltig status.', 'schemaprowp'),
                    array('status' => 400)
                );
            }
            $args['status'] = $status;
        }

        // Apply search parameter if present
        if (!empty($params['search'])) {
            $args['search'] = sanitize_text_field($params['search']);
        }

        return $args;
    }

    /**
     * Prepare item for response.
     *
     * @param mixed $item Resource item.
     * @param WP_REST_Request $request Request object (optional).
     * @return array Prepared item data.
     */
    public function prepare_response_for_collection($item, $request = null) {
        return array(
            'id' => absint($item['id']),
            'title' => sanitize_text_field($item['title']),
            'description' => wp_kses_post($item['description']),
            'type' => sanitize_text_field($item['type']),
            'status' => sanitize_text_field($item['status']),
            'created_at' => mysql_to_rfc3339($item['created_at']),
            'updated_at' => mysql_to_rfc3339($item['updated_at'])
        );
    }

    /**
     * Get the query params for collections.
     *
     * @return array Collection parameters.
     */
    public function get_collection_params() {
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
            'type' => array(
                'description' => __('Filtrera efter resurstyp.', 'schemaprowp'),
                'type' => 'string',
                'enum' => array('room', 'equipment', 'vehicle'),
                'sanitize_callback' => 'sanitize_text_field',
            ),
            'status' => array(
                'description' => __('Filtrera efter status.', 'schemaprowp'),
                'type' => 'string',
                'enum' => array('available', 'booked', 'maintenance'),
                'sanitize_callback' => 'sanitize_text_field',
            ),
        );
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
        
        return rest_ensure_response($this->prepare_response_for_collection($resource));
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
