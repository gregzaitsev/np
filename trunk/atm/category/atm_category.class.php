<?php
class atm_category extends sm_atm {

    protected $actions = array(
        "__default"   => array( "name" => "acp_add",       "sess" => "write" )
		,"add"        => array( "name" => "acp_add",       "sess" => "write" )
		,"doAdd"      => array( "name" => "acp_doAdd",     "sess" => "write" )
		,"doUpdate"   => array( "name" => "acp_doUpdate",  "sess" => "write" )
    );

    function __construct( $acp, $parameters ) {
    	$this->tpl = new sm_tpl( __FILE__ );
        parent::__construct( $acp, $parameters );

    } // function __construct()

    public function acp_add( $parameters ){

		$this->tpl->display( "add.tpl" );

    }
	
	public function acp_doAdd() {
	
		$pid = $_REQUEST['pid'];
		$name = $_REQUEST['cname'];
	
		$category = array(
			'name' => $name
			,'project_id' => $pid
		);

		$c = new ent_category;
		$c->add($category);
	}

	public function acp_doUpdate() {
		$cid = substr($_REQUEST['id'], 1);
		$name = $_REQUEST['name'];

		$c = new ent_category;
		$c->update($cid, $name);
	}

}

?>
