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
        $this->rest_base = 'bookings';
        $this->model = new SchemaProWP_Booking();
    }

    /**
     * Registrera ytterligare routes
     */
    public function register_routes() {
        parent::register_routes();

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
        $args = array();
        
        // Hantera filtrering
        if (!empty($request['resource_id'])) {
            $args['where']['resource_id'] = $request['resource_id'];
        }
        if (!empty($request['user_id'])) {
            $args['where']['user_id'] = $request['user_id'];
        }
        if (!empty($request['status'])) {
            $args['where']['status'] = $request['status'];
        }

        // Hantera tidsfiltrering
        if (!empty($request['start_time']) && !empty($request['end_time'])) {
            return $this->model->get_by_timespan(
                $request['start_time'],
                $request['end_time'],
                $args
            );
        }

        // Hantera paginering
        if (!empty($request['per_page'])) {
            $args['limit'] = $request['per_page'];
            if (!empty($request['page'])) {
                $args['offset'] = ($request['page'] - 1) * $request['per_page'];
            }
        }

        // Hantera sortering
        if (!empty($request['orderby'])) {
            $args['orderby'] = $request['orderby'];
        }
        if (!empty($request['order'])) {
            $args['order'] = $request['order'];
        }

        $items = $this->model->get_all($args);
        
        if (is_wp_error($items)) {
            return $items;
        }

        $response = array();
        foreach ($items as $item) {
            $response[] = $this->prepare_item_for_response($item, $request);
        }

        return rest_ensure_response($response);
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
        $validation = $this->validate_request_params($request->get_params(), $request, 'create');
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
        $validation = $this->validate_request_params($request->get_params(), $request, 'update');
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
     * Validera request parametrar
     *
     * @param array           $params  Request parametrar
     * @param WP_REST_Request $request Full data om requesten
     * @param string          $type    Typ av validering (create/update)
     * @return true|WP_Error True om valid, WP_Error annars
     */
    protected function validate_request_params($params, $request, $type) {
        $validation = $this->model->validate($params);
        if (is_wp_error($validation)) {
            return $validation;
        }

        return true;
    }
}
