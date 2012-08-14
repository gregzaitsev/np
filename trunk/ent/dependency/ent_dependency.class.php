<?php

class ent_dependency extends sm_ent {

    public function depend_parents( $task_id ) {
	
		sm_debug::write("Getting tasks that task $task_id depends on.", 7);

		try {
			$query = "select
						  d.task1_id as task_id
						, d.type_id
					from
						dependency d left join task t on (t.id = d.task1_id)
					where
						d.task2_id = ?i(task2_id)
						and t.status_id != 5
					order by d.task1_id";
		
			$res = $this->db->query(
				array(
					"query" => $query
					,"binds" => array(
						"task2_id" => $task_id
					)
				)
			);
			
			sm_debug::write("Tasks found: ".$res["affected_rows"], 7);
			
			return $res["result"];
		} catch ( Exception $e ) {
			sm_debug::write("Getting task list error: ".$e->getMessage());
            throw new sm_exception( $e->getMessage() );
        }

    }
	
    public function depend_children( $task_id ) {
	
		sm_debug::write("Getting children of $task_id", 7);

		try {
			$query = "select
						  d.task2_id as task_id
						, d.type_id
						, t.timest as timest
					from
						dependency d left join task t on (t.id = d.task2_id)
					where
						d.task1_id = ?i(task1_id)
						and t.status_id != 5
					order by d.task2_id";
		
			$res = $this->db->query(
				array(
					"query" => $query
					,"binds" => array(
						"task1_id" => $task_id
					)
				)
			);
			
			sm_debug::write("Tasks found: ".$res["affected_rows"], 7);
			
			return $res["result"];
		} catch ( Exception $e ) {
			sm_debug::write("Getting task list error: ".$e->getMessage());
            throw new sm_exception( $e->getMessage() );
        }

    }


}

?>
