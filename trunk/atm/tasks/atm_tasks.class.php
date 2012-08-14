<?php
class atm_tasks extends sm_atm {

	private $taskList = array();          // 1D array of all tasks
	private $scheduledTasks = array();    // 2D array of scheduled tasks for each user (by owner_id)
	private $scheduleEndTime = array();   // End of schedule by user. Calculated from scheduleStartTime
	private $scheduleStartTime;           // Start of all schedules in UNIX timestamp format (the number of seconds since January 1 1970 00:00:00 UTC) 

	// Schedule error detection
	private $insertIDs = array(); // IDs of tasks being added. If there is a loop in this array, schedule is impossible.
	private $troublemakers = "";

    protected $actions = array(
        "__default"  	=> array( "name" => "acp_schedule",       "sess" => "write" )
        ,"schedule"  	=> array( "name" => "acp_schedule",       "sess" => "write" )
    );

    function __construct( $acp, $parameters ) {
    	$this->tpl = new sm_tpl( __FILE__ );
        parent::__construct( $acp, $parameters );

    } // function __construct()
	
	private function outputdebugtasks() {
	
		foreach ($this->taskList as $val) {
			sm_debug::write("Name: ".$val['name'].", Calculated deadline: ".$val['calc_end'], 7);
		}
	
	}
	
	private function adjustParentDeadline($task) {
	
		sm_debug::write("Task ID: ". $task['id'], 7);
	
		if (!isset($task['calc_end'])) return;
		if ($task['calc_end'] == null) return;
	
		$d = new ent_dependency;

		// Find "depend_parents" tasks
		$dependencies = $d->depend_parents($task['id']);
	
		foreach ($dependencies as $dval) {
			foreach ($this->taskList as $tkey1 => $tval1) {
				if ($tval1['id'] == $dval['task_id']) {
				
					// New deadline is deadline of child - length of child
					$newDeadLine = strtotime($task['calc_end']) - strtotime($task['timest']) + strtotime('TODAY');
				
					// If new deadline is earlier then previously set (if it exists), move the deadline
					if (($tval1['calc_end'] != null) 
					  && ($newDeadLine < strtotime($tval1['calc_end'])))
					{
						sm_debug::write("Setting deadline for task (deadline was set before): ".$tval1['id'].", new deadline: ".date("Y-m-d H:i:s", $newDeadLine), 7);
						$this->taskList[$tkey1]['calc_end'] = date("Y-m-d H:i:s", $newDeadLine);
						$tval1['calc_end'] = date("Y-m-d H:i:s", $newDeadLine);
						$this->adjustParentDeadline($tval1);
					}
					else if ($tval1['calc_end'] == null) {
						sm_debug::write("Setting deadline for task (no deadline before): ".$tval1['id'].", new deadline: ".date("Y-m-d H:i:s", $newDeadLine), 7);
						$this->taskList[$tkey1]['calc_end'] = date("Y-m-d H:i:s", $newDeadLine);
						$tval1['calc_end'] = date("Y-m-d H:i:s", $newDeadLine);
						$this->adjustParentDeadline($tval1);
					}
				}
			}
		}
	}
	
	private function loadParentTree($parentArray, $task_id) {
	
		$d = new ent_dependency;
		$parList = $d->depend_parents($task_id);

		foreach ($parList as $pkey => $pval) {
			$parentArray[] = $pval['task_id'];
			$this->loadParentTree($parentArray, $pval['task_id']);
		}
		return $parentArray;
	}

	private function loadChildTree($childArray, $task_id) {
	
		$d = new ent_dependency;
		$parList = $d->depend_children($task_id);
		if (!isset($childArray['length'])) $childArray['length'] = 0;

		foreach ($parList as $pkey => $pval) {
			$childArray[] = $pval['task_id'];
			$childArray['length'] += strtotime($pval['timest']) - strtotime('TODAY');
			$this->loadChildTree($childArray, $pval['task_id']);
		}
		
		return $childArray;
	}

	private function sectoHHMMSS($seconds) {
		$h = intval($seconds / 3600);
		$m = intval($seconds / 60) - $h*60;
		$s = $seconds % 60;
		return sprintf("%02d:%02d:%02d", $h, $m, $s);
	}
	
