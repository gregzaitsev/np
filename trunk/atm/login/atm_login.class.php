<?php
class atm_login extends sm_atm {

    protected $actions = array(
        "__default"        => array( "name" => "acp_showLogin" )
      , "login"            => array( "name" => "acp_showLogin")
      , "doLogin"          => array( "name" => "acp_doLogin")
      , "doLogout"         => array( "name" => "acp_doLogout")
      , "changePwd"        => array( "name" => "acp_changePwd")
      , "doChangePwd"      => array( "name" => "acp_doChangePwd")
    );

    /**
     * Constructor
     *
     * @access public
     * @param string $acp         - ACP name
     * @param string $parameters  - remain of URL string (part after the ACP name)
     */
    function __construct( $acp, $parameters ) {

        $this->tpl = new sm_tpl( __FILE__ );
        parent::__construct( $acp, $parameters );

    } // function __construct()


    function acp_showLogin( $parameters ) {

		if ( $this->visitor->user_id ) {
			session_regenerate_id( true );
			$this->visitor->newInstance();
		}

		// Load configuration relevant to login page
		sm_debug::write("Loading configuration for login page", 7);

		$this->tpl->assign("title", "Login");
		$this->tpl->assign("sub_title", "");
		$this->tpl->assign("show_help_button", "");
		$this->tpl->assign( "show_footer", "" );

		$this->tpl->display( "login.tpl" );

    } // function acp_showLogin()
	

    function doLogin( $parameters ) {

		$login = $_REQUEST["login"];
		$password = $_REQUEST["password"];
	
		sm_debug::write("doLogin: Login: $login", 7);
	
		if ( !$this->visitor->user_id ){

			$session = new ent_session;
			$session->begin();
			if ( isset( $_REQUEST["login"] ) && $_REQUEST["login"] ){
				if ( isset( $_REQUEST["password"] ) && $_REQUEST["password"] ){
				
					sm_debug::write("Login: Checking password", 7);
				
					$session->login = $_REQUEST["login"];
					$session->password = sha1( $_REQUEST["password"] );
					$session->updateLoginPassword();
					$user = new ent_user();
					
					sm_debug::write("debug 1", 7);
					
					if( $user->getByLogin( $_REQUEST["login"] ) ) {
						if (( $user->password == $_REQUEST["password"] )) {
					
							sm_debug::write("debug 2", 7);
					
							$this->visitor->login		= $user->login;
							$this->visitor->password	= $user->password;
							$this->visitor->user_id     = $session->user_id = $user->id;
							$this->visitor->session_id  = $session->id;
							$session->updateUserId();
							
							return null;
						} else {
							sm_debug::write("Wrong password", 7);
							throw new sm_exception( "Wrong password" );
						}
					}
					else{
						sm_debug::write("No such user", 7);
						throw new sm_exception( "User not found" );
					}
				}
				else {
					sm_debug::write("No password in POST", 7);
					throw new sm_exception( "Enter password" );
				}
			}
			else {
				sm_debug::write("No login in POST", 7);
				throw new sm_exception( "Enter login" );
			}
		}

    } // function acp_doLogin()

    function acp_doLogin( $parameters ) {

		$this->doLogin( $parameters );
		
    } // function acp_doLogin()

    function acp_doLogout( $parameters ) {

		$tmp = $this->visitor->login;
		sm_debug::write("user $tmp is going to logout now", 7);
		
		$this->logout();
		sm_debug::write("logged out", 7);

		header("Location: /login/");

    } // function acp_doLogout()
	
	function acp_changePwd() {
		$login = $this->visitor->login;
	
		$this->tpl->assign("title", "Change Password");
		$this->tpl->assign("sub_title", "");
		$this->tpl->assign("show_help_button", "");
		$this->tpl->assign( "show_footer", "" );
		$this->tpl->assign( "login", $login );

		$this->tpl->display( "changepwd.tpl" );
	}
	
	function acp_doChangePwd() {
		$login = $this->visitor->login;
		$passwordold = $_REQUEST["passwordold"];
		$passwordnew = $_REQUEST["passwordnew"];
		
		sm_debug::write("Changing password for user $login", 7);
		sm_debug::write("Old password: $passwordold", 7);
		sm_debug::write("New password: $passwordnew", 7);
	
		$u = new ent_user;
		$u->changePassword($login, $passwordold, $passwordnew);
		$this->visitor->password = $passwordnew;
	}
	
} // class atm_Login

?>
