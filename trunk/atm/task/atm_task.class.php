<?php
class atm_task extends sm_atm {

    protected $actions = array(
        "__default"         => array( "name" => "acp_view",             "sess" => "write" )
		,"view"             => array( "name" => "acp_view",             "sess" => "write" )
		,"doAdd"            => array( "name" => "acp_doAdd",            "sess" => "write" )
		,"doLoad"           => array( "name" => "acp_doLoad",           "sess" => "write" )
		,"doUpdateDesc"     => array( "name" => "acp_doUpdateDesc",     "sess" => "write" )
		,"doUpdateSteps"    => array( "name" => "acp_doUpdateSteps",    "sess" => "write" )
		,"doAddNote"        => array( "name" => "acp_doAddNote",        "sess" => "write" )
		,"doUpdateStatus"   => array( "name" => "acp_doUpdateStatus",   "sess" => "write" )
		,"doDelete"         => array( "name" => "acp_doDelete",         "sess" => "write" )
		,"doUpdateName"     => array( "name" => "acp_doUpdateName",     "sess" => "write" )
		,"doUpdateRelease"  => array( "name" => "acp_doUpdateRelease",  "sess" => "write" )
		,"doUpdateOwner"    => array( "name" => "acp_doUpdateOwner",    "sess" => "write" )
		,"doUpdateCategory" => array( "name" => "acp_doUpdateCategory", "sess" => "write" )
		,"doUpdateEstimate" => array( "name" => "acp_doUpdateEstimate", "sess" => "write" )
		,"doUpdateProgress" => array( "name" => "acp_doUpdateProgress", "sess" => "write" )
		,"doAddDependency"  => array( "name" => "acp_doAddDependency",  "sess" => "write" )
		,"doUpdateLiveline" => array( "name" => "acp_doUpdateLiveline", "sess" => "write" )
		,"doUpdateDeadline" => array( "name" => "acp_doUpdateDeadline", "sess" => "write" )
		,"doUpdateRepeat"   => array( "name" => "acp_doUpdateRepeat",   "sess" => "write" )
    );

    function __construct( $acp, $parameters ) {
    	$this->tpl = new sm_tpl( __FILE__ );
        parent::__construct( $acp, $parameters );

    } // function __construct()

