<?php
/**
 * Abstract Class sm_atm_base
 * 
 * @package ConcertTerminal
 * @version 1.0.0.
 * @category php5 only
 * @copyright Copyright &copy; November 2007, UNITECSYS
 *
 */

abstract class sm_atm_base {


    /**
     * Default acp parameters
     *
     * @access private
     * @var array
     */
    protected $acp_parameters = array(
        "sess" => "no"
    );


    /**
     * Session object
     *
     * @access public
     * @var object
     */
    public $sess = NULL;


    /**
     * ent_visitor object
     *
     * @access public
     * @var object
     */
    public $visitor = NULL;


    /**
     * Processed parameters
     *
     * @access private
     * @var array
     */
    protected $params = array();


    /**
     * Constructor
     * 
     * @access public
     * @param string $acp         - ACP name
     * @param string $parameters  - remain of URL string (part after the ACP name)
     */
    function __construct( $acp, $parameters, &$params ) {
	
        if( !array_key_exists( $acp, $this->actions ) ) {
            if( $acp == "" ) {
                sm_config::$parameters = array();
            } else {
                array_unshift( $parameters, $acp );
            }
            sm_config::$acp = $acp = "__default";
        }

        $params = array_merge( $this->acp_parameters, $this->actions[$acp] );

        $this->sess = sm_sess::getInstance( $params['sess'] );
		//sm_debug::write("Session loaded/created: session_id = ".session_id(), 7);

        $this->visitor = ent_visitor::getInstance();

    } // function __construct()


} // class sm_atm_base

?>
