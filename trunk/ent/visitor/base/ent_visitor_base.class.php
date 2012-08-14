<?php
/**
 * Class ent_visitor_base
 * 
 * @package ConcertTerminal
 * @version 1.0.0.
 * @category php5 only
 * @copyright Copyright &copy; November 2007, UNITECSYS
 *
 */

class ent_visitor_base {


    protected static $instance = null;


    /**
     * Constructor
     */
    protected function __construct() {

    } // function __construct()



    /**
     * Get instance of ent_visitor
     *
     * @return object  - instance of ent_visitor
     */
    public static function getInstance() {

        if( !isset( self::$instance ) ) {
            self::$instance =
                isset( $_SESSION["Visitor"] )
                ? $_SESSION["Visitor"]
                : new ent_visitor()
            ;
        }
        return self::$instance;
    
    } // function getInstance()


} // class ent_visitor_base

?>
