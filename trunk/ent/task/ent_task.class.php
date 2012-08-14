<?php

class ent_task extends sm_ent {

	const HISTORY_ACTION_PROGRESS_UPD = 1;
	const HISTORY_ACTION_OWNER_UPD = 2;
	const HISTORY_ACTION_DESCRIPTION_UPD = 3;
	const HISTORY_ACTION_STEPS_UPD = 4;
	const HISTORY_ACTION_STATUS_UPD = 5;
	const HISTORY_ACTION_NAME_UPD = 6;
	const HISTORY_ACTION_DELETE = 7;
	const HISTORY_ACTION_RELEASE_UPD = 8;
	const HISTORY_ACTION_CATEGORY_UPD = 10;
	const HISTORY_ACTION_ESTIMATE_UPD = 11;
	const HISTORY_ACTION_ESTIMATE_PREC_UPD = 12;
	const HISTORY_ACTION_NOTE_ADD = 13;
	const HISTORY_ACTION_NOTE_UPD = 14;
	const HISTORY_ACTION_CREATED = 15;

    public function get( $id ) {
		$res = $this->db->query(
			array(
				"query" => "select
								  id
								, name
								, status_id
								, project_id
								, owner_id
								, release_id
								, category_id
								, priority
								, description
								, teststeps
								, DATE_FORMAT(timest, '%H:%i') as timest
								, start_time
								, end_time
								, timest_precision
								, progress
							from
								task
							where
								id = ?i(id)"
				,"binds" => array(
					  "id" => $id
				)
			)
		);
		if ( $res["affected_rows"] == 0 )
		{
			sm_debug::write("Task $id not found", 1);
			return null;
		}
		else
			return $res["result"][0];
    } // function get()

    public function getList( $project_id, $release_id, $category_id, $owner_id, $status_id ) {
	
		sm_debug::write("Getting task list: $project_id, $release_id, $category_id, $owner_id, $status_id", 7);

		try {
			$query = "select
						  t.id
						, t.name
						, t.status_id
						, t.project_id
						, t.owner_id
						, u.firstname as owner_first_name
						, u.lastname as owner_last_name
						, t.release_id
						, t.category_id
						, t.priority
						, t.description
						, t.teststeps
						, DATE_FORMAT(t.timest, '%H:%i') as timest
						, t.timest_precision
						, t.end_time
						, t.start_time
						, t.progress
					from
						task t left join user u on u.id = t.owner_id
					where
						1 = 1";
						
			if ($project_id!=0) $query .= " and project_id = ?i(project_id)";
			if ($release_id!=0) $query .= " and release_id = ?i(release_id)";
			if ($category_id!=0) $query .= " and category_id = ?i(category_id)";
			if ($owner_id!=0) $query .= " and owner_id = ?i(owner_id)";
			if ($status_id!=0) $query .= " and status_id = ?i(status_id)";
			else $query .= " and progress < 100";

			//sm_debug::write("Query: $query", 7);
		
			$res = $this->db->query(
				array(
					"query" => $query
					,"binds" => array(
						"project_id" => $project_id
						, "release_id" => $release_id
						, "category_id" => $category_id
						, "owner_id" => $owner_id
						, "status_id" => $status_id
					)
				)
			);
			
			sm_debug::write("Tasks found: ".$res["affected_rows"], 7);
			
			return $res["result"];
		} catch ( Exception $e ) {
			sm_debug::write("Getting task list error: ".$e->getMessage());
            throw new sm_exception( $e->getMessage() );
        }

    } // function get()
	
