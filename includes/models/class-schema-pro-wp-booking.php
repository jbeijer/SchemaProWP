<?php
/**
 * Modellklass för bokningar
 *
 * @package    SchemaProWP
 * @subpackage SchemaProWP/includes/models
 */

class SchemaProWP_Booking extends SchemaProWP_Model {
    
    /**
     * Konstruktor
     */
    public function __construct() {
        parent::__construct();
        $this->table_name = 'schemaprowp_bookings';
    }

    /**
     * Hämta bokningar för en specifik resurs
     *
     * @param int   $resource_id Resurs-ID
     * @param array $args        Extra argument för filtrering
     * @return array
     */
    public function get_by_resource($resource_id, $args = array()) {
        $args['where']['resource_id'] = $resource_id;
        return $this->get_all($args);
    }

    /**
     * Hämta bokningar för en specifik användare
     *
     * @param int   $user_id Användar-ID
     * @param array $args    Extra argument för filtrering
     * @return array
     */
    public function get_by_user($user_id, $args = array()) {
        $args['where']['user_id'] = $user_id;
        return $this->get_all($args);
    }

    /**
     * Hämta bokningar för ett specifikt tidsintervall
     *
     * @param string $start_time Starttid (Y-m-d H:i:s format)
     * @param string $end_time   Sluttid (Y-m-d H:i:s format)
     * @param array  $args       Extra argument för filtrering
     * @return array
     */
    public function get_by_timespan($start_time, $end_time, $args = array()) {
        $table = $this->get_table_name();
        $where = "WHERE (start_time <= %s AND end_time >= %s)
                    OR (start_time <= %s AND end_time >= %s)";
        $values = array($end_time, $start_time, $end_time, $start_time);

        if (!empty($args['where'])) {
            foreach ($args['where'] as $field => $value) {
                $where .= " AND {$field} = %s";
                $values[] = $value;
            }
        }

        $sql = $this->db->prepare(
            "SELECT * FROM {$table} {$where} ORDER BY start_time ASC",
            $values
        );

        return $this->db->get_results($sql);
    }

    /**
     * Validera bokningsdata
     *
     * @param array $data Data att validera
     * @return true|WP_Error
     */
    public function validate($data) {
        $errors = new WP_Error();
        
        if (empty($data['resource_id'])) {
            $errors->add('empty_resource_id', __('Resource is required', 'schema-pro-wp'));
        }
        
        if (empty($data['user_id'])) {
            $errors->add('empty_user_id', __('User is required', 'schema-pro-wp'));
        }
        
        if (empty($data['start_time'])) {
            $errors->add('empty_start_time', __('Start time is required', 'schema-pro-wp'));
        }
        
        if (empty($data['end_time'])) {
            $errors->add('empty_end_time', __('End time is required', 'schema-pro-wp'));
        }
        
        // Validera att start_time är före end_time
        if (!empty($data['start_time']) && !empty($data['end_time'])) {
            $start = strtotime($data['start_time']);
            $end = strtotime($data['end_time']);
            
            if ($start >= $end) {
                $errors->add('invalid_timespan', __('End time must be after start time', 'schema-pro-wp'));
            }
        }
        
        // Kontrollera om resursen är tillgänglig för den valda tiden
        if (!empty($data['resource_id']) && !empty($data['start_time']) && !empty($data['end_time'])) {
            $table = $this->get_table_name();
            $existing = $this->db->get_var($this->db->prepare(
                "SELECT COUNT(*) FROM {$table}
                WHERE resource_id = %d
                AND id != %d
                AND status != 'cancelled'
                AND (
                    (start_time <= %s AND end_time >= %s)
                    OR (start_time <= %s AND end_time >= %s)
                )",
                array(
                    $data['resource_id'],
                    !empty($data['id']) ? $data['id'] : 0,
                    $data['end_time'],
                    $data['start_time'],
                    $data['end_time'],
                    $data['start_time']
                )
            ));
            
            if ($existing > 0) {
                $errors->add('resource_unavailable', __('Resource is not available for the selected time period', 'schema-pro-wp'));
            }
        }
        
        return $errors->has_errors() ? $errors : true;
    }

    /**
     * Skapa en ny bokning med validering
     *
     * @param array $data Bokningsdata
     * @return int|WP_Error
     */
    public function create($data) {
        $validation = $this->validate($data);
        if (is_wp_error($validation)) {
            return $validation;
        }
        
        // Sätt standardstatus om ingen angetts
        if (empty($data['status'])) {
            $data['status'] = 'pending';
        }
        
        return parent::create($data);
    }

    /**
     * Uppdatera en bokning med validering
     *
     * @param int   $id   Boknings-ID
     * @param array $data Bokningsdata
     * @return bool|WP_Error
     */
    public function update($id, $data) {
        // Hämta existerande data för att kunna validera
        $existing = $this->get($id);
        if (!$existing) {
            return new WP_Error('booking_not_found', __('Booking not found', 'schema-pro-wp'));
        }
        
        // Slå ihop existerande data med nya data för full validering
        $full_data = (array) $existing;
        foreach ($data as $key => $value) {
            $full_data[$key] = $value;
        }
        
        $validation = $this->validate($full_data);
        if (is_wp_error($validation)) {
            return $validation;
        }
        
        return parent::update($id, $data);
    }

    /**
     * Avboka en bokning (sätt status till cancelled)
     *
     * @param int $id Boknings-ID
     * @return bool
     */
    public function cancel($id) {
        return $this->update($id, array('status' => 'cancelled'));
    }
}
