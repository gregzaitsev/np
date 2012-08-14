<?php

class ent_release extends sm_ent {

    public function get( $id ) {
		$res = $this->db->query(
			array(
				"query" => "select
								  id
								, name
								, date
								, project_id
							from
								projrelease
							where
								id = ?i(id)"
				,"binds" => array(
					  "id" => $id
				)
			)
		);
		if ( $res["affected_rows"] == 0 )
		{
			sm_debug::write("Release $id not found", 1);
			throw new sm_exception( "Release $id not found", 1 );
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
								, date
								, project_id
							from
								projrelease
							where
								project_id = ?i(project_id)
							order by date asc"
				,"binds" => array(
					"project_id" => $project_id
				)
			)
		);
		return $res["result"];
    } // function get()

    public function getFutureList( $project_id ) {
		$res = $this->db->query(
			array(
				"query" => "select
								  id
								, name
								, date
								, project_id
							from
								projrelease
							where
								project_id = ?i(project_id)
								and date >= now()
							order by date asc"
				,"binds" => array(
					"project_id" => $project_id
				)
			)
		);
		return $res["result"];
    } // function get()

	public function add($release) {
	
		sm_debug::write("Adding release: ".$release['name']);
		sm_debug::write("Project ID: ".$release['project_id']);
		sm_debug::write("Date: ".$release['date']);
		
		if (!$this->is_date($release['date'])) throw new sm_exception( "Date is invalid. Release is not added." );
		if ($release['name'] == '') throw new sm_exception( "Name is not specified. Release is not added." );
	
        try {
            $this->db->query(
                array(
                    "query" => "INSERT INTO
                                       projrelease(
										  name
										, date
										, project_id
                                   )
                                   VALUES(
                                             ?s(name)
                                           , ?s(date)
										   , ?i(project_id)
                                   )"
                    ,"binds" => array(
						"name" => $release['name']
						, "date" => $release['date']
						, "project_id" => $release['project_id']
					)
				)
			);
        } catch ( Exception $e ) {
			sm_debug::write("Adding release error: ".$e->getMessage());
            throw new sm_exception( $e->getMessage() );
        }
    } // function add()
	
	private function is_date( $str ) 
	{ 
		$stamp = strtotime( $str ); 
		if (!is_numeric($stamp)) return FALSE; 
		$month = date( 'm', $stamp ); 
		$day   = date( 'd', $stamp ); 
		$year  = date( 'Y', $stamp ); 
		if (checkdate($month, $day, $year)) return TRUE; 
		return FALSE; 
	}

	public function update($relID, $name, $date) {
	
		sm_debug::write("Updating release ID: ".$relID, 7);
		sm_debug::write("New name: ".$name, 7);
		sm_debug::write("New date: ".$date, 7);
		
		// Check if the date is valid
		if ($this->is_date($date)) $validDate = 1;
		else $validDate = 0;
		
		sm_debug::write("UPDATE projrelease
					            SET
									name=?s(name)
									".(($validDate==1)?",date=?s(date)":"")." 
                                WHERE
                                   id=?i(id)", 7);
		
        try {
            $this->db->query(
                array(
                    "query" => "UPDATE projrelease
					            SET
									name=?s(name)
									".(($validDate==1)?",date=?s(date)":"").
                                "WHERE
                                   id=?i(id)"
                    ,"binds" => array(
						"id" => $relID
						, "name" => $name
						, "date" => $date
					)
				)
			);
			
        } catch ( Exception $e ) {
			sm_debug::write("Updating release error: ".$e->getMessage());
            throw new sm_exception( $e->getMessage() );
        }
		
		if ($validDate == 0) throw new sm_exception( "Date is invalid. Only name was updated." );
	}

	
}

?>