    public function getOpenList( $project_id, $release_id, $category_id, $owner_id ) {
	
		sm_debug::write("Getting open task list: $project_id, $release_id, $category_id, $owner_id", 7);

		try {
			$query = "select
						  t.id
						, t.name
						, t.status_id
						, t.project_id
						, t.owner_id
						, u.firstname as owner_first_name
						, u.lastname as owner_last_name
						, t.release_id
						, t.category_id
						, t.priority
						, t.description
						, t.teststeps
						, DATE_FORMAT(t.timest, '%H:%i') as timest
						, t.timest_precision
						, t.end_time
						, t.start_time
						, t.progress
					from
						task t left join user u on u.id = t.owner_id
					where
						status_id != 5";
						
			if ($project_id!=0) $query .= " and project_id = ?i(project_id)";
			if ($release_id!=0) $query .= " and release_id = ?i(release_id)";
			if ($category_id!=0) $query .= " and category_id = ?i(category_id)";
			if ($owner_id!=0) $query .= " and owner_id = ?i(owner_id)";
			else $query .= " and progress < 100";

			//sm_debug::write("Query: $query", 7);
		
			$res = $this->db->query(
				array(
					"query" => $query
					,"binds" => array(
						"project_id" => $project_id
						, "release_id" => $release_id
						, "category_id" => $category_id
						, "owner_id" => $owner_id
					)
				)
			);
			
			sm_debug::write("Tasks found: ".$res["affected_rows"], 7);
			
			return $res["result"];
		} catch ( Exception $e ) {
			sm_debug::write("Getting task list error: ".$e->getMessage());
            throw new sm_exception( $e->getMessage() );
        }

    } // function get()

	
	public function add($task) {
	
		sm_debug::write("Adding task: ".$task['name']);
		sm_debug::write("Project ID: ".$task['project_id']);
		sm_debug::write("Priority: ".$task['priority']);
		sm_debug::write("Owner ID: ".$task['owner_id']);
		sm_debug::write("Release ID: ".$task['release_id']);
		sm_debug::write("Category ID: ".$task['category_id']);

        try {
            $this->db->query(
                array(
                    "query" => "INSERT INTO
                                       task(
											name
											, status_id
											, project_id
											, owner_id
											, release_id
											, category_id
											, priority
                                   )
                                   VALUES(
                                             ?s(name)
										   , '1'
										   , ?i(project_id)
										   , ?i(owner_id)
										   , ?i(release_id)
										   , ?i(category_id)
										   , ?i(priority)
                                   )"
                    ,"binds" => array(
						"name" => $task['name']
						, "project_id" => $task['project_id']
						, "release_id" => $task['release_id']
						, "category_id" => $task['category_id']
						, "owner_id" => $task['owner_id']
						, "priority" => $task['priority']
					)
				)
			);
			$newTaskId = $this->db->get_last_inserted_id();
			$this->updateTaskHistory($newTaskId, ent_task::HISTORY_ACTION_CREATED, $task['name']);

        } catch ( Exception $e ) {
			sm_debug::write("Adding task error: ".$e->getMessage());
            throw new sm_exception( $e->getMessage() );
        }
		
		// Send notifications
		$n = new ent_subscription;
		$n->sendNotifications($task['project_id'], "New Task name: ".$task['name'], "New Task Added");

    } // function add()

	public function updateDesc($taskID, $description) {
	
		sm_debug::write("Updating description for task ID: ".$taskID, 7);
		sm_debug::write("New description: ".$description, 7);
		
		
        try {
            $this->db->query(
                array(
                    "query" => "UPDATE task
					            SET
									description=?s(description)
                                WHERE
                                   id=?i(id)"
                    ,"binds" => array(
						"id" => $taskID
						, "description" => $description
					)
				)
			);
			
			$this->updateTaskHistory($taskID, ent_task::HISTORY_ACTION_DESCRIPTION_UPD, $description);
			
        } catch ( Exception $e ) {
			sm_debug::write("Updating task error: ".$e->getMessage());
            throw new sm_exception( $e->getMessage() );
        }
	}
	
	public function updateSteps($taskID, $teststeps) {
	
		sm_debug::write("Updating steps to test for task ID: ".$taskID, 7);
		
        try {
            $this->db->query(
                array(
                    "query" => "UPDATE task
					            SET
									teststeps=?s(teststeps)
                                WHERE
                                   id=?i(id)"
                    ,"binds" => array(
						"id" => $taskID
						, "teststeps" => $teststeps
					)
				)
			);
			
			$this->updateTaskHistory($taskID, ent_task::HISTORY_ACTION_STEPS_UPD, $teststeps);
			
        } catch ( Exception $e ) {
			sm_debug::write("Updating task error: ".$e->getMessage());
            throw new sm_exception( $e->getMessage() );
        }
	}

