<?php
/**
 * REST API Controller för resurser
 *
 * @package    SchemaProWP
 * @subpackage SchemaProWP/includes/api
 */

class SchemaProWP_Resources_Controller extends SchemaProWP_REST_Controller {
    
    /**
     * Resursmodell
     *
     * @var SchemaProWP_Resource
     */
    protected $model;

    /**
     * Konstruktor
     */
    public function __construct() {
        $this->rest_base = 'resources';
        $this->model = new SchemaProWP_Resource();
    }

    /**
     * Hämta en lista med resurser
     *
     * @param WP_REST_Request $request Full data om requesten
     * @return WP_REST_Response|WP_Error
     */
    public function get_items($request) {
        $args = array();
        
        // Hantera filtrering
        if (!empty($request['post_id'])) {
            $args['where']['post_id'] = $request['post_id'];
        }
        if (!empty($request['type'])) {
            $args['where']['type'] = $request['type'];
        }
        if (!empty($request['status'])) {
            $args['where']['status'] = $request['status'];
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
     * Hämta en enskild resurs
     *
     * @param WP_REST_Request $request Full data om requesten
     * @return WP_REST_Response|WP_Error
     */
    public function get_item($request) {
        $id = (int) $request['id'];
        $item = $this->model->get($id);

        if (!$item) {
            return new WP_Error(
                'rest_resource_not_found',
                __('Resource not found.', 'schema-pro-wp'),
                array('status' => 404)
            );
        }

        return $this->prepare_item_for_response($item, $request);
    }

    /**
     * Skapa en ny resurs
     *
     * @param WP_REST_Request $request Full data om requesten
     * @return WP_REST_Response|WP_Error
     */
    public function create_item($request) {
        $validation = $this->validate_request_params($request->get_params(), $request, 'create');
        if (is_wp_error($validation)) {
            return $validation;
        }

        $result = $this->model->create($request->get_params());

        if (is_wp_error($result)) {
            return $result;
        }

        $item = $this->model->get($result);
        
        return rest_ensure_response($this->prepare_item_for_response($item, $request));
    }

    /**
     * Uppdatera en resurs
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
     * Ta bort en resurs
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
                __('The resource cannot be deleted.', 'schema-pro-wp'),
                array('status' => 500)
            );
        }

        return rest_ensure_response(array(
            'deleted'  => true,
            'previous' => $this->prepare_item_for_response($this->model->get($id), $request)
        ));
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
            'id'         => (int) $item->id,
            'post_id'    => (int) $item->post_id,
            'name'       => $item->name,
            'type'       => $item->type,
            'status'     => $item->status,
            'properties' => json_decode($item->properties),
            'created_at' => mysql_to_rfc3339($item->created_at),
            'updated_at' => mysql_to_rfc3339($item->updated_at),
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
            'post_id' => array(
                'description' => __('Organization post ID.', 'schema-pro-wp'),
                'type'        => 'integer',
                'required'    => $is_create,
            ),
            'name' => array(
                'description' => __('Resource name.', 'schema-pro-wp'),
                'type'        => 'string',
                'required'    => $is_create,
            ),
            'type' => array(
                'description' => __('Resource type.', 'schema-pro-wp'),
                'type'        => 'string',
                'required'    => $is_create,
            ),
            'status' => array(
                'description' => __('Resource status.', 'schema-pro-wp'),
                'type'        => 'string',
                'enum'        => array('active', 'inactive'),
                'default'     => 'active',
            ),
            'properties' => array(
                'description' => __('Resource properties.', 'schema-pro-wp'),
                'type'        => 'object',
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
