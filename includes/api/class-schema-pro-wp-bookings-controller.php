<?php
/**
 * REST API Controller för bokningar
 *
 * @package    SchemaProWP
 * @subpackage SchemaProWP/includes/api
 */

class SchemaProWP_Bookings_Controller extends SchemaProWP_REST_Controller {
    
    /**
     * Bokningsmodell
     *
     * @var SchemaProWP_Booking
     */
    protected $model;

    /**
     * Konstruktor
     */
    public function __construct() {
        $this->namespace = 'schemaprowp/v1';
        $this->rest_base = 'bookings';
        $this->model = new SchemaProWP_Booking();
    }

    /**
     * Registrera routes.
     */
    public function register_routes() {
        // Register base collection routes
        register_rest_route($this->namespace, '/' . $this->rest_base, array(
            array(
                'methods'             => WP_REST_Server::READABLE,
                'callback'            => array($this, 'get_items'),
                'permission_callback' => array($this, 'get_items_permissions_check'),
                'args'               => $this->get_collection_params(),
            ),
            array(
                'methods'             => WP_REST_Server::CREATABLE,
                'callback'            => array($this, 'create_item'),
                'permission_callback' => array($this, 'create_item_permissions_check'),
                'args'               => $this->get_endpoint_args_for_item_schema(true),
            ),
        ));

        // Register single item routes
        register_rest_route($this->namespace, '/' . $this->rest_base . '/(?P<id>[\d]+)', array(
            array(
                'methods'             => WP_REST_Server::READABLE,
                'callback'            => array($this, 'get_item'),
                'permission_callback' => array($this, 'get_item_permissions_check'),
                'args'               => array(
                    'id' => array(
                        'validate_callback' => 'rest_validate_request_arg',
                    ),
                ),
            ),
            array(
                'methods'             => WP_REST_Server::EDITABLE,
                'callback'            => array($this, 'update_item'),
                'permission_callback' => array($this, 'update_item_permissions_check'),
                'args'               => $this->get_endpoint_args_for_item_schema(false),
            ),
            array(
                'methods'             => WP_REST_Server::DELETABLE,
                'callback'            => array($this, 'delete_item'),
                'permission_callback' => array($this, 'delete_item_permissions_check'),
                'args'               => array(
                    'id' => array(
                        'validate_callback' => 'rest_validate_request_arg',
                    ),
                ),
            ),
        ));

        // Route för att avboka
        register_rest_route(
            $this->namespace,
            '/' . $this->rest_base . '/(?P<id>[\d]+)/cancel',
            array(
                array(
                    'methods'             => WP_REST_Server::EDITABLE,
                    'callback'            => array($this, 'cancel_booking'),
                    'permission_callback' => array($this, 'update_item_permissions_check'),
                )
            )
        );

        // Route för att hämta tillgängliga tider
        register_rest_route(
            $this->namespace,
            '/' . $this->rest_base . '/available',
            array(
                array(
                    'methods'             => WP_REST_Server::READABLE,
                    'callback'            => array($this, 'get_available_times'),
                    'permission_callback' => array($this, 'get_items_permissions_check'),
                    'args'               => array(
                        'resource_id' => array(
                            'required'    => true,
                            'type'        => 'integer',
                            'description' => __('Resource ID to check availability for.', 'schema-pro-wp'),
                        ),
                        'start_time' => array(
                            'required'    => true,
                            'type'        => 'string',
                            'format'      => 'date-time',
                            'description' => __('Start time to check from.', 'schema-pro-wp'),
                        ),
                        'end_time' => array(
                            'required'    => true,
                            'type'        => 'string',
                            'format'      => 'date-time',
                            'description' => __('End time to check until.', 'schema-pro-wp'),
                        ),
                    ),
                )
            )
        );
    }

    /**
     * Hämta en lista med bokningar
     *
     * @param WP_REST_Request $request Full data om requesten
     * @return WP_REST_Response|WP_Error
     */
    public function get_items($request) {
        try {
            // Test data for development
            $test_bookings = array(
                array(
                    'id' => 1,
                    'resource_id' => 1,
                    'resource_name' => 'Konferensrum A',
                    'user_id' => get_current_user_id(),
                    'start_time' => '2025-03-01 09:00:00',
                    'end_time' => '2025-03-01 11:00:00',
                    'status' => 'confirmed'
                ),
                array(
                    'id' => 2,
                    'resource_id' => 2,
                    'resource_name' => 'Projektor',
                    'user_id' => get_current_user_id(),
                    'start_time' => '2025-03-02 13:00:00',
                    'end_time' => '2025-03-02 15:00:00',
                    'status' => 'pending'
                )
            );

            // For development, return test data
            if (defined('WP_DEBUG') && WP_DEBUG) {
                return rest_ensure_response($test_bookings);
            }

            // Real implementation
            $args = array();

            // Sanitize and validate request parameters
            if (!empty($request['resource_id'])) {
                $args['resource_id'] = absint($request['resource_id']);
            }
            if (!empty($request['user_id'])) {
                $args['user_id'] = absint($request['user_id']);
            }
            if (!empty($request['start_date'])) {
                $args['start_date'] = sanitize_text_field($request['start_date']);
            }
            if (!empty($request['end_date'])) {
                $args['end_date'] = sanitize_text_field($request['end_date']);
            }

            $bookings = $this->model->get_bookings($args);
            
            if (is_wp_error($bookings)) {
                return new WP_Error(
                    'schemaprowp_db_error',
                    __('Ett fel uppstod vid hämtning av bokningar.', 'schemaprowp'),
                    array('status' => 500)
                );
            }

            return rest_ensure_response($bookings);

        } catch (Exception $e) {
            return new WP_Error(
                'schemaprowp_error',
                $e->getMessage(),
                array('status' => 500)
            );
        }
    }

