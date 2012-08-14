<?php

class ent_note extends sm_ent {

    public function get( $id ) {
		$res = $this->db->query(
			array(
				"query" => "select
								  id
								, task_id
								, text
								, author_id
								, dt
							from
								note
							where
								id = ?i(id)"
				,"binds" => array(
					  "id" => $id
				)
			)
		);
		if ( $res["affected_rows"] == 0 )
		{
			sm_debug::write("Note $id not found", 1);
			throw new sm_exception( "Note $id not found", 1 );
		}
		else
			return $res["result"][0];
    } // function get()

    public function getList( $task_id ) {
		$res = $this->db->query(
			array(
				"query" => "select
								  n.id as id
								, n.task_id as task_id
								, n.text as text
								, n.author_id as author_id
								, n.dt as dt
								, u.firstname as firstname
								, u.lastname as lastname
							from
								note n
							join
								user u
							where
								task_id = ?i(task_id)
								and u.id = n.author_id
							order by n.dt asc"
				,"binds" => array(
					"task_id" => $task_id
				)
			)
		);
		return $res["result"];
    }
	
	public function add($note) {
	
		sm_debug::write("Adding note: ".$note['text']);
		sm_debug::write("Task ID: ".$note['task_id']);
		sm_debug::write("Author ID: ".$note['author_id']);
	
        try {
            $this->db->query(
                array(
                    "query" => "INSERT INTO
                                       note(
										  task_id
										, text
										, author_id
										, dt
                                   )
                                   VALUES(
                                             ?i(task_id)
                                           , ?s(text)
										   , ?i(author_id)
										   , now()
                                   )"
                    ,"binds" => array(
						"task_id" => $note['task_id']
						, "text" => $note['text']
						, "author_id" => $note['author_id']
					)
				)
			);
			$newNoteId = $this->db->get_last_inserted_id();

			// Add history record
			$t = new ent_task;
			$taskID = $note['task_id'];
			$t->updateTaskHistory($taskID, ent_task::HISTORY_ACTION_NOTE_ADD, $newNoteId);

			// Send notifications
			$n = new ent_subscription;
			$text = $note['text'];
			$t = new ent_task;
			$task = $t->get($taskID);
			$project_id = $task['project_id'];

			$n->sendNotifications($project_id, "Task ID: $taskID\nTask Name: ".$task['name']."\nNote added (ID:$newNoteId): $text", "New note in task (ID:$taskID) ".$task['name']);
			
        } catch ( Exception $e ) {
			sm_debug::write("Adding note error: ".$e->getMessage());
            throw new sm_exception( $e->getMessage() );
        }
    } // function add()


}

?>
