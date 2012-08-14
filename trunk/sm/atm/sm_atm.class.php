<?php
/**
 * Class sm_atm
 *
 * @package ConcertTerminal
 * @version 1.0.0.
 * @category php5 only
 * @author GZ
 * @copyright 
 *
 */

function ajax_error_handler( $errno, $errstr, $errfile, $errline ) {
	sm_debug::write("Error: errstr: $errstr, errno: $errno, errfile: $errfile, errline: $errline", 1);
	$errtext = "PHP ERROR: $errstr, errno: $errno, errfile: $errfile, errline: $errline";
	echo "ajaxerror='".$errtext."'";
	return true;
}

class sm_atm extends sm_atm_base {

	/**
	 * Default acp parameters
	 *
	 * @var array
	 * @access protected
	 */
	protected $acp_parameters = array("sess" => "write");

	/**
	 * Database object
	 *
	 * @var object
	 * @access public
	 */
	public $db = NULL;
	
	private $display_called = false;

	/**
	 * Constructor
	 *
	 * @access public
	 * @param string $acp         - ACP name
	 * @param string $parameters  - remain of URL string (part after the ACP name)
	 * @author GZ
	 */
	function __construct( $acp, $parameters ) {

		sm_debug::write("Starting ATM: ".sm_config::$atm.", ACP: $acp", 7);
		parent::__construct( $acp, $parameters, $params );
				
		// Initialize an instance of sm_db 
		sm_config::$db = $this->db = new sm_db();
		$params = array_merge( $this->acp_parameters, $this->actions[sm_config::$acp] );

		if (isset($_REQUEST['rtype']) && ($_REQUEST['rtype'] == 'ajax')) {
		
			// Handle AJAX request
			sm_debug::write("Running AJAX request", 7);
			$data = array();
			$data['ajaxerror'] = "";
			
			// Set custom error handler
			set_error_handler( "ajax_error_handler",  E_ALL );

			// Check if user is logged in
			if ((!$this->visitor->user_id) && ( sm_config::$atm != "atm_login" )) {
				$data['ajaxerror'] = "ERROR: Please re-login";
				$data['userdata'] = "";
			} else {
				try {
					$ret = $this->{$params["name"]}( $parameters );
					if (isset($ret) && is_array($ret)) $data = $ret;
					else {
						$data['ajaxerror'] = "";
						$data['userdata'] = "";
					}
				} catch ( Exception $e ) {
					$data['ajaxerror'] = "ERROR: ".$e->getMessage();
					$data['userdata'] = "";
				}
			}
			
			if (!$this->display_called) $this->displayAJAXResult( $data );
		} else {
			// Handle normal request
			if (!$this->visitor->user_id) {
				if( sm_config::$atm != "atm_login" )
				{
					header("Location: /login/");
					exit();
				}
			}
			else if ((sm_config::$default_password == $this->visitor->password)
					&& (sm_config::$atm != "atm_login"))
			{
				header("Location: /login/changePwd");
				exit();
			} 
			if( !sm_config::$language )
				sm_config::$language = 'en';
			sm_config::$localization = $this->loadLocalization( sm_config::$language );
			$this->tpl->assign( "applicationTitle", sm_config::$applicationTitle );
			$this->tpl->assign( "applicationVersion", sm_config::$applicationVersion );
			$this->{$params["name"]}( $parameters );
		}
	} // function __construct()

	public function loadLocalization( $language = '' ) {
		if( !strlen( $language ) ){
			$language = sm_config::$language;
		}
		$localization = array(
			"en" => array(
				  "cancel"     => "cancel"
				, "Attention"  => "Attention"
				, "edit"  => "edit"
				, "add"  => "add"
				)
		);
		return isset( $localization[$language] ) ? $localization[$language] : $localization['en'] ;
	}

	private function displayAJAXResult( $data ) {

		$result = "";
	
		if (!isset($data['ajaxerror'])) {
			$data['ajaxerror'] = 0;
		}
		else if ($data['ajaxerror'] == "") {
			$data['ajaxerror'] = 0;
		}
		
		$result .= "ajaxerror='".$data['ajaxerror']."'";
		
		if (isset($data['userdata'])) {
			$result .= ";userdata='".$data['userdata']."'";
		}
		
		$this->tpl->assign( "result", $result );
		$this->tpl->display( "Result.tpl" );

		$this->display_called = true;
	}

	public function displayResult( $data ) {
		$result = "ajaxerror='displayResult is obsolete. Remove it.'";
		$this->tpl->assign( "result", $result );
		$this->tpl->display( "Result.tpl" );
		$this->display_called = true;
	}
	
	public function logout(){
		if ( $this->visitor->user_id ) {
			$session = new ent_session;
			$session->id = $this->visitor->session_id;
			$session->end();
			session_regenerate_id( true );
			$this->visitor->newInstance();
		}
	}
}
	
?>
