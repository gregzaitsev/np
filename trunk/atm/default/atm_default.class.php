<?php
class atm_default extends sm_atm {

    protected $actions = array(
        "__default"  	=> array( "name" => "acp_default",       "sess" => "write" )
    );

    function __construct( $acp, $parameters ) {
    	$this->tpl = new sm_tpl( __FILE__ );
        parent::__construct( $acp, $parameters );

    } // function __construct()

    public function acp_default( $parameters ){

		if ($this->visitor->user_id) {
			$this->tpl->assign("title", "Main Menu");

			// Get Project List
			$p = new ent_project;
			$projects = $p->getList();
			
			// Populate project lead names
			$u = new ent_user;
			foreach ($projects as $key => $item) {
				$user = $u->get($item['lead_id']);
				$projects[$key]['leadname'] = $user['firstname']." ".$user['lastname'];
			}
			
			// Load online users
			$onlineusers = ent_session::getWhoIsOnline();
			$this->tpl->assign("onlineusers", $onlineusers);
			
			$this->tpl->assign("projects", $projects);
			
			if ($this->tpl->detectMobile())
				$this->tpl->display( "mainMenuM.tpl" );
			else $this->tpl->display( "mainMenu.tpl" );
			
		} else {
			header("Location: /login");
		}

    } // function acp_default()

} // class atm_default

?>