    public function acp_view( $parameters ){

		if (isset($_REQUEST['pid'])) $pid = $_REQUEST['pid'];
		else $pid = 0;
		$tid = $_REQUEST['tid'];
		$t = new ent_task;
		$task = $t->get($tid);
		
		// Check task ID and pid
		if ($task === null) {
			header("Location: /");
			exit;
		}
		if ($pid == 0) {
			$pid = $task['project_id'];
		}
		
		$n = new ent_note;
		$notes = $n->getList($tid);
		
		// Load dependencies and their names
		$d = new ent_dependency;
		$dependencies = $d->depend_parents($tid);
		foreach ($dependencies as $key => $value) {
			$ttmp = new ent_task;
			$task2 = $ttmp->get($value['task_id']);
			$dependencies[$key]['name'] = $task2['name'];
		}
		
		// Load all current tasks, remove dependencies, remove self
		$tc = new ent_task;
		$curTaskList = $tc->getOpenList($pid, 0, 0, 0);
		foreach ($curTaskList as $key => $value) {
			foreach ($dependencies as $dep) {
				if ($dep['task_id'] == $value['id']) unset($curTaskList[$key]);
			}
			if ($value['id'] == $tid) unset($curTaskList[$key]);
		}
		
		// Status
		$s = new ent_status;
		$status = $s->getList();
		foreach ($status as $key => $item)
		{
			if ($item['id'] == $task['status_id'])
				$status[$key]['selected'] = 1;
			else 
				$status[$key]['selected'] = 0;
		}
		
		// Progress
		$progressVals = array(0, 10, 20, 30, 40, 50, 60, 70, 80, 90, 100);
		
		// Release
		$r = new ent_release;
		$release = $r->getList($pid);
		foreach ($release as $key => $item)
		{
			if ($item['id'] == $task['release_id'])
				$release[$key]['selected'] = 1;
			else 
				$release[$key]['selected'] = 0;
		}

		// Owner
		$u = new ent_user;
		$users = $u->getList();
		foreach ($users as $key => $item)
		{
			if ($item['id'] == $task['owner_id'])
				$users[$key]['selected'] = 1;
			else 
				$users[$key]['selected'] = 0;
		}

		// Category
		$c = new ent_category;
		$category = $c->getList($pid);
		foreach ($category as $key => $item)
		{
			if ($item['id'] == $task['category_id'])
				$category[$key]['selected'] = 1;
			else 
				$category[$key]['selected'] = 0;
		}
		
		// Time estimate
		if ($task['timest'] === null) $task['timest'] = '00:00';
		
		// Liveline
		sm_debug::write("Liveline: ".$task['start_time'], 7);
		if (isset($task['start_time']) && ($task['start_time'] != null)) {
			$unixLiveline = strtotime($task['start_time']);
			$startYear = date('Y', $unixLiveline);
			$startMonth = date('m', $unixLiveline);
			$startDay = date('d', $unixLiveline);
			$startHour = date('G', $unixLiveline);
			$startMinute = date('i', $unixLiveline);
			
			$this->tpl->assign("starttime", true);
			$this->tpl->assign("startyear", $startYear);
			$this->tpl->assign("startmonth", $startMonth);
			$this->tpl->assign("startday", $startDay);
			$this->tpl->assign("starthour", $startHour);
			$this->tpl->assign("startminute", $startMinute);
		} else {
			$this->tpl->assign("starttime", false);
		}
		
		// Deadline
		sm_debug::write("Deadline: ".$task['end_time'], 7);
		if (isset($task['end_time']) && ($task['end_time'] != null)) {
			$unixDeadline = strtotime($task['end_time']);
			$endYear = date('Y', $unixDeadline);
			$endMonth = date('m', $unixDeadline);
			$endDay = date('d', $unixDeadline);
			$endHour = date('G', $unixDeadline);
			$endMinute = date('i', $unixDeadline);

			$this->tpl->assign("endtime", true);
			$this->tpl->assign("endyear", $endYear);
			$this->tpl->assign("endmonth", $endMonth);
			$this->tpl->assign("endday", $endDay);
			$this->tpl->assign("endhour", $endHour);
			$this->tpl->assign("endminute", $endMinute);
		} else {
			$this->tpl->assign("endtime", false);
		}
		
		// Repeat
		$repeat = $t->getRepeat($tid);
		if ($repeat === null) $repeat = array('repeat_type' => '', 'mask' => 0);
		$weekdays = array(0, 0, 0, 0, 0, 0, 0);
		for ($i=0; $i<7; $i++)
			if ($repeat['mask'] & (1 << $i)) $weekdays[$i] = 1;
		$repeat['mo'] = $weekdays[0];
		$repeat['tu'] = $weekdays[1];
		$repeat['we'] = $weekdays[2];
		$repeat['th'] = $weekdays[3];
		$repeat['fr'] = $weekdays[4];
		$repeat['sa'] = $weekdays[5];
		$repeat['su'] = $weekdays[6];
		$this->tpl->assign("repeat", $repeat);
		
		// History
		$fullHistory = $t->getTaskHistory($tid);
		$history = array();
		foreach ($fullHistory as $key => $item)
		{
			// User name
			$howner = $u->get($item['user_id']);
			$history[$key]['owner_name'] = $howner['firstname'].' '.$howner['lastname'];
			
			// Date/time
			$history[$key]['datetime'] = $item['dto'];

			// Action name
			$actionName = '';
			switch ($item['action_id'])	{
			case ent_task::HISTORY_ACTION_PROGRESS_UPD: $actionName = 'Updated progress to '.$item['progress'].'%'; break;
			case ent_task::HISTORY_ACTION_OWNER_UPD: 
				$newownerUser = $u->get($item['owner_id']);
				$newownerName = $newownerUser['firstname'].' '.$newownerUser['lastname'];
				$actionName = 'Changed owner to '.$newownerName;
				break;
			case ent_task::HISTORY_ACTION_DESCRIPTION_UPD: $actionName = 'Updated description'; break;
			case ent_task::HISTORY_ACTION_STEPS_UPD: $actionName = 'Updated steps to test'; break;
			case ent_task::HISTORY_ACTION_STATUS_UPD: $actionName = 'Updated status'; break;
			case ent_task::HISTORY_ACTION_NAME_UPD: $actionName = 'Updated name to: '.$item['name']; break;
			//case ent_task::HISTORY_ACTION_DELETE: $actionName = 'Deleted'; break;
			case ent_task::HISTORY_ACTION_RELEASE_UPD: $actionName = 'Updated release'; break;
			case ent_task::HISTORY_ACTION_CATEGORY_UPD: $actionName = 'Updated category'; break;
			case ent_task::HISTORY_ACTION_ESTIMATE_UPD: $actionName = 'Updated estimate'; break;
			case ent_task::HISTORY_ACTION_ESTIMATE_PREC_UPD: $actionName = 'Updated estimate precision'; break;
			case ent_task::HISTORY_ACTION_NOTE_ADD: $actionName = 'Added note ID '.$item['note_id']; break;
			case ent_task::HISTORY_ACTION_NOTE_UPD: $actionName = 'Edited note ID '.$item['note_id']; break;
			case ent_task::HISTORY_ACTION_CREATED: $actionName = 'Task Created with name: '.$item['name']; break;
			}
			$history[$key]['action_name'] = $actionName;
			
		}
		
		$this->tpl->assign("title", "Tasks - $tid");
		$this->tpl->assign("description", $task['description']);
		$this->tpl->assign("teststeps", $task['teststeps']);
		$this->tpl->assign("notes", $notes);
		$this->tpl->assign("dependencies", $dependencies);
		$this->tpl->assign("curTaskList", $curTaskList);
		$this->tpl->assign("tid", $tid);
		$this->tpl->assign("tname", $task['name']);
		$this->tpl->assign("pid", $pid);
		$this->tpl->assign("status", $status);
		$this->tpl->assign("release", $release);
		$this->tpl->assign("users", $users);
		$this->tpl->assign("category", $category);
		$this->tpl->assign("timest", $task['timest']);
		$this->tpl->assign("timest_prec", $task['timest_precision']);
		$this->tpl->assign("progress", $task['progress']);
		$this->tpl->assign("progressVals", $progressVals);
		$this->tpl->assign("history", $history);
		
		$this->tpl->display( "view.tpl" );
    }
	
