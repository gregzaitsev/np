<?php
/**
 * Abstract Class sm_ent_base
 * 
 * @package ConcertTerminal
 * @version 1.1.0.
 * @category php5 only
 * @copyright Copyright &copy; November 2007, UNITECSYS
 *
 */

abstract class sm_ent_base {

    /**
     * Database object
     *
     * @access public
     * @var object
     */
    protected $db = NULL;


    /**
     * Constructor
     * 
     * @access public
     */
    function __construct() {

        $this->db = new sm_db();

    } // function __construct()


} // class sm_ent_base

?>
