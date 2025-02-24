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
            'limit' => 0,
            'offset' => 0,
            'where' => array(),
        );

        $args = wp_parse_args($args, $defaults);
        $table = $this->get_table_name();
        
        $where = '';
        $values = array();
        
        if (!empty($args['where'])) {
            $conditions = array();
            foreach ($args['where'] as $field => $value) {
                $conditions[] = "{$field} = %s";
                $values[] = $value;
            }
            $where = 'WHERE ' . implode(' AND ', $conditions);
        }

        $limit = '';
        if ($args['limit'] > 0) {
            $limit = 'LIMIT %d';
            if ($args['offset'] > 0) {
                $limit .= ' OFFSET %d';
                $values[] = $args['limit'];
                $values[] = $args['offset'];
            } else {
                $values[] = $args['limit'];
            }
        }

        $sql = "SELECT * FROM {$table} 
                {$where} 
                ORDER BY {$args['orderby']} {$args['order']}
                {$limit}";

        if (!empty($values)) {
            $sql = $this->db->prepare($sql, $values);
        }

        return $this->db->get_results($sql);
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