	public function acp_doAdd() {
	
		$pid = $_REQUEST['pid'];
		$name = $_REQUEST['tname'];
		$priority = $_REQUEST['tpriority'];
		$owner_id = substr($_REQUEST['towner'], 1);
		$release_id = substr($_REQUEST['trel'], 1);
		$category_id = substr($_REQUEST['tcat'], 1);
		
		$task = array(
			'name' => $name
			,'priority' => $priority
			,'project_id' => $pid
			,'owner_id' => $owner_id
			,'release_id' => $release_id
			,'category_id' => $category_id
		);
		
		$t = new ent_task;
		$t->add($task);
	}

	private function add_business_days($startdate,$buisnessdays,$holidays,$dateformat){
		$i=1;
		$dayx = strtotime($startdate);
		while($i < $buisnessdays){
			$day = date('N',$dayx);
			$date = date('Y-m-d',$dayx);
			if($day < 6 && !in_array($date,$holidays))$i++;
				$dayx = strtotime($date.' +1 day');
		}
		return date($dateformat,$dayx);
	 }
	public function acp_doLoad() {
	
		sm_debug::write("Started", 7);
	
		$pid = $_REQUEST['pid'];
		$release_id = substr($_REQUEST['relid'], 1);
		$category_id = substr($_REQUEST['catid'], 1);
		$user_id = substr($_REQUEST['user_id'], 1);
		$status_id = substr($_REQUEST['status_id'], 1);
		
		sm_debug::write("Getting task list: $pid, $release_id, $category_id, $user_id, $status_id", 7);
		
		// create the status array based on status_id >= 100
		if ($status_id >= 100)
		{
			switch ($status_id) {
				case 100: $statusArray = array(1, 2); // Unfinished
				break;
			}
		} else $statusArray = array($status_id);
		
		// Open colleting table
		$table = '<table border="0" cellspacing="0">';
		$table .= '<tbody><tr><td>';
		
		// Format tasks table
		$table .= '<table border="0" cellspacing="1" class="tasktable">';
		$table .= '<thead>';
		$table .= '<tr>';
		$table .= '<th width="20"></th>';
		$table .= '<th width="20"></th>';
		$table .= '<th width="40">ID</th>';
		$table .= '<th width="200">Name</th>';
		$table .= '<th width="80">Priority</th>';
		$table .= '<th width="100">Owner</th>';
		$table .= '<th width="100">Release</th>';
		$table .= '<th width="100">Category</th>';
		$table .= '<th width="80">Status</th>';
		$table .= '<th width="80">Estimate</th>';
		$table .= '</tr>';
		$table .= '</thead>';
		$table .= '<tbody>';

		sm_debug::write("Debug 1", 7);
		
		$pr = array(
			'1' => 'H'
			,'2' => 'M'
			,'3' => 'L'
		);

		$u = new ent_user;
		$r = new ent_release;
		$c = new ent_category;
		$s = new ent_status;
		
		$statlist = $s->getList();
		$statnames = array();
		$statcolors = array();
		foreach ($statlist as $item) {
			$statnames[$item['id']] = $item['name'];
			$statcolors[$item['id']] = $item['color'];
		}
		
		$relname = "";
		$ownername = "";
		$catname = "";
		
		// IDs of previous requests for optimization
		$prevcat_id = 0;
		$prevrel_id = 0;
		$prevusr_id = 0;
		$totalMinutesMin = array(); // minimum estimate by user
		$totalMinutesMax = array(); // maximum estimate by user
		$totalProgressMinutes = 0;
		$totalMinutes = 0;
		$nas = false; // true if there are 'n/a' in the time estimates
		$initials = array();

		// Load tasks
		sm_debug::write("Debug 2", 7);
		foreach ($statusArray as $statusItem) {
		
			sm_debug::write("Debug 3: $statusItem", 7);
		
			// Load tasks with fixed status
			$t = new ent_task;
			$list = $t->getList($pid, $release_id, $category_id, $user_id, $statusItem);
			
			foreach ($list as $item)
			{
				// Prepare data
				if ($prevusr_id != $item['owner_id']) {
					$user = $u->get($item['owner_id']);
					$ownername = $user['firstname'];
					$prevusr_id = $item['owner_id'];
				}
				
				if ($prevrel_id != $item['release_id']) {
					$release = $r->get($item['release_id']);
					$relname = $release['name'];
					$prevrel_id = $item['release_id'];
				}
				
				if ($prevcat_id != $item['category_id']) {
					$category = $c->get($item['category_id']);
					$catname = $category['name'];
					$prevcat_id = $item['category_id'];
				}
				
				$estimate = 'n/a';
				if ($item['timest'] != null) {
					$estimate = $item['timest'];
					$prec = $item['timest_precision'];
					
					if (!isset($totalMinutesMin[$item['owner_id']])) $totalMinutesMin[$item['owner_id']] = 0;
					if (!isset($totalMinutesMax[$item['owner_id']])) $totalMinutesMax[$item['owner_id']] = 0;

					$hours = substr($estimate, 0, strpos($estimate, ":")); 
					$minutes = substr($estimate, -2, 2);
					$progressratio = (100-$item['progress'])/100;

					if ($item['status_id'] <= 2) {
						if ($prec != 0) $totalMinutesMin[$item['owner_id']] += ($hours*60 + $minutes)*100*$progressratio/($prec + 100);
						else $totalMinutesMin[$item['owner_id']] += ($hours*60 + $minutes)*$progressratio;
						
						$totalMinutesMax[$item['owner_id']] += ($hours*60 + $minutes)*($prec+100)*$progressratio/100;
						
						// Save user's initials in $initials
						$initials[$item['owner_id']] = substr($user['firstname'], 0, 1).substr($user['lastname'], 0, 1);
						//$initials[$item['owner_id']] = "GZ";
					}
					
					// Estimate total progress
					$totalProgressMinutes += ($hours*60 + $minutes)*(1 - $progressratio);
					$totalMinutes += ($hours*60 + $minutes);

				} else {
					$nas = true;
				}

				// Color status cell if status is "In progress"
				if ($item['status_id'] == 2)
					$status = '<div style="background-color: #00aaaa; width: '.$item['progress'].'%;"><div class = "progress-text">'.$statnames[$item['status_id']].'</div></div>';
				else $status = $statnames[$item['status_id']];
				
				$color = $statcolors[$item['status_id']];
				
				// Format HTML
				$tdb = '<td bgcolor="'.$color.'">';
				$tde = '</td>';

				$table .= '<tr><td bgcolor="'.$color.'"><img src="/img/edit.gif" onclick="openTask('.$item['id'].')"></td><td bgcolor="'.$color.'"><img src="/img/delete.gif" onclick="deleteTask('.$item['id'].')"></td>';
				
				$table .= $tdb.$item['id'].$tde;
				$table .= '<td bgcolor="'.$color.'" onclick="openTask('.$item['id'].')">'.$item['name'].$tde;
				$table .= $tdb.$pr[$item['priority']].$tde;
				$table .= $tdb.$ownername.$tde;
				$table .= $tdb.$relname.$tde;;
				$table .= $tdb.$catname.$tde;
				$table .= $tdb.$status.$tde;
				$table .= $tdb.$estimate.$tde;
				
				$table.='</tr>';
			}
		}
		$table .= '</tbody></table>';
		
		sm_debug::write("Debug 4", 7);

		/////////////////////////////////////////////////////////////////////////////////////
		// Format task estimates tables
		$estWidth = 170;
		
		$table .= '</td></tr><tr><td>'; // Collecting table - next row
		$table .= '<table border="0" cellspacing="0" width="100%" class="estimate">';
		$table .= '<tbody>';
		
		// Find the maximum estimate by user
		$maxMinIdx = -1;  // Index of maximum value of minimum estimates
		$maxMaxIdx = -1;  // Index of maximum value of maximum estimates
		foreach ($totalMinutesMin as $key => $item) {
			if ($maxMinIdx == -1) $maxMinIdx = $key;
			else if ($item > $totalMinutesMin[$maxMinIdx]) $maxMinIdx = $key;
		}
		foreach ($totalMinutesMax as $key => $item) {
			if ($maxMaxIdx == -1) $maxMaxIdx = $key;
			else if ($item > $totalMinutesMax[$maxMaxIdx]) $maxMaxIdx = $key;
		}
			
		sm_debug::write("Debug 5: maxMinIdx = $maxMinIdx", 7);

		// add the line with delivery estimate

		$holidays = array("2012-01-10");
		$startdate = date('Y-m-j');
		if (($maxMinIdx != -1) && ($maxMaxIdx != -1)) {
			$busDaysMin = $totalMinutesMin[$maxMinIdx]/480;
			$busDaysMax = $totalMinutesMax[$maxMaxIdx]/480;
		} else {
			$busDaysMin = 0;
			$busDaysMax = 0;
		}
		$deliveryMin = $this->add_business_days($startdate,$busDaysMin,$holidays,'d M');
		$deliveryMax = $this->add_business_days($startdate,$busDaysMax,$holidays,'d M y');
		
		$table .= '<tr><td>';
		$table .= '</td><td width="20">';
		$table .= '<div class = "estimatedetails" id="plus" style="display:block"><img src="/img/bullet_toggle_plus.png" onclick="toggleDetails()"></div>';
		$table .= '<div class = "estimatedetails" id="minus" style="display:none"><img src="/img/bullet_toggle_minus.png" onclick="toggleDetails()"></div>';
		$table .= '</td><td width="90">';
		$table .= ' Delivery est.:</td><td width="'.$estWidth.'">';
		if ($nas) $table .= '<font color=#FF0000>';
		$table .= $deliveryMin.' - '.$deliveryMax;
		if ($nas) $table .= ' (???)</font>';
		$table .= '</td></tr>';
		
		//if (($maxMinIdx != -1) && ($maxMaxIdx != -1)) {
		//} else {
		//	$table .= 'no details available';
		//}
		
		$table .= '</tbody></table>';
		
		// add the table with total time estimate and delivery details
		$table .= '<div id="estdetails" style="display:none">';
		$table .= '<table border="0" cellspacing="1" width="100%" class="estimatedetails">';
		$table .= '</tbody>';
		
		// The line with total time
		$tmpHoursMin = 0;
		$tmpHoursMax = 0;
		foreach ($totalMinutesMin as $item) $tmpHoursMin += $item/60;
		foreach ($totalMinutesMax as $item) $tmpHoursMax += $item/60;
		$totalHoursMin = sprintf("%.01f", $tmpHoursMin);
		$totalHoursMax = sprintf("%.01f", $tmpHoursMax);
		$table .= '<tr><td>Total time:</td><td width="'.$estWidth.'">';
		$table .= $totalHoursMin.'-'.$totalHoursMax.' h';
		$table .= '</td></tr>';
		
		// The line with total progress
		if ($totalMinutes) $totalProgress = $totalProgressMinutes / $totalMinutes;
		else $totalProgress = 0;
		$table .= '<tr><td>Total progress:</td><td width="'.$estWidth.'">';
		$table .= sprintf("%.1f", $totalProgress*100).'%';
		$table .= '</td></tr>';

		
		sm_debug::write("Debug 6", 7);
		
		// Estimates and delivery by user
		foreach ($totalMinutesMin as $key => $itemMin) {

			if (isset($initials[$key])) {
				sm_debug::write("Debug 7: key=$key: ".$initials[$key], 7);
				
				// Hours
				$tmpHoursMin = $itemMin/60;
				$tmpHoursMax = $totalMinutesMax[$key]/60;
				$totalHoursMin = sprintf("%.01f", $tmpHoursMin);
				$totalHoursMax = sprintf("%.01f", $tmpHoursMax);
				$table .= '<tr><td>'.$initials[$key].' Total time:</td><td width="'.$estWidth.'">';
				$table .= $totalHoursMin.'-'.$totalHoursMax.' h';
				$table .= '</td></tr>';
				
				// Delivery
				$busDaysMin = $itemMin/480;
				$busDaysMax = $totalMinutesMax[$key]/480;
				$deliveryMin = $this->add_business_days($startdate,$busDaysMin,$holidays,'d M');
				$deliveryMax = $this->add_business_days($startdate,$busDaysMax,$holidays,'d M y');
				$table .= '<tr><td>'.$initials[$key].' Delivery:</td><td width="'.$estWidth.'">';
				$table .= $deliveryMin.' - '.$deliveryMax;
				$table .= '</td></tr>';
			}
		}
		
		$table .= '</tbody></table>';
		$table .= '</div>';
		
		// Close collecting table
		$table .= '</td></tr>';
		$table .= '</tbody></table>';
			
		$data = array();
		$data['userdata'] = $table;
		return $data;
	}

