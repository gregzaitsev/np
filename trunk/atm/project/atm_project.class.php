<?php
class atm_project extends sm_atm {

    protected $actions = array(
        "__default"   => array( "name" => "acp_add",       "sess" => "write" )
		,"add"        => array( "name" => "acp_add",       "sess" => "write" )
		,"doAdd"      => array( "name" => "acp_doAdd",     "sess" => "write" )
		,"edit"       => array( "name" => "acp_edit",      "sess" => "write" )
		,"doUpdate"   => array( "name" => "acp_doUpdate",  "sess" => "write" )
		,"tasks"      => array( "name" => "acp_tasks",     "sess" => "write" )
    );

    function __construct( $acp, $parameters ) {
    	$this->tpl = new sm_tpl( __FILE__ );
        parent::__construct( $acp, $parameters );

    } // function __construct()

    public function acp_add( $parameters ){

		$this->tpl->assign("title", "Project - New");
		
		// Load users for "lead" field
		$u = new ent_user;
		$users = $u->getList();
		
		$this->tpl->assign("users", $users);
		

		$this->tpl->display( "add.tpl" );

    }
	
	public function acp_doAdd() {
	
		$lead_id = substr($_REQUEST['lead_id'], 1);
	
		$project = array(
			'name' => $_REQUEST['pname']
			,'creator_id' => $this->visitor->user_id
			,'lead_id' => $lead_id
		);
		
		sm_debug::write("Adding project: ".$project['name'], 7);

		$p = new ent_project;
		$p->add($project);
	}
	
    public function acp_edit( $parameters ){

		$pid = $_REQUEST['pid'];
		
		$p = new ent_project;
		$project = $p->get($pid);
	
		$this->tpl->assign("title", "Project - Edit - ".$project['name']);
		
		// Load users for "lead" field
		$u = new ent_user;
		$users = $u->getList();
		$this->tpl->assign("users", $users);
		
		// Load project releases
		$r = new ent_release;
		$releases = $r->getList($pid);
		$this->tpl->assign("releases", $releases);
		
		// Load project categories
		$c = new ent_category;
		$categories = $c->getList($pid);
		$this->tpl->assign("categories", $categories);
		
		$this->tpl->assign("lead_id", $project['lead_id']);
		$this->tpl->assign("pname", $project['name']);
		$this->tpl->assign("pid", $pid);
		$this->tpl->display( "edit.tpl" );

    }

	public function acp_doUpdate() {
	
		$pid = $_REQUEST['pid'];
		$lead_id = substr($_REQUEST['lead_id'], 1);
		$pname = $_REQUEST['pname'];

		$project = array(
			'id' => $_REQUEST['pid']
			,'name' => $pname
			,'lead_id' => $lead_id
		);
		
		$p = new ent_project;
		$p->update($project);
	}
	
	public function acp_tasks($parameters) {
		
		$pid = $_REQUEST['pid'];
		
		$p = new ent_project;
		$project = $p->get($pid);
	
		$this->tpl->assign("title", "Project - Tasks - ".$project['name']);
		
		// Load project releases
		$r = new ent_release;
		$releases = $r->getList($pid);
		$this->tpl->assign("releases", $releases);
		$futurereleases = $r->getFutureList($pid);
		$this->tpl->assign("futurereleases", $futurereleases);

		// Load project categories
		$c = new ent_category;
		$categories = $c->getList($pid);
		$this->tpl->assign("categories", $categories);
		
		// Load users for "lead" field, mark project owner
		$u = new ent_user;
		$users = $u->getList();
		foreach ($users as $key => $item) 
			if ($item['id'] == $project['lead_id']) $users[$key]['lead'] = 1;
			else $users[$key]['lead'] = 0;
		$this->tpl->assign("users", $users);
		
		// Load statuses
		$s = new ent_status;
		$statuses = $s->getList();
		$this->tpl->assign("statuses", $statuses);
		
		$this->tpl->assign("pname", $project['name']);
		$this->tpl->assign("pid", $pid);
		$this->tpl->display( "tasks.tpl" );
	}
	
}

?>
