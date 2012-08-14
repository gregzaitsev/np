<?php
class atm_release extends sm_atm {

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
		$release = array(
			'name' => $_REQUEST['rname']
			,'date' => $_REQUEST['rdate']
			,'project_id' => $_REQUEST['pid']
		);

		$r = new ent_release;
		$r->add($release);
	}
	
	public function acp_doUpdate() {
		$rid = substr($_REQUEST['id'], 1);
		$name = $_REQUEST['name'];
		$date = $_REQUEST['date'];
		
		$r = new ent_release;
		$r->update($rid, $name, $date);
	}
}
?>