	function acp_doUpdateDesc() {
		$tid = $_REQUEST['tid'];
		$desc = $_REQUEST['desc'];
		$t = new ent_task;
		$t->updateDesc($tid, $desc);
	}

	function acp_doUpdateSteps() {
		$tid = $_REQUEST['tid'];
		$text = $_REQUEST['steps'];
		$t = new ent_task;
		$t->updateSteps($tid, $text);
	}
	
	function acp_doAddNote() {
		$tid = $_REQUEST['tid'];
		$text = $_REQUEST['text'];
		$author_id = $this->visitor->user_id;
		$note = array(
			  "task_id" => $tid
			, "text" => $text
			, "author_id" => $author_id
		);
		$n = new ent_note;
		$n->add($note);
		sm_debug::write("note add returned", 7);
	}
	
	function acp_doUpdateStatus() {
		$tid = $_REQUEST['tid'];
		$status = $_REQUEST['status'];
		$t = new ent_task;
		$t->updateStatus($tid, $status);
	}
	
	function acp_doDelete() {
		$tid = $_REQUEST['tid'];
		$t = new ent_task;
		$t->delete($tid);
	}
	
	function acp_doUpdateName() {
		$tid = $_REQUEST['tid'];
		$name = $_REQUEST['name'];
		$t = new ent_task;
		$t->updateName($tid, $name);
	}