	private function getEndOfScheduleUnixTime($userID) {
	
		// Initialize scheduleEndTime
		if (!isset($this->scheduleEndTime[$userID])) $this->scheduleEndTime[$userID] = 0;
		
		return $this->scheduleStartTime + $this->scheduleEndTime[$userID];
	}
	
	private function getTaskDurationS($task) {
		// Calculate task duration in s
		$hhmm = explode(':', $task['timest']);
		if (!isset($hhmm[2])) $hhmm[2] = 0;
		if (!isset($hhmm[0]) || !isset($hhmm[1]) || ($hhmm[1] >= 59) || ($hhmm[0] < 0) || ($hhmm[1] < 0) || ($hhmm[2] < 0) || ($hhmm[2] > 59))
			$taskDuration = 3600; // default to 1 hour
		else $taskDuration = $hhmm[0] * 3600 + $hhmm[1] * 60 + $hhmm[2];
		
		return $taskDuration;
	}
	
	private function addTaskToEndOfSchedule($task) {
	
		// Calculate task duration in s
		$taskDuration = $this->getTaskDurationS($task);
		sm_debug::write("Task duration: $taskDuration s", 7);
		
		// User ID
		$userID = $task['owner_id'];

		// Initialize scheduleEndTime and scheduleStartTime
		if (!isset($this->scheduleEndTime[$userID])) $this->scheduleEndTime[$userID] = 0;
		
		// Set task actual start and end time/date
		$actualStartTimestamp = $this->getEndOfScheduleUnixTime($userID);
		$newEndOfSchedule = $this->scheduleEndTime[$userID] + $taskDuration;
		$actualEndTimestamp = $actualStartTimestamp + $taskDuration;
		sm_debug::write("Old EndOfSchedule: ".$this->scheduleEndTime[$userID], 7);
		sm_debug::write("newEndOfSchedule: $newEndOfSchedule s", 7);
		sm_debug::write("actualStartTimestamp: $actualStartTimestamp", 7);
		
		$se1 = explode('#', date("Y-m-d#H:i:s", $actualStartTimestamp));
		$task['actual_start_date'] = $se1[0];
		$task['actual_start_time'] = $se1[1];
		
		$se2 = explode('#', date("Y-m-d#H:i:s", $actualEndTimestamp));
		$task['actual_end_date'] = $se2[0];
		$task['actual_end_time'] = $se2[1];
		
		// Add task to the end of queue
		if (!isset($scheduledTasks[$userID])) $scheduledTasks[$userID] = array();
		$this->scheduledTasks[$userID][] = $task;
		$this->scheduleEndTime[$userID] = $newEndOfSchedule;
		sm_debug::write("Task ".$task['id']."(".$task['name'].") added to the schedule. Actual start: ".date("Y-m-d H:i:s", $actualStartTimestamp).", actual end: ".date("Y-m-d H:i:s", $actualEndTimestamp), 7);
	}
	
	private function addDelay($waittime, $userID) {
	
		$delayTask = array();
		$delayTask['id'] = 0;
		$delayTask['name'] = 'Wait '.$waittime.' seconds';
		$delayTask['timest'] = $this->sectoHHMMSS($waittime);
		$delayTask['owner_first_name'] = "na";
		$delayTask['owner_last_name'] = "na";
		$delayTask['owner_id'] = $userID;
		
		$this->addTaskToEndOfSchedule($delayTask);
		sm_debug::write("Delay added. Timest = ".$delayTask['timest'], 7);
	}

