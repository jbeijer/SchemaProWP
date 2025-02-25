<?php
/**
 * Basklass för alla modeller i SchemaProWP
 *
 * @package    SchemaProWP
 * @subpackage SchemaProWP/includes/models
 */

abstract class SchemaProWP_Model {
    /**
     * WordPress databas global
     *
     * @var wpdb
     */
    protected $db;

    /**
     * Tabellnamn utan prefix
     *
     * @var string
     */
    protected $table_name;

    /**
     * Primärnyckel
     *
     * @var string
     */
    protected $primary_key = 'id';

    /**
     * Konstruktor
     */
    public function __construct() {
        global $wpdb;
        $this->db = $wpdb;
    }

    /**
     * Hämta ett objekt med specifikt ID
     *
     * @param int $id Objektets ID
     * @return object|null
     */
    public function get($id) {
        $table = $this->get_table_name();
        return $this->db->get_row(
            $this->db->prepare(
                "SELECT * FROM {$table} WHERE {$this->primary_key} = %d",
                $id
            )
        );
    }

    /**
     * Hämta alla objekt
     *
     * @param array $args Argument för filtrering och sortering
     * @return array
     */
    public function get_all($args = array()) {
        $defaults = array(
            'orderby' => $this->primary_key,
            'order' => 'DESC',
            'per_page' => 10,
            'page' => 1,
            'where' => array(),
        );

        $args = wp_parse_args($args, $defaults);
        $table = $this->get_table_name();
        
        // Build WHERE clause
        $where_conditions = array();
        $where_values = array();
        
        if (!empty($args['where'])) {
            foreach ($args['where'] as $field => $value) {
                $where_conditions[] = "{$field} = %s";
                $where_values[] = $value;
            }
        }
        
        // Calculate pagination
        $offset = ($args['page'] - 1) * $args['per_page'];
        
        // Get total count first
        $count_sql = "SELECT COUNT(*) FROM {$table}";
        if (!empty($where_conditions)) {
            $count_sql .= " WHERE " . implode(' AND ', $where_conditions);
            $count_sql = $this->db->prepare($count_sql, $where_values);
        }
        $total = (int) $this->db->get_var($count_sql);
        
        // Build main query
        $sql = "SELECT * FROM {$table}";
        if (!empty($where_conditions)) {
            $sql .= " WHERE " . implode(' AND ', $where_conditions);
        }
        
        // Add ordering
        $sql .= $this->db->prepare(" ORDER BY %s %s", 
            array($args['orderby'], $args['order'])
        );
        
        // Add pagination
        $sql .= " LIMIT %d OFFSET %d";
        $values = array_merge($where_values, array($args['per_page'], $offset));
        
        // Prepare and execute query
        $sql = $this->db->prepare($sql, $values);
        $items = $this->db->get_results($sql);
        
        return array(
            'items' => $items,
            'total' => $total,
            'page' => $args['page'],
            'per_page' => $args['per_page']
        );
    }

    /**
     * Skapa ett nytt objekt
     *
     * @param array $data Data att spara
     * @return int|false ID för det nya objektet eller false vid fel
     */
    public function create($data) {
        $table = $this->get_table_name();
        
        // Ta bort created_at och updated_at om de finns, dessa sätts automatiskt
        unset($data['created_at'], $data['updated_at']);
        
        $result = $this->db->insert($table, $data);
        
        if ($result === false) {
            return false;
        }
        
        return $this->db->insert_id;
    }

    /**
     * Uppdatera ett objekt
     *
     * @param int   $id   Objektets ID
     * @param array $data Data att uppdatera
     * @return bool
     */
    public function update($id, $data) {
        $table = $this->get_table_name();
        
        // Ta bort created_at och updated_at om de finns
        unset($data['created_at'], $data['updated_at']);
        
        return $this->db->update(
            $table,
            $data,
            array($this->primary_key => $id)
        ) !== false;
    }

    /**
     * Ta bort ett objekt
     *
     * @param int $id Objektets ID
     * @return bool
     */
    public function delete($id) {
        $table = $this->get_table_name();
        return $this->db->delete(
            $table,
            array($this->primary_key => $id)
        ) !== false;
    }

    /**
     * Hämta fullt tabellnamn med prefix
     *
     * @return string
     */
    protected function get_table_name() {
        return $this->db->prefix . $this->table_name;
    }

    /**
     * Validera data innan sparande
     *
     * @param array $data Data att validera
     * @return bool|WP_Error
     */
    abstract public function validate($data);
}
