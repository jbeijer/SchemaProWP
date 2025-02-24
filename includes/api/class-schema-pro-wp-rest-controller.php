<?php
/**
 * Abstrakt basklass för REST API controllers.
 */
abstract class SchemaProWP_REST_Controller {
    /**
     * Namespace för REST API endpoints.
     *
     * @var string
     */
    protected $namespace;

    /**
     * REST base för endpoints.
     *
     * @var string
     */
    protected $rest_base;

    /**
     * Registrera routes.
     */
    abstract public function register_routes();

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
     * Validera request parametrar.
     *
     * @param array           $params  Request parametrar.
     * @param WP_REST_Request $request Request object.
     * @return true|WP_Error True om valid, WP_Error annars.
     */
    protected function validate_request_params($params, $request) {
        return true;
    }

    /**
     * Sanitera request parametrar.
     *
     * @param array $params Request parametrar.
     * @return array Saniterade parametrar.
     */
    protected function sanitize_request_params($params) {
        return $params;
    }

    /**
     * Förbereder ett item för response.
     *
     * @param mixed           $item    Item att förbereda.
     * @param WP_REST_Request $request Request object.
     * @return array Förberett item.
     */
    protected function prepare_response_for_collection($item, $request = null) {
        return $item;
    }

    /**
     * Hämta collection parametrar.
     *
     * @return array Collection parametrar.
     */
    protected function get_collection_params() {
        return array();
    }

    /**
     * Hämta endpoint argument för item schema.
     *
     * @param bool $is_create Om detta är för create operation.
     * @return array Endpoint argument.
     */
    protected function get_endpoint_args_for_item_schema($is_create = false) {
        return array();
    }
}