	public function updateStatus($taskID, $status_id) {
	
		sm_debug::write("Updating status for task ID: ".$taskID, 7);
		sm_debug::write("New status ID: ".$status_id, 7);
		
        try {
            $this->db->query(
                array(
                    "query" => "UPDATE task
					            SET
									status_id=?s(status_id)
                                WHERE
                                   id=?i(id)"
                    ,"binds" => array(
						"id" => $taskID
						, "status_id" => $status_id
					)
				)
			);
			
			// Send notifications
			$task = $this->get($taskID);
			$taskname = $task['name'];
			
			$s = new ent_status;
			$status = $s->get($status_id);
			$statusName = $status['name'];
			
			$n = new ent_subscription;
			$n->sendNotifications($task['project_id'], "Task ID: $taskID\nTask Name: $taskname\nNew status: $statusName", "Status updated in task (ID:$taskID)");
			
			$this->updateTaskHistory($taskID, ent_task::HISTORY_ACTION_STATUS_UPD, $status_id);
			
        } catch ( Exception $e ) {
			sm_debug::write("Updating task error: ".$e->getMessage());
            throw new sm_exception( $e->getMessage() );
        }
	}

	public function updateName($taskID, $name) {
	
		sm_debug::write("Updating task ID: ".$taskID, 7);
		sm_debug::write("New name: ".$name, 7);
		
        try {
            $this->db->query(
                array(
                    "query" => "UPDATE task
					            SET
									name=?s(name)
                                WHERE
                                   id=?i(id)"
                    ,"binds" => array(
						"id" => $taskID
						, "name" => $name
					)
				)
			);
			
			$this->updateTaskHistory($taskID, ent_task::HISTORY_ACTION_NAME_UPD, $name);
			
        } catch ( Exception $e ) {
			sm_debug::write("Updating task error: ".$e->getMessage());
            throw new sm_exception( $e->getMessage() );
        }
	}

	public function delete($id) {
	
		sm_debug::write("Deleting task: ".$id, 7);

        try {
            $this->db->query(
                array(
                    "query" => "DELETE from task
                                WHERE id=?i(id)"
                    ,"binds" => array(
						"id" => $id
					)
				)
			);
			
            $this->db->query(
                array(
                    "query" => "DELETE from note
                                WHERE task_id=?i(id)"
                    ,"binds" => array(
						"id" => $id
					)
				)
			);
			
			$this->updateTaskHistory($taskID, ent_task::HISTORY_ACTION_DELETE, "");

        } catch ( Exception $e ) {
			sm_debug::write("Updating task error: ".$e->getMessage());
            throw new sm_exception( $e->getMessage() );
        }

	}

	public function updateRelease($taskID, $release_id) {
	
		sm_debug::write("Updating release for task ID: ".$taskID, 7);
		sm_debug::write("New release ID: ".$release_id, 7);
		
		// Get release date. If before current, do not update
		$r = new ent_release;
		$release = $r->get($release_id);
		$release_date = strtotime($release['date']);
		$now = time();
		if ($release_date < $now) {
			sm_debug::write("Error, release date is in the past.", 5);
            throw new sm_exception( "Error: Cannot assign a task to a release in the past." );
		}
		
		try {
            $this->db->query(
                array(
                    "query" => "UPDATE task
					            SET
									release_id=?s(release_id)
                                WHERE
                                   id=?i(id)"
                    ,"binds" => array(
						"id" => $taskID
						, "release_id" => $release_id
					)
				)
			);
			
			$this->updateTaskHistory($taskID, ent_task::HISTORY_ACTION_RELEASE_UPD, $release_id);
			
        } catch ( Exception $e ) {
			sm_debug::write("Updating task error: ".$e->getMessage(), 1);
            throw new sm_exception( $e->getMessage() );
        }
	}

