<?php

class ent_subscription extends sm_ent {

    public function get( $id ) {
		$res = $this->db->query(
			array(
				"query" => "select
								  id
								, user_id
								, project_id
							from
								subscription
							where
								id = ?i(id)"
				,"binds" => array(
					  "id" => $id
				)
			)
		);
		if ( $res["affected_rows"] == 0 )
		{
			sm_debug::write("Subscription $id not found", 1);
			throw new sm_exception( "Subscription $id not found", 1 );
		}
		else
			return $res["result"][0];
    } // function get()

    public function getList( $user_id ) {
		$res = $this->db->query(
			array(
				"query" => "select
								  id
								, project_id
							from
								subscription
							where
								user_id = ?i(user_id)"
				,"binds" => array(
					"user_id" => $user_id
				)
			)
		);
		return $res["result"];
    } // function get()
	
    public function getListByProject( $project_id ) {
		$res = $this->db->query(
			array(
				"query" => "select
								  id
								, user_id
							from
								subscription
							where
								project_id = ?i(project_id)"
				,"binds" => array(
					"project_id" => $project_id
				)
			)
		);
		return $res["result"];
    } // function get()

	
	public function subscribe($user_id, $project_id) {
	
		try {
			$this->db->query(
                array(
					"query" => "INSERT INTO
                                       subscription(
										  user_id
										, project_id
                                   )
                                   VALUES(
                                             ?i(user_id)
										   , ?i(project_id)
                                   )"
                    ,"binds" => array(
						"user_id" => $user_id
						, "project_id" => $project_id
					)
				)
			);
        } catch ( Exception $e ) {
			sm_debug::write("Adding subscription error: ".$e->getMessage());
            throw new sm_exception( $e->getMessage() );
        }
    }

	public function unsubscribe($user_id, $project_id) {
	
		try {
			$this->db->query(
                array(
					"query" => "DELETE from
									subscription
								WHERE
									user_id = ?i(user_id)
									and project_id = ?i(project_id)
								"
                    ,"binds" => array(
						"user_id" => $user_id
						, "project_id" => $project_id
					)
				)
			);
        } catch ( Exception $e ) {
			sm_debug::write("Deleting subscription error: ".$e->getMessage());
            throw new sm_exception( $e->getMessage() );
        }
    }

	public function sendNotifications($project_id, $msg, $subj) {
	
		set_time_limit(5);
	
		sm_debug::write("Sending notifications project_id = $project_id, msg = $msg, subj = $subj", 7);
	
		// Get project name
		$p = new ent_project;
		$project = $p->get($project_id);
		$pname = $project['name'];
	
		// Check who is subscribed to this project updates
		$userList = $this->getListByProject($project_id);

		$u = new ent_user;
		foreach ($userList as $item) {
		
			$uid = $item['user_id'];
			$user = $u->get($uid);
			
			sm_debug::write("Sending notification to ".$user['firstname']." ".$user['lastname'], 7);
			
			$msg = str_replace("<br>", "\n", $msg);
			$msg = str_replace("<BR>", "\n", $msg);
			
			$email = $user['email'];
			$headers = 'From: "kisProject" <support@arcatechsystems.com>' . "\r\n" .
				'Reply-To: webmaster@example.com' . "\r\n" .
				'Content-type: text/plain' . "\r\n" .
				'X-Mailer: PHP/' . phpversion();
			$res = mail($email, "[kisProject ($pname)] $subj", $msg, $headers);
			if (!$res) sm_debug::write("Error sending email", 1);
			
			sm_debug::write("Notification send finished", 7);
		}
    }

}

?>