    /**
     * Hämta en enskild bokning
     *
     * @param WP_REST_Request $request Full data om requesten
     * @return WP_REST_Response|WP_Error
     */
    public function get_item($request) {
        $id = (int) $request['id'];
        $item = $this->model->get($id);

        if (!$item) {
            return new WP_Error(
                'rest_booking_not_found',
                __('Booking not found.', 'schema-pro-wp'),
                array('status' => 404)
            );
        }

        return $this->prepare_item_for_response($item, $request);
    }

    /**
     * Skapa en ny bokning
     *
     * @param WP_REST_Request $request Full data om requesten
     * @return WP_REST_Response|WP_Error
     */
    public function create_item($request) {
        $validation = $this->validate_request_params($request->get_params(), $request);
        if (is_wp_error($validation)) {
            return $validation;
        }

        // Sätt användar-ID om det inte angetts
        if (empty($request['user_id'])) {
            $request['user_id'] = get_current_user_id();
        }

        $result = $this->model->create($request->get_params());

        if (is_wp_error($result)) {
            return $result;
        }

        $item = $this->model->get($result);
        
        return rest_ensure_response($this->prepare_item_for_response($item, $request));
    }

    /**
     * Uppdatera en bokning
     *
     * @param WP_REST_Request $request Full data om requesten
     * @return WP_REST_Response|WP_Error
     */
    public function update_item($request) {
        $validation = $this->validate_request_params($request->get_params(), $request);
        if (is_wp_error($validation)) {
            return $validation;
        }

        $id = (int) $request['id'];
        $result = $this->model->update($id, $request->get_params());

        if (is_wp_error($result)) {
            return $result;
        }

        $item = $this->model->get($id);
        
        return rest_ensure_response($this->prepare_item_for_response($item, $request));
    }

    /**
     * Ta bort en bokning
     *
     * @param WP_REST_Request $request Full data om requesten
     * @return WP_REST_Response|WP_Error
     */
    public function delete_item($request) {
        $id = (int) $request['id'];
        $result = $this->model->delete($id);

        if (!$result) {
            return new WP_Error(
                'rest_cannot_delete',
                __('The booking cannot be deleted.', 'schema-pro-wp'),
                array('status' => 500)
            );
        }

        return rest_ensure_response(array(
            'deleted'  => true,
            'previous' => $this->prepare_item_for_response($this->model->get($id), $request)
        ));
    }

    /**
     * Avboka en bokning
     *
     * @param WP_REST_Request $request Full data om requesten
     * @return WP_REST_Response|WP_Error
     */
    public function cancel_booking($request) {
        $id = (int) $request['id'];
        $result = $this->model->cancel($id);

        if (!$result) {
            return new WP_Error(
                'rest_cannot_cancel',
                __('The booking cannot be cancelled.', 'schema-pro-wp'),
                array('status' => 500)
            );
        }

        $item = $this->model->get($id);
        
        return rest_ensure_response($this->prepare_item_for_response($item, $request));
    }

    /**
     * Hämta tillgängliga tider för en resurs
     *
     * @param WP_REST_Request $request Full data om requesten
     * @return WP_REST_Response|WP_Error
     */
    public function get_available_times($request) {
        $resource_model = new SchemaProWP_Resource();
        $available = $resource_model->get_available(
            $request['start_time'],
            $request['end_time'],
            array('where' => array('id' => $request['resource_id']))
        );

        if (is_wp_error($available)) {
            return $available;
        }

        return rest_ensure_response($available);
    }

    /**
     * Förbereder ett item för response
     *
     * @param mixed           $item    Item att förbereda
     * @param WP_REST_Request $request Request object
     * @return WP_REST_Response
     */
    public function prepare_item_for_response($item, $request) {
        if (!is_object($item)) {
            return $item;
        }

        $data = array(
            'id'          => (int) $item->id,
            'resource_id' => (int) $item->resource_id,
            'user_id'     => (int) $item->user_id,
            'start_time'  => mysql_to_rfc3339($item->start_time),
            'end_time'    => mysql_to_rfc3339($item->end_time),
            'status'      => $item->status,
            'comments'    => $item->comments,
            'created_at'  => mysql_to_rfc3339($item->created_at),
            'updated_at'  => mysql_to_rfc3339($item->updated_at),
        );

        return rest_ensure_response($data);
    }

