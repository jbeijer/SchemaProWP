<?php
/**
 * Basklass för REST API controllers
 *
 * @package    SchemaProWP
 * @subpackage SchemaProWP/includes/api
 */

abstract class SchemaProWP_REST_Controller {
    /**
     * Namespace för API:et
     *
     * @var string
     */
    protected $namespace = 'schemaprowp/v1';

    /**
     * Route för denna controller
     *
     * @var string
     */
    protected $rest_base;

    /**
     * Registrera routes
     */
    public function register_routes() {
        register_rest_route(
            $this->namespace,
            '/' . $this->rest_base,
            array(
                array(
                    'methods'             => WP_REST_Server::READABLE,
                    'callback'            => array($this, 'get_items'),
                    'permission_callback' => array($this, 'get_items_permissions_check'),
                ),
                array(
                    'methods'             => WP_REST_Server::CREATABLE,
                    'callback'            => array($this, 'create_item'),
                    'permission_callback' => array($this, 'create_item_permissions_check'),
                    'args'                => $this->get_endpoint_args_for_item_schema(true),
                ),
            )
        );

        register_rest_route(
            $this->namespace,
            '/' . $this->rest_base . '/(?P<id>[\d]+)',
            array(
                array(
                    'methods'             => WP_REST_Server::READABLE,
                    'callback'            => array($this, 'get_item'),
                    'permission_callback' => array($this, 'get_item_permissions_check'),
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
                ),
            )
        );
    }

    /**
     * Kontrollera om användaren har behörighet att läsa items
     *
     * @param WP_REST_Request $request Full data om requesten
     * @return bool|WP_Error
     */
    public function get_items_permissions_check($request) {
        return current_user_can('read');
    }

    /**
     * Kontrollera om användaren har behörighet att läsa ett item
     *
     * @param WP_REST_Request $request Full data om requesten
     * @return bool|WP_Error
     */
    public function get_item_permissions_check($request) {
        return $this->get_items_permissions_check($request);
    }

    /**
     * Kontrollera om användaren har behörighet att skapa items
     *
     * @param WP_REST_Request $request Full data om requesten
     * @return bool|WP_Error
     */
    public function create_item_permissions_check($request) {
        return current_user_can('publish_posts');
    }

    /**
     * Kontrollera om användaren har behörighet att uppdatera ett item
     *
     * @param WP_REST_Request $request Full data om requesten
     * @return bool|WP_Error
     */
    public function update_item_permissions_check($request) {
        return $this->create_item_permissions_check($request);
    }

    /**
     * Kontrollera om användaren har behörighet att ta bort ett item
     *
     * @param WP_REST_Request $request Full data om requesten
     * @return bool|WP_Error
     */
    public function delete_item_permissions_check($request) {
        return $this->create_item_permissions_check($request);
    }

    /**
     * Förbereder ett item för response
     *
     * @param mixed           $item    Item att förbereda
     * @param WP_REST_Request $request Request object
     * @return WP_REST_Response
     */
    public function prepare_item_for_response($item, $request) {
        return rest_ensure_response($item);
    }

    /**
     * Hämta argument för endpoint baserat på schema
     *
     * @param bool $is_create Om detta är för create operation
     * @return array
     */
    protected function get_endpoint_args_for_item_schema($is_create = false) {
        return array();
    }

    /**
     * Sanitera och validera request parametrar
     *
     * @param array           $params  Request parametrar
     * @param WP_REST_Request $request Full data om requesten
     * @param string          $type    Typ av validering (create/update)
     * @return true|WP_Error True om valid, WP_Error annars
     */
    protected function validate_request_params($params, $request, $type) {
        return true;
    }
}
