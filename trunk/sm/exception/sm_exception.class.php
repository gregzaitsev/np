<?php
/**
 * Class sm_exception
 * 
 * @package ConcertTerminal
 * @version 1.0.0.
 * @category php5 only
 * @copyright Copyright &copy; November 2007, UNITECSYS
 *
 */

class sm_exception extends Exception {


    /**
     * Stack of exceptions
     *
     * @access private
     * @var array (
     *          'message' => error message
     *        , 'code'    => error code
     *        , 'file'    => file name
     *        , 'line'    => line number
     *      )
     */
    private $stack = array();


    /**
     * Backtrace
     *
     * @access private
     * @var array
     */
    private $backtrace;


    /**
     * Constructor
     *
     * @access public
     * @param string $message   - exception message    
     * @param int    $code      - user defined exception code           
     * @param object $exception - class extends sm_exception
     */
    public function __construct( $message = '', $code = 0, $exception = null ){

		sm_debug::write("Exception: message: $message, code: $code", 1);
	
        parent::__construct( $message, $code );

        $this->stack['message'] = $this->getMessage();
        $this->stack['code'] 	= $this->getCode();
        $this->stack['file'] 	= $this->getFile();
        $this->stack['line'] 	= $this->getLine();
        $this->backtrace 		= debug_backtrace();

        if ( isset( $exception ) && ( $exception instanceof sm_exception ) ) {
            $this->stack['stack'] = $exception->getStack();
        }

    } // function __construct()


    /**
     * Returns stack property
     *
     * @access public
     * @return array()
     */
    public function getStack() {

        return $this->stack;

    } // function getStack()


    /**
     * Returns backtrace property
     *
     * @access public
     * @return array()
     */
    public function getBacktrace() {

        return $this->backtrace;

    } // function getBacktrace()


} // class sm_exception

?>