	private function insertTaskToSchedule($task) {
	
		sm_debug::write("Inserting task ".$task['id'], 7);
		
		//////////////////////////////////
		// Detect errors
		
		if ($task['repeated'] == 0) $this->insertIDs[] = $task['id'];
		
		// detect loops in insertIDs of size 1 - count($taskList)
		$curSize = count($this->insertIDs);
		for ($i=1; $i<=count($this->taskList); $i++) {
			if ($i > $curSize/2) break;
			
			// check for loops of size i
			$loop = true;
			for ($j=$curSize-1; $j>=$curSize-$i-1; $j--) {
				if ($this->insertIDs[$j] != $this->insertIDs[$j-$i]) {
					$loop = false;
					break;
				}
			}
			if ($loop) {
				$this->troublemakers = "";
				$first = true;
				for ($k=0; $k<$i; $k++) {
					if (!$first) $this->troublemakers .= ",";
					else $first = false;
					$this->troublemakers .= $this->insertIDs[$curSize - $k - 1];
				}
				sm_debug::write("Looping on tasks: ".$this->troublemakers, 2);
				return false;
			}
		}
		

		// check if the task has deadline and liveline
		$hasDeadLine = false;
		if (isset($task['calc_end']) && ($task['calc_end'] != null)) $hasDeadLine = true;
		$hasLiveLine = false;
		if (isset($task['calc_start']) && ($task['calc_start'] != null)) $hasLiveLine = true;

		// Load parent tree, recalculate into parent IDs array
		$parList = array();
		$parList = $this->loadParentTree($parList, $task['id']);
		$parIDs = array();
		foreach ($parList as $pval)
			$parIDs[] = $pval['task_id'];

		// Load children tree, recalculate into children IDs array
		$chiList = array();
		$chiList = $this->loadChildTree($chiList, $task['id']);
		$chiIDs = array();
		foreach ($chiList as $pval)
			if (isset($pval['task_id'])) $chiIDs[] = $pval['task_id'];
		$insTreeLength = $chiList['length'];

		// Create user ID list
		$u = new ent_user;
		$userList = $u->getList();
		$userIDList = array();
		foreach ($userList as $user) $userIDList[] = $user['id'];
		
		// Make a tmp copy of the schedule
		$scheduleCopy = $this->scheduledTasks;

		// Initialize schedules
		foreach ($userIDList as $userID)
			$this->scheduledTasks[$userID] = array();
			
		// Initialize scheduleEndTime and scheduleStartTime
		foreach ($userIDList as $userID) 
			$this->scheduleEndTime[$userID] = 0;
		
		// Initialize queue sizes
		foreach ($userIDList as $userID) $queueSize[$userID] = 0;
		
		// Calculate the queue sizes
		foreach ($scheduleCopy as $userID => $userSchedule)
			$queueSize[$userID] = count($userSchedule);
		
		// Initialize "Current Task" indexes 
		$ct = array();
		foreach ($userIDList as $userID)
			$ct[$userID] = 0;
				

		// In the user's line, skip tasks until liveline: Build $this->scheduledTasks back up
		sm_debug::write("=== Skipping all tasks until ".$task['calc_start']." ===", 7);
		$unixLiveLine = time();
		$insUserID = $task['owner_id'];
		if ($hasLiveLine) {
			$unixLiveLine = strtotime($task['calc_start']);
			while ($ct[$insUserID] < $queueSize[$insUserID]) {
				if ($this->getEndOfScheduleUnixTime($insUserID) >= $unixLiveLine) break;
				$this->addTaskToEndOfSchedule($scheduleCopy[$insUserID][$ct[$insUserID]]);
				$ct[$insUserID]++;
			}
		}
		sm_debug::write("unixLiveLine = $unixLiveLine", 7);
		sm_debug::write("End of schedule = ".$this->getEndOfScheduleUnixTime($insUserID), 7);
		sm_debug::write("ct[insUserID] = ".$ct[$insUserID].", queueSize[insUserID] = ".$queueSize[$insUserID], 7);
		
		// Still did not reach the liveline? Add delay!
		if (($ct[$insUserID] >= $queueSize[$insUserID]) && ($unixLiveLine > $this->getEndOfScheduleUnixTime($insUserID)))
		{
			// Add delay task
			$waittime = $unixLiveLine - $this->getEndOfScheduleUnixTime($insUserID);
			$this->addDelay($waittime, $insUserID);
			sm_debug::write("Adding delay to match task ".$task['id']." liveline. Delay length = $waittime seconds", 7);
		}
		
		// Find the "ct" index of right-most parent in all queues
		foreach ($userIDList as $userID) {
			$rightMostParentIdx[$userID] = -1;
			for ($ct1 = $queueSize[$userID]-1; $ct1>=0; $ct1--) {
				if (in_array($scheduleCopy[$userID][$ct1]['id'], $parIDs)) {
					$rightMostParentIdx[$userID] = $ct1;
					break;
				}
			}
		}

		// Skip the depend-parents for this task in each queue
		// Add tasks to include all parents
		sm_debug::write("=== Skipping all parents in all lines. Parent count: ".count($parIDs)." ===", 7);
		foreach ($userIDList as $userID) {
			sm_debug::write("rightMostParentIdx = ".$rightMostParentIdx[$userID], 7);
			while ($ct[$userID] <= $rightMostParentIdx[$userID]) {
				$this->addTaskToEndOfSchedule($scheduleCopy[$userID][$ct[$userID]]);
				$ct[$userID]++;
			}
		}
	
		// Find the latest end time among all parents
		$parentsEndTime = time();
		foreach ($userIDList as $userID) {
			foreach ($this->scheduledTasks[$userID] as $t) {
				if (in_array($t['id'], $parIDs))
					if ($parentsEndTime < strtotime($t['actual_end_date']." ".$t['actual_end_time']))
						$parentsEndTime = strtotime($t['actual_end_date']." ".$t['actual_end_time']);
			}
		}
		sm_debug::write("End of all parents: ".date("Y-m-d H:i:s", $parentsEndTime), 7);
		sm_debug::write("Schedule End time: ".date("Y-m-d H:i:s", $this->getEndOfScheduleUnixTime($insUserID)), 7);
		
		// Make sure new task is inserted after all its parents in other lines time-wise as well.
		// That means 
		//     - skip other tasks in this line
		//     - insert a delay if necessary
		sm_debug::write("=== Skipping tasks until end of schedule in this line is >= End of Parents ===", 7);
		if ($parentsEndTime > $this->getEndOfScheduleUnixTime($insUserID)) {
		
			// Skip other tasks
			while (($parentsEndTime > $this->getEndOfScheduleUnixTime($insUserID))
				&& ($ct[$insUserID] < $queueSize[$insUserID])) {
				$this->addTaskToEndOfSchedule($scheduleCopy[$insUserID][$ct[$insUserID]]);
				$ct[$userID]++;
			}
			
			// Insert delay
			if ($parentsEndTime > $this->getEndOfScheduleUnixTime($insUserID)) {
				$waittime = $parentsEndTime - $this->getEndOfScheduleUnixTime($insUserID);
				$this->addDelay($waittime, $insUserID);
				sm_debug::write("Adding delay to wait until parents are done in the other lines for task ".$task['id'], 7);
			}
		}
		
		// At this point the task can be added, but skipping a few tasks with longer child tree will be optimal 
		sm_debug::write("=== Skipping all pier tasks with longer child tree in all lines. ===", 7);
		foreach ($userIDList as $userID) {
			while ($ct[$userID] < $queueSize[$userID]) {
				if (in_array($scheduleCopy[$userID][$ct[$userID]]['id'], $chiIDs)) {
					break;
				} else {
					// Load full child tree for the task that is about to be skipped
					$d = new ent_dependency;
					$childTree = array();
					$childTree = $this->loadChildTree($childTree, $scheduleCopy[$userID][$ct[$userID]]['id']);
					$skipTreeLength = $childTree['length'];
					sm_debug::write("Skip task tree length = $skipTreeLength, insTreeLength = $insTreeLength", 7);
					
					if ($skipTreeLength > $insTreeLength) {
						$this->addTaskToEndOfSchedule($scheduleCopy[$userID][$ct[$userID]]);
						$ct[$userID]++;
					} else break;
				}
			}
		}
		
		// Add the task at the end of current build of $this->scheduledTasks where it belongs (user $insUserID)
		sm_debug::write("=== Adding task! Hooray!!! ===", 7);
		$insLength = $this->getTaskDurationS($task);
		$insDeadline = 0;
		if (isset($task['end_time']) && ($task['end_time'] != 'null'))
			$insDeadline = strtotime($task['end_time']);
		if (($insDeadline == 0) || ($this->getEndOfScheduleUnixTime($insUserID) + $insLength <= $insDeadline)) {
			$this->addTaskToEndOfSchedule($task);
		} else {
			$this->troublemakers = $task['id'];
			sm_debug::write("Damn it. Too late. :((((( Task ".$task['id']." is not added due to its deadline: end of schedule: ".$scheduleEndTime[$insUserID].", insLength = $insLength, insDeadline = $insDeadline", 7);
			return false;
		}
		
		// Add the rest of tasks, but only if their deadlines are not broken. If they are broken, remove them from schedule and insert recursively after this loop
		// Also, remove all children of inserted task
		sm_debug::write("=== Adding the rest of tasks ===", 7);
		$removedTasks = array();
		foreach ($userIDList as $userID) {
			while ($ct[$userID] < $queueSize[$userID]) {
			
				if (in_array($scheduleCopy[$userID][$ct[$userID]]['id'], $chiIDs)) {
					$removedTasks[] = $scheduleCopy[$userID][$ct[$userID]];
					sm_debug::write("Removing task ".$scheduleCopy[$userID][$ct[$userID]]['id'], 7);
				} else {
					if (isset($scheduleCopy[$userID][$ct[$userID]]['calc_end']) && ($scheduleCopy[$userID][$ct[$userID]]['calc_end'] != null)) {
						// calculate the new deadline
						$newDeadLine = strtotime($this->getEndOfScheduleUnixTime($userID)." + ".$this->getTaskDurationS($scheduleCopy[$userID][$ct[$userID]])."seconds");
						if ($newDeadLine <= strtotime($scheduleCopy[$userID][$ct[$userID]]['calc_end'])) {
							$this->addTaskToEndOfSchedule($scheduleCopy[$userID][$ct[$userID]]);
						} else {
							$removedTasks[] = $scheduleCopy[$userID][$ct[$userID]];
							sm_debug::write("Removing task ".$scheduleCopy[$userID][$ct[$userID]]['id'], 7);
						}
					} else {
						$this->addTaskToEndOfSchedule($scheduleCopy[$userID][$ct[$userID]]);
					}
				}
				$ct[$userID]++;
			}
		}
		
		foreach ($removedTasks as $rval)
			if (!$this->insertTaskToSchedule($rval)) return false;
		
		// Output current queue state
		sm_debug::write("Current queue size for user $insUserID: ".count($this->scheduledTasks[$insUserID]), 7);
		sm_debug::write("Current schedule for user $insUserID:", 7);
		foreach ($this->scheduledTasks[$insUserID] as $t) {
			sm_debug::write("  ".$t['id']." start time = ".$t['actual_start_date']." ".$t['actual_start_time'].", duration = ".$t['timest'], 7);
		}
		
		return true;
		
		/*
		// Add to the end of queue
		if (!$hasDeadLine && !$hasLiveLine) {
			$this->scheduledTasks[] = $task;
		}
		
		// Add to the min (end of queue, liveline)
		else if (!$hasDeadLine && $hasLiveLine) {
			// Calculate the end of queue
			$endOfQueue = time();
			foreach ($this->scheduledTasks as $sval)
				$endOfQueue += (strtotime($sval['timest']) - strtotime('TODAY'));
				
			$unixLiveLine = strtotime($task['calc_start']);
			
			if ($unixLiveLine < $endOfQueue) $this->scheduledTasks[] = $task;
			else {
				// Add delay task (ID = 0)
				$delayTask = array();
				$delayTask['id'] = 0;
				$delayTask['name'] = 'Wait';
				$delayTask['timest'] = date("H:i:s", $unixLiveLine - $endOfQueue);
				$this->scheduledTasks[] = $delayTask;
				
				$this->scheduledTasks[] = $task;
			}
		}
		
		// Insert...
		else {
			
		
		}
		*/
		
	
	}
	

