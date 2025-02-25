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
        parent::__construct();
        global $wpdb;
        $this->table_name = $wpdb->prefix . 'schemapro_resources';
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

            // Default query arguments
            $defaults = array(
                'per_page' => 10,
                'page' => 1,
                'orderby' => 'id',
                'order' => 'DESC'
            );
            $args = wp_parse_args($args, $defaults);

            // Sanitize inputs
            $orderby = sanitize_sql_orderby($args['orderby'] . ' ' . $args['order']) ?: 'id DESC';
            $per_page = absint($args['per_page']);
            $offset = absint(($args['page'] - 1) * $per_page);

            // Count total items
            $count_query = "SELECT COUNT(*) FROM {$table_name}";
            $total_items = $wpdb->get_var($count_query);

            if ($total_items === null) {
                return new WP_Error(
                    'resource_count_error',
                    'Failed to count resources',
                    array('status' => 500)
                );
            }

            // Fetch items
            $query = $wpdb->prepare(
                "SELECT * FROM {$table_name} 
                ORDER BY {$orderby}
                LIMIT %d OFFSET %d",
                $per_page,
                $offset
            );

            $items = $wpdb->get_results($query, ARRAY_A);

            if ($items === null) {
                return new WP_Error(
                    'resource_query_error',
                    'Failed to fetch resources',
                    array('status' => 500)
                );
            }

            return array(
                'items' => $items,
                'total' => (int) $total_items,
                'pages' => ceil($total_items / $per_page)
            );

        } catch (Exception $e) {
            return new WP_Error(
                'resource_error',
                $e->getMessage(),
                array('status' => 500)
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
