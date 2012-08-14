<?php
class atm_activity extends sm_atm {

    protected $actions = array(
        "__default"         => array( "name" => "acp_view",             "sess" => "write" )
		,"view"             => array( "name" => "acp_view",             "sess" => "write" )
    );

    function __construct( $acp, $parameters ) {
    	$this->tpl = new sm_tpl( __FILE__ );
        parent::__construct( $acp, $parameters );

    } // function __construct()

    public function acp_view( $parameters ){

		// History
		$t = new ent_task;
		$u = new ent_user;
		$p = new ent_project;
		$s = new ent_status;
		$r = new ent_release;
		$c = new ent_category;
		$n = new ent_note;
		$fullHistory = $t->getRecentHistory();
		$history = array();
		foreach ($fullHistory as $key => $item)
		{
			// check item
			$itemOK = true;
			if (!isset($item['task_id']) || ($item['task_id'] == '')) $itemOK = false;
		
			if ($itemOK) {
				// Project name
				$task = $t->get($item['task_id']);
				$project = $p->get($task['project_id']);
				$history[$key]['project_name'] = $project['name'];
				
				// Task name and ID
				$history[$key]['task_name'] = $item['task_id']." - ".$task['name'];

				// User name
				$huser = $u->get($item['user_id']);
				$history[$key]['owner_name'] = $huser['firstname'].' '.$huser['lastname'];
				
				// Load status name
				if ($item['status_id']) {
					$status = $s->get($item['status_id']);
					$statusName = $status['name'];
				}
				
				// Load release name
				if ($item['release_id']) {
					$release = $r->get($item['release_id']);
					$releaseName = $release['name'];
				}

				// Load category name
				if ($item['category_id']) {
					$category = $r->get($item['category_id']);
					$categoryName = $category['name'];
				}
				
				// Load note
				if ($item['note_id']) {
					$note = $n->get($item['note_id']);
					$noteText = $note['text'];
				}

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
				case ent_task::HISTORY_ACTION_DESCRIPTION_UPD: $actionName = 'New description: '.$item['description']; break;
				case ent_task::HISTORY_ACTION_STEPS_UPD: $actionName = 'New steps to test: '.$item['steps']; break;
				case ent_task::HISTORY_ACTION_STATUS_UPD: $actionName = 'Updated status to "'.$statusName.'"'; break;
				case ent_task::HISTORY_ACTION_NAME_UPD: $actionName = 'Updated name to: '.$item['name']; break;
				//case ent_task::HISTORY_ACTION_DELETE: $actionName = 'Deleted'; break;
				case ent_task::HISTORY_ACTION_RELEASE_UPD: $actionName = 'Updated release to "'.$releaseName.'"'; break;
				case ent_task::HISTORY_ACTION_CATEGORY_UPD: $actionName = 'Updated category to '.$categoryName.'"'; break;
				case ent_task::HISTORY_ACTION_ESTIMATE_UPD: $actionName = 'Updated estimate to '.$item['timest']; break;
				case ent_task::HISTORY_ACTION_ESTIMATE_PREC_UPD: $actionName = 'Updated estimate precision to '.$item['timest_precision'].'%'; break;
				case ent_task::HISTORY_ACTION_NOTE_ADD: $actionName = 'New note (ID:'.$item['note_id'].'): '.$noteText; break;
				case ent_task::HISTORY_ACTION_NOTE_UPD: $actionName = 'Edited note (ID:'.$item['note_id'].'): '.$noteText; break;
				case ent_task::HISTORY_ACTION_CREATED: $actionName = 'Task Created with name: '.$item['name']; break;
				}
				$history[$key]['action_name'] = $actionName;
			} else {
				$history[$key]['project_name'] = 'Error';
				$history[$key]['task_name'] = 'Error';
				$history[$key]['owner_name'] = 'Error';
				$history[$key]['datetime'] = 'Error';
				$history[$key]['action_name'] = 'Error. Record ID: '.$fullHistory[$key]['id'];
			}
		}

		$this->tpl->assign("title", "Recent Activity");
		$this->tpl->assign("history", $history);

		$this->tpl->display( "view.tpl" );
    }
}

?>
