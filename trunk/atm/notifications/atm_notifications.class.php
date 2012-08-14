<?php
class atm_notifications extends sm_atm {

	protected $actions = array(
		"__default"     => array( "name" => "acp_notifications",       "sess" => "write" )
		,"doSubscribe"   => array( "name" => "acp_doSubscribe",         "sess" => "write" )
		,"doUnsubscribe" => array( "name" => "acp_doUnsubscribe",       "sess" => "write" )
	);

    function __construct( $acp, $parameters ) {
    	$this->tpl = new sm_tpl( __FILE__ );
        parent::__construct( $acp, $parameters );

    } // function __construct()

    public function acp_notifications( $parameters ){

		$this->tpl->assign("title", "Notifications");

		// User ID
		$uid = $this->visitor->user_id;
		
		// Get Project List
		$p = new ent_project;
		$projects = $p->getList();
		
		// Get user subscriptions
		$s = new ent_subscription;
		$subscriptions = $s->getList($uid);
		
		// Merge subscriptions with projects
		foreach ($projects as $pkey => $pvalue) {
			$projects[$pkey]['subscribed'] = 0;
			foreach ($subscriptions as $skey => $svalue)
				if ($pvalue['id'] == $svalue['project_id'])
					$projects[$pkey]['subscribed'] = 1;
		}
			
		$this->tpl->assign("projects", $projects);
		$this->tpl->assign("uid", $uid);
		$this->tpl->display( "notifications.tpl" );

    }
	
	public function acp_doSubscribe(){
	
		$uid = $_REQUEST['uid'];
		$pid = $_REQUEST['pid'];

		$s = new ent_subscription;
		$s->subscribe($uid, $pid);
	}
	
	public function acp_doUnsubscribe(){
	
		$uid = $_REQUEST['uid'];
		$pid = $_REQUEST['pid'];

		$s = new ent_subscription;
		$s->unsubscribe($uid, $pid);
	}

} // class atm_default

?>
