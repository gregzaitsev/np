<?php

class ent_project extends sm_ent {

    public $items = array();

    public function get( $id ) {
		$res = $this->db->query(
			array(
				"query" => "select
								  id
								, name
								, dto
								, creator_id
								, lead_id
							from
								project
							where
								id = ?i(id)"
				,"binds" => array(
					  "id" => $id
				)
			)
		);
		if ( $res["affected_rows"] == 0 )
		{
			sm_debug::write("Project $id not found", 1);
			throw new sm_exception( "Project $id not found", 1 );
		}
		else
			return $res["result"][0];
    } // function get()

    public function getList( ) {
		$res = $this->db->query(
			array(
				"query" => "select
								  id
								, name
								, dto
								, creator_id
								, lead_id
							from
								project"
				,"binds" => array(
				)
			)
		);
		return $res["result"];
    } // function get()
	
	public function add($project) {
	
		sm_debug::write("Adding project: ".$project['name']);
		sm_debug::write("Creator ID: ".$project['creator_id']);
		sm_debug::write("Lead ID: ".$project['lead_id']);
	
        try {
            $this->db->query(
                array(
                    "query" => "INSERT INTO
                                       project(
										  name
										, dto
										, creator_id
										, lead_id
                                   )
                                   VALUES(
                                             ?s(name)
                                           , now()
										   , ?i(creator_id)
										   , ?i(lead_id)
                                   )"
                    ,"binds" => array(
						"name" => $project['name']
						, "creator_id" => $project['creator_id']
						, "lead_id" => $project['lead_id']
					)
				)
			);
        } catch ( Exception $e ) {
			sm_debug::write("Adding project error: ".$e->getMessage());
            throw new sm_exception( $e->getMessage() );
        }
    } // function add()

	public function update($project) {
	
		sm_debug::write("Updating project ID: ".$project['id'], 7);
		sm_debug::write("New project name: ".$project['name'], 7);
		sm_debug::write("New lead ID: ".$project['lead_id'], 7);
	
        try {
            $this->db->query(
                array(
                    "query" => "UPDATE project
					
					            SET
									name=?s(name)
									,lead_id=?i(lead_id)
                               WHERE
                                   id=?i(id)"
                    ,"binds" => array(
						"id" => $project['id']
						, "name" => $project['name']
						, "lead_id" => $project['lead_id']
					)
				)
			);
        } catch ( Exception $e ) {
			sm_debug::write("Updating project error: ".$e->getMessage());
            throw new sm_exception( $e->getMessage() );
        }
    }

}

?>