	function acp_doUpdateRelease() {
		$tid = $_REQUEST['tid'];
		$release = $_REQUEST['release'];
		$t = new ent_task;
		$t->updateRelease($tid, $release);
	}

	function acp_doUpdateOwner() {
		$tid = $_REQUEST['tid'];
		$owner = $_REQUEST['owner'];
		$t = new ent_task;
		$t->updateOwner($tid, $owner);
	}
	
	function acp_doUpdateCategory() {
		$tid = $_REQUEST['tid'];
		$category = $_REQUEST['category'];
		$t = new ent_task;
		$t->updateCategory($tid, $category);
	}
	
	function acp_doUpdateEstimate() {
		$tid = $_REQUEST['tid'];
		$timest = $_REQUEST['timest'];
		$precision = $_REQUEST['prec'];
		$t = new ent_task;
		$t->updateEstimate($tid, $timest, $precision);
	}
	
	function acp_doUpdateProgress() {
		$tid = $_REQUEST['tid'];
		$progress = $_REQUEST['progress'];
		$t = new ent_task;
		$t->updateProgress($tid, $progress);
	}
	
	function acp_doAddDependency() {
		$tid = $_REQUEST['tid'];
		$depid = $_REQUEST['dependency'];
		$t = new ent_task;
		$t->addDependency($tid, $depid);
	}
	