	public function updateOwner($taskID, $owner_id) {
	
		sm_debug::write("Updating owner for task ID: ".$taskID, 7);
		sm_debug::write("New owner ID: ".$release_id, 7);
		
        try {
            $this->db->query(
                array(
                    "query" => "UPDATE task
					            SET
									owner_id=?s(owner_id)
                                WHERE
                                   id=?i(id)"
                    ,"binds" => array(
						"id" => $taskID
						, "owner_id" => $owner_id
					)
				)
			);
			
			$this->updateTaskHistory($taskID, ent_task::HISTORY_ACTION_OWNER_UPD, $owner_id);
			
        } catch ( Exception $e ) {
			sm_debug::write("Updating task error: ".$e->getMessage());
            throw new sm_exception( $e->getMessage() );
        }
	}
	
	public function updateCategory($taskID, $category_id) {
	
		sm_debug::write("Updating category for task ID: ".$taskID, 7);
		sm_debug::write("New category ID: ".$category_id, 7);
		
        try {
            $this->db->query(
                array(
                    "query" => "UPDATE task
					            SET
									category_id=?s(category_id)
                                WHERE
                                   id=?i(id)"
                    ,"binds" => array(
						"id" => $taskID
						, "category_id" => $category_id
					)
				)
			);
			
			$this->updateTaskHistory($taskID, ent_task::HISTORY_ACTION_CATEGORY_UPD, $category_id);
			
        } catch ( Exception $e ) {
			sm_debug::write("Updating task error: ".$e->getMessage());
            throw new sm_exception( $e->getMessage() );
        }
	}
	
	public function updateEstimate($taskID, $timest, $precision) {
	
		sm_debug::write("Updating time estimate for task ID: ".$taskID, 7);
		sm_debug::write("New time estimate: ".$timest, 7);
		sm_debug::write("New precision: ".$precision, 7);
		
        try {
            $this->db->query(
                array(
                    "query" => "UPDATE task
					            SET
									timest=?s(timest)
									,timest_precision=?s(prec)
                                WHERE
                                   id=?i(id)"
                    ,"binds" => array(
						"id" => $taskID
						, "timest" => $timest
						, "prec" => $precision
					)
				)
			);
			
			$this->updateTaskHistory($taskID, ent_task::HISTORY_ACTION_ESTIMATE_UPD, $timest);
			$this->updateTaskHistory($taskID, ent_task::HISTORY_ACTION_ESTIMATE_PREC_UPD, $precision);
			
        } catch ( Exception $e ) {
			sm_debug::write("Updating task error: ".$e->getMessage());
            throw new sm_exception( $e->getMessage() );
        }
	}

	public function updateProgress($taskID, $progress) {
	
		sm_debug::write("Updating progress for task ID: ".$taskID, 7);
		sm_debug::write("New progress: ".$progress, 7);
		
        try {
            $this->db->query(
                array(
                    "query" => "UPDATE task
					            SET
									progress=?s(progress)
                                WHERE
                                   id=?i(id)"
                    ,"binds" => array(
						"id" => $taskID
						, "progress" => $progress
					)
				)
			);
			
			$this->updateTaskHistory($taskID, ent_task::HISTORY_ACTION_PROGRESS_UPD, $progress);
			
        } catch ( Exception $e ) {
			sm_debug::write("Updating task error: ".$e->getMessage());
            throw new sm_exception( $e->getMessage() );
        }
	}