    public function acp_schedule( $parameters ){

		if ($this->visitor->user_id) {
			$this->tpl->assign("title", "Tasks");
			
			// Load all open tasks for all users
			// $this->visitor->user_id
			$t = new ent_task;
			$this->taskList = $t->getOpenList(0, 0, 0, 0);
			
			// Find the max ID for adding repeated tasks
			$maxID = 0;
			foreach ($this->taskList as $tval) {
				if ($maxID < $tval['id']) $maxID = $tval['id'];
			}
			
			// Initialize schedule begin
			$this->scheduleStartTime = time();
			
			// Multiply repeatable tasks
			$tasksToAdd = array();
			foreach ($this->taskList as $tkey => $tval) {
				$repeat = $t->getRepeat($tval['id']);
				if ($repeat) {
					// Add 10 instances of this task with corrected end_time and start_time
					switch ($repeat['repeat_type']) {
					case 'weekly':
						$taskcount = 0;
						if ($repeat['mask'] == 0) break;
						
						// setup current day as beginning of today's date (12 am)
						$currentDay = strtotime(date("Y-m-d 00:00:00", time()));
						//$currentDay = strtotime('TODAY');
						while ($taskcount < 10) {
						
							sm_debug::write("Adding repeated task for date ".date("Y-m-d", $currentDay), 7);
						
							// Increase $currentDay until the week day matches the mask
							while (1) {
								// Find out what day of week is the $currentDay (0 for Monday 6 for Sunday)
								$currentWeekDay = date("N", $currentDay) - 1;

								// find the next day the task should be repeated
								if ((1 << $currentWeekDay) & $repeat['mask']) break;
								
								// Increase current day by 1
								$currentDay = strtotime("+1 day", $currentDay);
							}
							
							// Replace the date portion of end_time and start_time
							$newTask = $tval;
							if (isset($tval['start_time']) && ($tval['start_time'] != null)) {
								$startLinuxTime = strtotime($tval['start_time']);
								$original_start_time = $startLinuxTime - strtotime(date("Y-m-d 00:00:00", $startLinuxTime));
								$newTask['start_time'] = date("Y-m-d H:i:s", $original_start_time + $currentDay);
								sm_debug::write("New start time = ".$newTask['start_time'], 7);
							}
							if (isset($tval['end_time']) && ($tval['end_time'] != null)) {
								$endLinuxTime = strtotime($tval['end_time']);
								$original_end_time = $endLinuxTime - strtotime(date("Y-m-d 00:00:00", $endLinuxTime));
								$newTask['end_time'] = date("Y-m-d H:i:s", $original_end_time + $currentDay);
								sm_debug::write("New end time = ".$newTask['end_time'], 7);
							}
							
							// Add task to the task add buffer array
							$maxID++;
							$newTask['id'] = $maxID;
							$tasksToAdd[] = $newTask;
							$taskcount++;
							
							// Increase current day by 1
							$currentDay = strtotime("+1 day", $currentDay);
						}
						break;
					}
					
					// Remove the task from the normal list
					unset($this->taskList[$tkey]);

				} else {
					// Mark task as non-repeated
					$this->taskList[$tkey]['repeated'] = 0;
				}
			}
			
			// Add $tasksToAdd to $this->taskList
			foreach ($tasksToAdd as $tval) {
				$tval['repeated'] = 1;
				$this->taskList[] = $tval;
			}
			
			//////////////////////////////////////////////////////////////////
			// Scheduling algorytm
			//////////////////////////////////////////////////////////////////

			/////////////////////////////////////////////////////////
			// Un-link tasks by setting livelines and deadlines
			
			// Copy natual deadline/liveline into calculated deadline/liveline
			foreach ($this->taskList as $tkey => $tval) {
				$this->taskList[$tkey]['calc_end'] = $tval['end_time'];
				$this->taskList[$tkey]['calc_start'] = $tval['start_time'];
			}
			$this->outputdebugtasks();

			// Recursively shift deadlines of the parent tasks
			foreach ($this->taskList as $tval) {
				$this->adjustParentDeadline($tval);
			}
			
			$this->outputdebugtasks();
			
			/////////////////////////////////////////////////////////
			// Rearrange tasks according to their deadlines
			
			$this->scheduledTasks = array();
			$this->insertIDs = array();
			$this->troublemakers = "";
			
			$scheduleOK = 1;
			$curSize = count($this->insertIDs);
			foreach ($this->taskList as $tval) {
				if (!$this->insertTaskToSchedule($tval)) {
					$scheduleOK = 0;
					break;
				}
			}
			
			$currentUserID = $this->visitor->user_id;
			if (count($this->scheduledTasks[$currentUserID]) == 0) {
				$this->scheduledTasks[$currentUserID][0]['name'] = "Sit back and relax!";
				$this->scheduledTasks[$currentUserID][0]['actual_start_date'] = date("Y-m-d");
				$this->scheduledTasks[$currentUserID][0]['actual_start_time'] = date("H:i:s");
			}
			
			$this->tpl->assign("tasks", $this->scheduledTasks[$currentUserID]);
			$this->tpl->assign("scheduleOK", $scheduleOK);
			$this->tpl->assign("troublemakers", $this->troublemakers);
			
			
			//if ($this->tpl->detectMobile())
			//	$this->tpl->display( "mainMenuM.tpl" );
			//else $this->tpl->display( "tasks.tpl" );
			$this->tpl->display( "tasks.tpl" );
			
		} else {
			header("Location: /login");
		}

    } // function acp_default()

} // class atm_default

?>