	function acp_doUpdateLiveline() {
		$tid = $_REQUEST['tid'];
		$y = $_REQUEST['y'];
		$m = $_REQUEST['m'];
		$d = $_REQUEST['d'];
		$h = $_REQUEST['h'];
		$min = $_REQUEST['min'];
		$dt = "$y-$m-$d $h:$min:00";
		
		$t = new ent_task;
		if ($y) $t->updateLiveline($tid, $dt);
		else $t->updateLiveline($tid, null);
	}
	
	function acp_doUpdateDeadline() {
		$tid = $_REQUEST['tid'];
		$y = $_REQUEST['y'];
		$m = $_REQUEST['m'];
		$d = $_REQUEST['d'];
		$h = $_REQUEST['h'];
		$min = $_REQUEST['min'];
		$dt = "$y-$m-$d $h:$min:00";
		
		$t = new ent_task;
		if ($y) $t->updateDeadline($tid, $dt);
		else $t->updateDeadline($tid, null);
	}
	
	function acp_doUpdateRepeat() {
		$tid = $_REQUEST['tid'];
		$rtype = $_REQUEST['repeat_type'];
		$mo = $_REQUEST['mo'];
		$tu = $_REQUEST['tu'];
		$we = $_REQUEST['we'];
		$th = $_REQUEST['th'];
		$fr = $_REQUEST['fr'];
		$sa = $_REQUEST['sa'];
		$su = $_REQUEST['su'];
		$mask = $mo + ($tu << 1) + ($we << 2) + ($th << 3) + ($fr << 4) + ($sa << 5) + ($su << 6);
		
		$t = new ent_task;
		$t->updateRepeat($tid, $rtype, $mask);
	}

}
?>