<?php
/**
 * Modellklass för resurser
 *
 * @package    SchemaProWP
 * @subpackage SchemaProWP/includes/models
 */

class SchemaProWP_Resource extends SchemaProWP_Model {
    
    /**
     * Konstruktor
     */
    public function __construct() {
        global $wpdb;
        $this->table_name = $wpdb->prefix . 'schemaprowp_resources';
    }

    /**
     * Hämta tabellnamn
     *
     * @return string
     */
    protected function get_table_name() {
        return $this->table_name;
    }

    /**
     * Hämta resurser för en specifik organisation
     *
     * @param int   $post_id    Organizations post ID
     * @param array $args       Extra argument för filtrering
     * @return array
     */
    public function get_by_organization($post_id, $args = array()) {
        $args['where']['post_id'] = $post_id;
        return $this->get_all($args);
    }

    /**
     * Hämta resurser av en specifik typ
     *
     * @param string $type Resurstyp
     * @param array  $args Extra argument för filtrering
     * @return array
     */
    public function get_by_type($type, $args = array()) {
        $args['where']['type'] = $type;
        return $this->get_all($args);
    }

    /**
     * Hämta tillgängliga resurser för ett tidsintervall
     *
     * @param string $start_time Starttid (Y-m-d H:i:s format)
     * @param string $end_time   Sluttid (Y-m-d H:i:s format)
     * @param array  $args       Extra argument för filtrering
     * @return array
     */
    public function get_available($start_time, $end_time, $args = array()) {
        $table = $this->get_table_name();
        $bookings_table = $this->db->prefix . 'schemaprowp_bookings';
        
        $where = "WHERE r.status = 'active'";
        $values = array();
        
        if (!empty($args['where'])) {
            foreach ($args['where'] as $field => $value) {
                $where .= " AND r.{$field} = %s";
                $values[] = $value;
            }
        }
        
        // Lägg till tidsfiltret
        $values[] = $start_time;
        $values[] = $end_time;
        $values[] = $start_time;
        $values[] = $end_time;
        
        $sql = $this->db->prepare(
            "SELECT r.* 
            FROM {$table} r
            WHERE r.id NOT IN (
                SELECT DISTINCT resource_id 
                FROM {$bookings_table}
                WHERE (start_time <= %s AND end_time >= %s)
                   OR (start_time <= %s AND end_time >= %s)
                   AND status != 'cancelled'
            )
            AND r.status = 'active'
            ORDER BY r.name ASC",
            $values
        );
        
        return $this->db->get_results($sql);
    }