	public function updateTaskHistory($taskID, $action_id, $fieldContent) {
		try {
		
			sm_debug::write("Updating task history: taskID = $taskID, action_id = $action_id, fieldContent = $fieldContent", 7);
		
			$user_id = ent_visitor::getInstance()->user_id;
			$fieldName = '';
			switch ($action_id) {
			case ent_task::HISTORY_ACTION_PROGRESS_UPD:     $fieldName = 'progress'; break;
			case ent_task::HISTORY_ACTION_OWNER_UPD:        $fieldName = 'owner_id'; break;
				
			case ent_task::HISTORY_ACTION_DESCRIPTION_UPD:  $fieldName = 'description'; break;
			case ent_task::HISTORY_ACTION_STEPS_UPD:        $fieldName = 'steps'; break;
			case ent_task::HISTORY_ACTION_STATUS_UPD:       $fieldName = 'status_id'; break;
			case ent_task::HISTORY_ACTION_NAME_UPD:         $fieldName = 'name'; break;
			//case ent_task::HISTORY_ACTION_DELETE:         $fieldName = 'owner_id'; break;
			case ent_task::HISTORY_ACTION_RELEASE_UPD:      $fieldName = 'release_id'; break;
			case ent_task::HISTORY_ACTION_CATEGORY_UPD:     $fieldName = 'category_id'; break;
			case ent_task::HISTORY_ACTION_ESTIMATE_UPD:     $fieldName = 'timest'; break;
			case ent_task::HISTORY_ACTION_ESTIMATE_PREC_UPD:  $fieldName = 'timest_precision'; break;
			case ent_task::HISTORY_ACTION_NOTE_ADD:         $fieldName = 'note_id'; break;
			case ent_task::HISTORY_ACTION_NOTE_UPD:         $fieldName = 'note_id'; break;
			case ent_task::HISTORY_ACTION_CREATED:          $fieldName = 'name'; break;
			default: return;
			}

            $this->db->query(
                array(
                    "query" => "INSERT INTO
                                       task_history(
											task_id
											, user_id
											, dto
											, action_id
											, ".$fieldName."
                                   )
                                   VALUES(
                                             ?i(tid)
										   , ?i(user_id)
										   , now()
										   , ?i(action_id)
										   , ?s(newval)
                                   )"
                    ,"binds" => array(
						"tid" => $taskID
						, "user_id" => $user_id
						, "action_id" => $action_id
						, "newval" => $fieldContent
					)
				)
			);

        } catch ( Exception $e ) {
			sm_debug::write("Updating task error: ".$e->getMessage());
            throw new sm_exception( $e->getMessage() );
        }

	}

	public function getTaskHistory($taskID) {	
	
        try {
            $res = $this->db->query(
                array(
                    "query" => "SELECT
									user_id
									, dto
									, action_id
									, description
									, name
									, steps
									, release_id
									, status_id
									, progress
									, owner_id
									, category_id
									, timest
									, timest_precision
									, priority_id
									, note_id
					            FROM
									task_history
                                WHERE
                                   task_id=?i(tid)
								ORDER BY dto asc"
                    ,"binds" => array(
						"tid" => $taskID
					)
				)
			);
			
			if ( $res["affected_rows"] == 0 )
			{
				sm_debug::write("Task $id not found", 1);
				return null;
			}
			else
				return $res["result"];
			
        } catch ( Exception $e ) {
			sm_debug::write("Getting task history error: ".$e->getMessage());
            throw new sm_exception( $e->getMessage() );
        }
	
	}

	public function getRecentHistory() {	
	
        try {
            $res = $this->db->query(
                array(
                    "query" => "SELECT
									id
									, task_id
									, user_id
									, dto
									, action_id
									, description
									, name
									, steps
									, release_id
									, status_id
									, progress
									, owner_id
									, category_id
									, timest
									, timest_precision
									, priority_id
									, note_id
					            FROM
									task_history
                                WHERE
                                   dto >= DATE_SUB(now(),INTERVAL 7 DAY)
								ORDER BY dto asc"
				)
			);
			
			if ( $res["affected_rows"] == 0 )
			{
				sm_debug::write("Records not found", 1);
				return null;
			}
			else
				return $res["result"];
			
        } catch ( Exception $e ) {
			sm_debug::write("Getting history error: ".$e->getMessage());
            throw new sm_exception( $e->getMessage() );
        }
	
	}
	
	
	public function addDependency($taskID, $depid) {
	
		sm_debug::write("Adding dependency $depid for task ID: ".$taskID, 7);
		
        try {
            $this->db->query(
                array(
                    "query" => "INSERT INTO dependency (task1_id, task2_id, type_id)
					            VALUES (?i(task1_id), ?i(task2_id), 0)"
                    ,"binds" => array(
						"task2_id" => $taskID
						,"task1_id" => $depid
					)
				)
			);
			
			//$this->updateTaskHistory($taskID, ent_task::HISTORY_ACTION_DESCRIPTION_UPD, $description);
			
        } catch ( Exception $e ) {
			sm_debug::write("Updating task error: ".$e->getMessage());
            throw new sm_exception( $e->getMessage() );
        }
	}
	
