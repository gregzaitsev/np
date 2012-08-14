<?php

class ent_category extends sm_ent {

    public function get( $id ) {
		$res = $this->db->query(
			array(
				"query" => "select
								  id
								, name
								, project_id
							from
								category
							where
								id = ?i(id)"
				,"binds" => array(
					  "id" => $id
				)
			)
		);
		if ( $res["affected_rows"] == 0 )
		{
			sm_debug::write("Category $id not found", 1);
			throw new sm_exception( "Category $id not found", 1 );
		}
		else
			return $res["result"][0];
    } // function get()

    public function getList( $project_id ) {
		$res = $this->db->query(
			array(
				"query" => "select
								  id
								, name
								, project_id
							from
								category
							where
								project_id = ?i(project_id)"
				,"binds" => array(
					"project_id" => $project_id
				)
			)
		);
		return $res["result"];
    } // function get()
	
	public function add($category) {
	
		sm_debug::write("Adding category: ".$category['name']);
		sm_debug::write("Project ID: ".$category['project_id']);
		
		if ($category['name'] == '') throw new sm_exception( "Category name is not specified" );
	
        try {
            $this->db->query(
                array(
                    "query" => "INSERT INTO
                                       category(
										  name
										, project_id
                                   )
                                   VALUES(
                                             ?s(name)
										   , ?i(project_id)
                                   )"
                    ,"binds" => array(
						"name" => $category['name']
						, "project_id" => $category['project_id']
					)
				)
			);
        } catch ( Exception $e ) {
			sm_debug::write("Adding category error: ".$e->getMessage());
            throw new sm_exception( $e->getMessage() );
        }
    } // function add()

	public function update($catID, $name) {
	
		sm_debug::write("Updating category ID: ".$catID, 7);
		sm_debug::write("New name: ".$name, 7);
		
        try {
            $this->db->query(
                array(
                    "query" => "UPDATE category
					            SET
									name=?s(name)
                                WHERE
                                   id=?i(id)"
                    ,"binds" => array(
						"id" => $catID
						, "name" => $name
					)
				)
			);
			
        } catch ( Exception $e ) {
			sm_debug::write("Updating category error: ".$e->getMessage());
            throw new sm_exception( $e->getMessage() );
        }
	}

}

?>