    /**
     * Hämta alla resurser
     *
     * @param array $args Extra argument för filtrering och sortering
     * @return array|WP_Error
     */
    public function get_all($args = array()) {
        try {
            global $wpdb;
            $table_name = $this->get_table_name();

            // Validate and sanitize input
            $defaults = array(
                'per_page' => 10,
                'page' => 1,
                'orderby' => 'id',
                'order' => 'DESC',
                'type' => '',
                'status' => ''
            );
            $args = wp_parse_args($args, $defaults);

            // Sanitize order and orderby to prevent SQL injection
            $valid_order = sanitize_text_field(strtoupper($args['order'])) === 'ASC' ? 'ASC' : 'DESC';
            $valid_orderby = in_array($args['orderby'], ['id', 'title', 'type', 'status', 'created_at']) 
                ? sanitize_text_field($args['orderby']) 
                : 'id';

            // Calculate offset
            $page = max(1, absint($args['page']));
            $per_page = max(1, absint($args['per_page']));
            $offset = ($page - 1) * $per_page;

            // Prepare WHERE clause
            $where_conditions = array();
            $where_values = array();

            if (!empty($args['type'])) {
                $where_conditions[] = 'type = %s';
                $where_values[] = sanitize_text_field($args['type']);
            }

            if (!empty($args['status'])) {
                $where_conditions[] = 'status = %s';
                $where_values[] = sanitize_text_field($args['status']);
            }

            $where_sql = !empty($where_conditions) 
                ? 'WHERE ' . implode(' AND ', $where_conditions) 
                : '';

            // Prepare count query
            $count_query = $wpdb->prepare(
                "SELECT COUNT(*) FROM {$table_name} {$where_sql}",
                $where_values
            );

            // Execute count query with error handling
            $total_items = $wpdb->get_var($count_query);

            if ($total_items === null) {
                error_log('SchemaProWP: Resource count query failed: ' . $wpdb->last_error);
                
                return new WP_Error(
                    'resource_count_error', 
                    'Failed to count resources: ' . $wpdb->last_error, 
                    ['status' => 500]
                );
            }

            // Prepare items query
            $query = $wpdb->prepare(
                "SELECT * FROM {$table_name} 
                {$where_sql}
                ORDER BY {$valid_orderby} {$valid_order} 
                LIMIT %d OFFSET %d",
                array_merge($where_values, [$per_page, $offset])
            );

            // Execute items query
            $items = $wpdb->get_results($query, ARRAY_A);

            if ($items === null) {
                error_log('SchemaProWP: Resource query failed: ' . $wpdb->last_error);
                
                return new WP_Error(
                    'resource_query_error', 
                    'Failed to fetch resources: ' . $wpdb->last_error, 
                    ['status' => 500]
                );
            }

            // Return results with pagination
            return [
                'items' => $items,
                'total' => (int)$total_items,
                'page' => $page,
                'per_page' => $per_page,
                'pages' => ceil($total_items / $per_page)
            ];

        } catch (Exception $e) {
            error_log('SchemaProWP: Unexpected error in resource retrieval: ' . $e->getMessage());
            
            return new WP_Error(
                'resource_unexpected_error', 
                'An unexpected error occurred: ' . $e->getMessage(), 
                ['status' => 500]
            );
        }
    }

    /**
     * Validera resursdata
     *
     * @param array $data Data att validera
     * @return true|WP_Error
     */
    public function validate($data) {
        $errors = new WP_Error();
        
        if (empty($data['name'])) {
            $errors->add('empty_name', __('Resource name is required', 'schema-pro-wp'));
        }
        
        if (empty($data['post_id'])) {
            $errors->add('empty_post_id', __('Organization is required', 'schema-pro-wp'));
        } elseif (get_post_type($data['post_id']) !== 'schemaprowp_org') {
            $errors->add('invalid_post_id', __('Invalid organization', 'schema-pro-wp'));
        }
        
        if (empty($data['type'])) {
            $errors->add('empty_type', __('Resource type is required', 'schema-pro-wp'));
        }
        
        if (!empty($data['properties']) && !is_array($data['properties'])) {
            $errors->add('invalid_properties', __('Properties must be an array', 'schema-pro-wp'));
        }
        
        return $errors->has_errors() ? $errors : true;
    }

    /**
     * Skapa en ny resurs med validering
     *
     * @param array $data Resursdata
     * @return int|WP_Error
     */
    public function create($data) {
        $validation = $this->validate($data);
        if (is_wp_error($validation)) {
            return $validation;
        }
        
        // Konvertera properties till JSON om det finns
        if (!empty($data['properties'])) {
            $data['properties'] = wp_json_encode($data['properties']);
        }
        
        return parent::create($data);
    }

    /**
     * Uppdatera en resurs med validering
     *
     * @param int   $id   Resurs-ID
     * @param array $data Resursdata
     * @return bool|WP_Error
     */
    public function update($id, $data) {
        $validation = $this->validate($data);
        if (is_wp_error($validation)) {
            return $validation;
        }
        
        // Konvertera properties till JSON om det finns
        if (!empty($data['properties'])) {
            $data['properties'] = wp_json_encode($data['properties']);
        }
        
        return parent::update($id, $data);
    }
}