	public function updateLiveline($taskID, $dt) {
	
		sm_debug::write("Updating liveline to $dt for task ID: ".$taskID, 7);
		
        try {
			if ($dt != null) {
				$this->db->query(
					array(
						"query" => "UPDATE task
									SET
										start_time=?s(dt)
									WHERE
									   id=?i(id)"
						,"binds" => array(
							"id" => $taskID
							, "dt" => $dt
						)
					)
				);
			} else {
				$this->db->query(
					array(
						"query" => "UPDATE task
									SET
										start_time=null
									WHERE
									   id=?i(id)"
						,"binds" => array(
							"id" => $taskID
						)
					)
				);
			}
			
        } catch ( Exception $e ) {
			sm_debug::write("Updating task error: ".$e->getMessage());
            throw new sm_exception( $e->getMessage() );
        }
	}
	
	public function updateDeadline($taskID, $dt) {
	
		sm_debug::write("Updating deadline to $dt for task ID: ".$taskID, 7);
		
        try {
			if ($dt != null) {
				$this->db->query(
					array(
						"query" => "UPDATE task
									SET
										end_time=?s(dt)
									WHERE
									   id=?i(id)"
						,"binds" => array(
							"id" => $taskID
							, "dt" => $dt
						)
					)
				);
			} else {
				$this->db->query(
					array(
						"query" => "UPDATE task
									SET
										end_time=null
									WHERE
									   id=?i(id)"
						,"binds" => array(
							"id" => $taskID
						)
					)
				);
			}
			
        } catch ( Exception $e ) {
			sm_debug::write("Updating task error: ".$e->getMessage());
            throw new sm_exception( $e->getMessage() );
        }
	}
	
	public function getRepeat( $task_id ) {
		$res = $this->db->query(
			array(
				"query" => "select
								repeat_type
								, mask
								, start_dt
								, stop_dt
							from
								taskrepeat
							where
								task_id = ?i(task_id)"
				,"binds" => array(
					  "task_id" => $task_id
				)
			)
		);
		if ( $res["affected_rows"] == 0 )
		{
			return null;
		}
		else
			return $res["result"][0];
	}

	public function updateRepeat($taskID, $rtype, $mask) {
	
		sm_debug::write("Type: $rtype, mask: $mask, task ID: $taskID", 7);
		
        try {
			if ($rtype == '0') {
				$this->db->query(
					array(
						"query" => "DELETE from taskrepeat
									WHERE
									   task_id=?i(task_id)"
						,"binds" => array(
							"task_id" => $taskID
						)
					)
				);
			} else {
				$this->db->query(
					array(
						"query" => "INSERT into taskrepeat
										(task_id, repeat_type, mask)
									VALUES
										(?i(task_id), ?s(rtype), ?i(mask))
									ON DUPLICATE KEY UPDATE
										repeat_type=?s(rtype)
										,mask=?i(mask)"
						,"binds" => array(
							"task_id" => $taskID
							, "rtype" => $rtype
							, "mask" => $mask
						)
					)
				);
			}
        } catch ( Exception $e ) {
			sm_debug::write("Updating task error: ".$e->getMessage());
            throw new sm_exception( $e->getMessage() );
        }
	}

}

?>
