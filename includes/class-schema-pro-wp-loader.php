<?php
/**
 * Registrera alla handlingar och filter för pluginet.
 *
 * @link       https://example.com
 * @since      1.0.0
 *
 * @package    SchemaProWP
 * @subpackage SchemaProWP/includes
 */

/**
 * Registrera alla handlingar och filter för pluginet.
 *
 * Underhåll en lista över alla krokar som är registrerade i hela
 * pluginet, och registrera dem med WordPress API. Anropa funktionen
 * run för att köra listan av handlingar och filter.
 *
 * @package    SchemaProWP
 * @subpackage SchemaProWP/includes
 * @author     Ditt Namn <din@email.com>
 */
class SchemaProWP_Loader {

    /**
     * Arrayen av handlingar registrerade med WordPress.
     *
     * @since    1.0.0
     * @access   protected
     * @var      array    $actions    Arrayen av handlingar registrerade med WordPress.
     */
    protected $actions;

    /**
     * Arrayen av filter registrerade med WordPress.
     *
     * @since    1.0.0
     * @access   protected
     * @var      array    $filters    Arrayen av filter registrerade med WordPress.
     */
    protected $filters;

    /**
     * Arrayen av shortcodes registrerade med WordPress.
     *
     * @since    1.0.0
     * @access   protected
     * @var      array    $shortcodes    Arrayen av shortcodes registrerade med WordPress.
     */
    protected $shortcodes;

    /**
     * Initiera samlingarna som används för att underhålla handlingar och filter.
     *
     * @since    1.0.0
     */
    public function __construct() {

        $this->actions = array();
        $this->filters = array();
        $this->shortcodes = array();

    }

    /**
     * Lägg till en ny handling till samlingen som ska registreras med WordPress.
     *
     * @since    1.0.0
     * @param    string               $hook             Namnet på WordPress-handlingen som registreras.
     * @param    object               $component        En referens till den instans av objektet där handlingen är definierad.
     * @param    string               $callback         Namnet på funktionen definitionen av handlingen.
     * @param    int                  $priority         Valfri. Prioriteten vid vilken handlingen ska köras. Standard är 10.
     * @param    int                  $accepted_args    Valfri. Antalet argument som ska skickas till återanropet. Standard är 1.
     */
    public function add_action( $hook, $component, $callback, $priority = 10, $accepted_args = 1 ) {
        $this->actions = $this->add( $this->actions, $hook, $component, $callback, $priority, $accepted_args );
    }

    /**
     * Lägg till ett nytt filter till samlingen som ska registreras med WordPress.
     *
     * @since    1.0.0
     * @param    string               $hook             Namnet på WordPress-filtret som registreras.
     * @param    object               $component        En referens till den instans av objektet där filtret är definierat.
     * @param    string               $callback         Namnet på funktionen definitionen av filtret.
     * @param    int                  $priority         Valfri. Prioriteten vid vilken filtret ska köras. Standard är 10.
     * @param    int                  $accepted_args    Valfri. Antalet argument som ska skickas till återanropet. Standard är 1
     */
    public function add_filter( $hook, $component, $callback, $priority = 10, $accepted_args = 1 ) {
        $this->filters = $this->add( $this->filters, $hook, $component, $callback, $priority, $accepted_args );
    }

    /**
     * Lägg till en ny shortcode till samlingen som ska registreras med WordPress.
     *
     * @since    1.0.0
     * @param    string               $tag              Namnet på WordPress-shortcode som registreras.
     * @param    object               $component        En referens till den instans av objektet där shortcode är definierad.
     * @param    string               $callback         Namnet på funktionen definitionen av shortcode.
     */
    public function add_shortcode( $tag, $component, $callback ) {
        $this->shortcodes = $this->add( $this->shortcodes, $tag, $component, $callback, null, null );
    }

    /**
     * En hjälpfunktion som används för att registrera handlingar och krokar i en enda iteration.
     *
     * @since    1.0.0
     * @access   private
     * @param    array                $hooks            Arrayen av krokar som ska registreras med WordPress.
     * @param    string               $hook             Namnet på WordPress-registreringen.
     * @param    object               $component        En referens till den instans av objektet där funktionen är definierad.
     * @param    string               $callback         Namnet på funktionen definitionen.
     * @param    int                  $priority         Prioriteten vid vilken funktionen ska köras.
     * @param    int                  $accepted_args    Antalet argument som ska skickas till funktionen.
     * @return   array                                  Samlingen av handlingar och filter registrerade med WordPress.
     */
    private function add( $hooks, $hook, $component, $callback, $priority, $accepted_args ) {

        $hooks[] = array(
            'hook'          => $hook,
            'component'     => $component,
            'callback'      => $callback,
            'priority'      => $priority,
            'accepted_args' => $accepted_args
        );

        return $hooks;

    }

    /**
     * Registrera krokarna med WordPress.
     *
     * @since    1.0.0
     */
    public function run() {

        foreach ( $this->filters as $hook ) {
            add_filter( $hook['hook'], array( $hook['component'], $hook['callback'] ), $hook['priority'], $hook['accepted_args'] );
        }

        foreach ( $this->actions as $hook ) {
            add_action( $hook['hook'], array( $hook['component'], $hook['callback'] ), $hook['priority'], $hook['accepted_args'] );
        }

        foreach ( $this->shortcodes as $hook ) {
            add_shortcode( $hook['hook'], array( $hook['component'], $hook['callback'] ) );
        }

    }

}