    /**
     * Hämta argument för endpoint baserat på schema
     *
     * @param bool $is_create Om detta är för create operation
     * @return array
     */
    protected function get_endpoint_args_for_item_schema($is_create = false) {
        return array(
            'resource_id' => array(
                'description' => __('Resource ID.', 'schema-pro-wp'),
                'type'        => 'integer',
                'required'    => $is_create,
            ),
            'user_id' => array(
                'description' => __('User ID.', 'schema-pro-wp'),
                'type'        => 'integer',
            ),
            'start_time' => array(
                'description' => __('Booking start time.', 'schema-pro-wp'),
                'type'        => 'string',
                'format'      => 'date-time',
                'required'    => $is_create,
            ),
            'end_time' => array(
                'description' => __('Booking end time.', 'schema-pro-wp'),
                'type'        => 'string',
                'format'      => 'date-time',
                'required'    => $is_create,
            ),
            'status' => array(
                'description' => __('Booking status.', 'schema-pro-wp'),
                'type'        => 'string',
                'enum'        => array('pending', 'confirmed', 'cancelled'),
                'default'     => 'pending',
            ),
            'comments' => array(
                'description' => __('Booking comments.', 'schema-pro-wp'),
                'type'        => 'string',
            ),
        );
    }

    /**
     * Validera request parametrar.
     *
     * @param array           $params  Request parametrar.
     * @param WP_REST_Request $request Request object.
     * @return true|WP_Error True om valid, WP_Error annars.
     */
    protected function validate_request_params($params, $request) {
        // Validera gemensamma fält
        if (!empty($params['resource_id']) && !is_numeric($params['resource_id'])) {
            return new WP_Error(
                'rest_invalid_param',
                __('Resource ID must be numeric.', 'schema-pro-wp'),
                array('status' => 400)
            );
        }

        if (!empty($params['start_time']) && !strtotime($params['start_time'])) {
            return new WP_Error(
                'rest_invalid_param',
                __('Start time must be a valid date/time.', 'schema-pro-wp'),
                array('status' => 400)
            );
        }

        if (!empty($params['end_time']) && !strtotime($params['end_time'])) {
            return new WP_Error(
                'rest_invalid_param',
                __('End time must be a valid date/time.', 'schema-pro-wp'),
                array('status' => 400)
            );
        }

        // Om detta är en create request
        if ($request->get_method() === 'POST') {
            if (empty($params['resource_id'])) {
                return new WP_Error(
                    'rest_missing_param',
                    __('Resource ID is required.', 'schema-pro-wp'),
                    array('status' => 400)
                );
            }

            if (empty($params['start_time'])) {
                return new WP_Error(
                    'rest_missing_param',
                    __('Start time is required.', 'schema-pro-wp'),
                    array('status' => 400)
                );
            }

            if (empty($params['end_time'])) {
                return new WP_Error(
                    'rest_missing_param',
                    __('End time is required.', 'schema-pro-wp'),
                    array('status' => 400)
                );
            }
        }

        return true;
    }

    /**
     * Check if a given request has access to get items
     *
     * @param WP_REST_Request $request Full data about the request.
     * @return WP_Error|bool
     */
    public function get_items_permissions_check($request) {
        // Allow public access to view bookings
        return true;
    }

    /**
     * Check if a given request has access to get a specific item
     *
     * @param WP_REST_Request $request Full data about the request.
     * @return WP_Error|bool
     */
    public function get_item_permissions_check($request) {
        // Allow public access to view individual bookings
        return true;
    }

    /**
     * Check if a given request has access to create items
     *
     * @param WP_REST_Request $request Full data about the request.
     * @return WP_Error|bool
     */
    public function create_item_permissions_check($request) {
        // For creating bookings, require user to be logged in
        if (!is_user_logged_in()) {
            return new WP_Error(
                'rest_forbidden',
                __('Du måste vara inloggad för att skapa bokningar.', 'schemaprowp'),
                array('status' => rest_authorization_required_code())
            );
        }

        // Verify nonce for logged-in users
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
        if (!is_user_logged_in()) {
            return new WP_Error(
                'rest_forbidden',
                __('Du måste vara inloggad för att uppdatera bokningar.', 'schemaprowp'),
                array('status' => rest_authorization_required_code())
            );
        }

        // Verify nonce for logged-in users
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
        if (!is_user_logged_in()) {
            return new WP_Error(
                'rest_forbidden',
                __('Du måste vara inloggad för att ta bort bokningar.', 'schemaprowp'),
                array('status' => rest_authorization_required_code())
            );
        }

        // Verify nonce for logged-in users
        if (!wp_verify_nonce($request->get_header('X-WP-Nonce'), 'wp_rest')) {
            return new WP_Error(
                'rest_forbidden_nonce',
                __('Ogiltig säkerhetstoken.', 'schemaprowp'),
                array('status' => rest_authorization_required_code())
            );
        }

        return true;
    }
}
