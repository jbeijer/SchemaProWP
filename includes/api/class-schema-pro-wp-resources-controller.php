<?php
/**
 * REST API Controller för resurshantering.
 *
 * @package SchemaProWP
 * @since 1.0.0
 */

class SchemaProWP_Resources_Controller extends WP_REST_Controller {
    
    protected $namespace = 'schemaprowp/v1';
    protected $rest_base = 'resources';
    protected $resource_model;

    public function __construct() {
        $this->resource_model = new SchemaProWP_Resource();
    }

    /**
     * Registrera routes.
     */
    public function register_routes() {
        register_rest_route(
            $this->namespace,
            '/' . $this->rest_base,
            array(
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
                    'args' => $this->get_endpoint_args_for_item_schema(WP_REST_Server::CREATABLE),
                )
            )
        );

        register_rest_route(
            $this->namespace,
            '/' . $this->rest_base . '/(?P<id>[\d]+)',
            array(
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
            )
        );
    }

    /**
     * Check if a given request has access to get items
     *
     * @param WP_REST_Request $request Full data about the request.
     * @return WP_Error|bool
     */
    public function get_items_permissions_check($request) {
        return is_user_logged_in();
    }

    /**
     * Check if a given request has access to get a specific item
     *
     * @param WP_REST_Request $request Full data about the request.
     * @return WP_Error|bool
     */
    public function get_item_permissions_check($request) {
        return is_user_logged_in();
    }

    /**
     * Check if a given request has access to create items
     *
     * @param WP_REST_Request $request Full data about the request.
     * @return WP_Error|bool
     */
    public function create_item_permissions_check($request) {
        return current_user_can('edit_posts');
    }

    /**
     * Check if a given request has access to update a specific item
     *
     * @param WP_REST_Request $request Full data about the request.
     * @return WP_Error|bool
     */
    public function update_item_permissions_check($request) {
        return current_user_can('edit_posts');
    }

    /**
     * Check if a given request has access to delete a specific item
     *
     * @param WP_REST_Request $request Full data about the request.
     * @return WP_Error|bool
     */
    public function delete_item_permissions_check($request) {
        return current_user_can('delete_posts');
    }

    /**
     * Hämta resurser.
     *
     * @param WP_REST_Request $request Request object.
     * @return WP_REST_Response|WP_Error Response object eller WP_Error.
     */
    public function get_items($request) {
        try {
            // Process request parameters
            $args = array(
                'per_page' => $request->get_param('per_page') ? (int) $request->get_param('per_page') : 10,
                'page' => $request->get_param('page') ? (int) $request->get_param('page') : 1,
                'orderby' => $request->get_param('orderby') ?: 'id',
                'order' => $request->get_param('order') ?: 'DESC',
            );

            // Get resources from the model
            $result = $this->resource_model->get_all($args);

            if (is_wp_error($result)) {
                return $result;
            }

            // Prepare items for response
            $items = array_map(array($this, 'prepare_response_for_collection'), $result['items']);

            // Prepare response
            $response = rest_ensure_response($items);

            // Add pagination headers
            $response->header('X-WP-Total', $result['total']);
            $response->header('X-WP-TotalPages', ceil($result['total'] / $result['per_page']));

            return $response;

        } catch (Exception $e) {
            error_log('SchemaProWP REST API Error: ' . $e->getMessage());
            return new WP_Error(
                'rest_error',
                'An error occurred while processing your request.',
                array('status' => 500)
            );
        }
    }

    /**
     * Hämta resurs.
     *
     * @param WP_REST_Request $request Request object.
     * @return WP_REST_Response|WP_Error Response object eller WP_Error.
     */
    public function get_item($request) {
        try {
            // Get resource from the model
            $result = $this->resource_model->get($request->get_param('id'));

            if (is_wp_error($result)) {
                return $result;
            }

            // Prepare response
            $response = rest_ensure_response($result);

            return $response;

        } catch (Exception $e) {
            return new WP_Error(
                'rest_error',
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
        try {
            // Sanitera input
            $params = $this->sanitize_request_params($request->get_params());
            
            // Validera input
            $validation = $this->validate_request_params($params, $request);
            if (is_wp_error($validation)) {
                return $validation;
            }
            
            // Skapa resurs
            $resource = $this->resource_model->create($params);
            
            if (is_wp_error($resource)) {
                return $resource;
            }
            
            // Prepare response
            $response = rest_ensure_response($this->prepare_response_for_collection($resource));

            return $response;

        } catch (Exception $e) {
            return new WP_Error(
                'rest_error',
                $e->getMessage(),
                array('status' => 500)
            );
        }
    }

    /**
     * Uppdatera resurs.
     *
     * @param WP_REST_Request $request Request object.
     * @return WP_REST_Response|WP_Error Response object eller WP_Error.
     */
    public function update_item($request) {
        try {
            // Sanitera input
            $params = $this->sanitize_request_params($request->get_params());
            
            // Validera input
            $validation = $this->validate_request_params($params, $request);
            if (is_wp_error($validation)) {
                return $validation;
            }
            
            // Uppdatera resurs
            $resource = $this->resource_model->update($request->get_param('id'), $params);
            
            if (is_wp_error($resource)) {
                return $resource;
            }
            
            // Prepare response
            $response = rest_ensure_response($this->prepare_response_for_collection($resource));

            return $response;

        } catch (Exception $e) {
            return new WP_Error(
                'rest_error',
                $e->getMessage(),
                array('status' => 500)
            );
        }
    }

    /**
     * Ta bort resurs.
     *
     * @param WP_REST_Request $request Request object.
     * @return WP_REST_Response|WP_Error Response object eller WP_Error.
     */
    public function delete_item($request) {
        try {
            // Ta bort resurs
            $result = $this->resource_model->delete($request->get_param('id'));
            
            if (is_wp_error($result)) {
                return $result;
            }
            
            // Prepare response
            $response = rest_ensure_response(array('message' => __('Resurs borttagen.', 'schemaprowp')));

            return $response;

        } catch (Exception $e) {
            return new WP_Error(
                'rest_error',
                $e->getMessage(),
                array('status' => 500)
            );
        }
    }

    /**
     * Sanitera request parametrar.
     *
     * @param array $params Request parametrar.
     * @return array Saniterade parametrar.
     */
    protected function sanitize_request_params($params) {
        $sanitized = array();
        
        if (!empty($params['title'])) {
            $sanitized['title'] = sanitize_text_field($params['title']);
        }
        
        if (!empty($params['description'])) {
            $sanitized['description'] = wp_kses_post($params['description']);
        }
        
        if (!empty($params['type'])) {
            $sanitized['type'] = sanitize_text_field($params['type']);
        }
        
        if (!empty($params['status'])) {
            $sanitized['status'] = sanitize_text_field($params['status']);
        }
        
        return $sanitized;
    }

    /**
     * Validera request parametrar.
     *
     * @param array           $params  Request parametrar.
     * @param WP_REST_Request $request Request object (optional).
     * @return true|WP_Error True om valid, WP_Error annars.
     */
    protected function validate_request_params($params, $request) {
        $errors = new WP_Error();
        
        if (empty($params['title'])) {
            $errors->add(
                'missing_title',
                __('Title is required.', 'schemaprowp'),
                array('status' => 400)
            );
        }
        
        if (!empty($params['type'])) {
            $valid_types = array('room', 'equipment');
            if (!in_array($params['type'], $valid_types)) {
                $errors->add(
                    'invalid_type',
                    __('Invalid resource type.', 'schemaprowp'),
                    array('status' => 400)
                );
            }
        }
        
        if (!empty($params['status'])) {
            $valid_statuses = array('active', 'inactive', 'maintenance');
            if (!in_array($params['status'], $valid_statuses)) {
                $errors->add(
                    'invalid_status',
                    __('Invalid resource status.', 'schemaprowp'),
                    array('status' => 400)
                );
            }
        }
        
        return $errors->has_errors() ? $errors : true;
    }

    /**
     * Get the query params for collections.
     *
     * @return array Collection parameters.
     */
    public function get_collection_params() {
        return array(
            'page' => array(
                'description' => __('Current page of the collection.', 'schemaprowp'),
                'type' => 'integer',
                'default' => 1,
                'minimum' => 1,
                'sanitize_callback' => 'absint',
            ),
            'per_page' => array(
                'description' => __('Maximum number of items to be returned in result set.', 'schemaprowp'),
                'type' => 'integer',
                'default' => 10,
                'minimum' => 1,
                'maximum' => 100,
                'sanitize_callback' => 'absint',
            ),
            'orderby' => array(
                'description' => __('Sort collection by parameter.', 'schemaprowp'),
                'type' => 'string',
                'default' => 'id',
                'enum' => array('id', 'title', 'type', 'status', 'created_at'),
            ),
            'order' => array(
                'description' => __('Order sort attribute ascending or descending.', 'schemaprowp'),
                'type' => 'string',
                'default' => 'DESC',
                'enum' => array('ASC', 'DESC'),
            ),
        );
    }

    /**
     * Prepare item for response.
     *
     * @param mixed $item Resource item.
     * @param WP_REST_Request $request Request object (optional).
     * @return array Prepared item data.
     */
    public function prepare_response_for_collection($item) {
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
     * Hämta endpoint argument för item schema.
     *
     * @param bool $is_create Om detta är för create operation.
     * @return array Endpoint argument.
     */
    public function get_endpoint_args_for_item_schema($method = WP_REST_Server::CREATABLE) {
        $args = array();
        
        if (WP_REST_Server::CREATABLE === $method || WP_REST_Server::EDITABLE === $method) {
            $args = array(
                'title' => array(
                    'description' => __('The title of the resource.', 'schemaprowp'),
                    'type' => 'string',
                    'required' => true,
                    'sanitize_callback' => 'sanitize_text_field',
                ),
                'description' => array(
                    'description' => __('The description of the resource.', 'schemaprowp'),
                    'type' => 'string',
                    'required' => false,
                    'sanitize_callback' => 'wp_kses_post',
                ),
                'type' => array(
                    'description' => __('The type of the resource.', 'schemaprowp'),
                    'type' => 'string',
                    'required' => true,
                    'enum' => array('room', 'equipment'),
                ),
                'status' => array(
                    'description' => __('The status of the resource.', 'schemaprowp'),
                    'type' => 'string',
                    'required' => false,
                    'default' => 'active',
                    'enum' => array('active', 'inactive', 'maintenance'),
                ),
            );
        }
        
        return $args;
    }
}
