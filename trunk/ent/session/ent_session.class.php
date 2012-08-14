<?php

class ent_session extends sm_ent {

    public $items = array();

    public static function getList(){
		ent_session::cleanup();
        try {
	        $results = sm_config::$db->query(
	        	array(
	        		  "query" => "select
	        		  					id
									  	,login
										,password
	        		  					,user_id
	        		  					,dtb
	        		  					,dte
	        		  					,error_id
	        		  				from
	        		  					session"
	        	)
	        );
	        return $results['result'];
        } catch ( Exception $e ) {
            throw new sm_exception( $e->getMessage() );
        }
    }
	
    public static function getWhoIsOnline(){
		ent_session::cleanup();
        try {
	        $results = sm_config::$db->query(
	        	array(
	        		  "query" => "select
									  	login
	        		  				from
	        		  					session
									where
										user_id is not null
										and dte is null"
	        	)
	        );
			
			$onlineusers = $results['result'];
			
			// Remove duplicates
			$refined = array();
			foreach ($onlineusers as $user) 
				if (!in_array(trim($user['login']), $refined)) $refined[] = trim($user['login']);
			
	        return $refined;
        } catch ( Exception $e ) {
            throw new sm_exception( $e->getMessage() );
        }
    }

    /**
     * начало сессии пользователя
     *
     * @access public
     *
     */
    public function begin(){
    	$this->db->begin();
		ent_session::cleanup();
        try {
	        $this->db->query(
	        	array(
	        		  "query" => "insert into
										session(
										  	dtb
									    )
									    values (
										  	now()
									    )"
	        		  , "binds" => $this->items
	        	)
	        );
	        $this->id = $this->db->get_last_inserted_id();
	        $this->db->commit();
        } catch ( Exception $e ) {
        	sm_debug::write( "[session] Begin error: ".$e->getMessage(), 2);
			$this->db->rollback();
            $this->db->commit();
            throw new sm_exception( '', self::ERROR_BEGIN_SESSION_ERROR );
        }
    } // function begin()

    /**
     * окончание сессии пользователя
     *
     * @access public
     *
     */
    public function end(){
	
		sm_debug::write("Closing session ".$this->id, 7);
	
		ent_session::cleanup();
        try {
			// Find all open sessions of this user by session ID
	        $res1 = $this->db->query(
	        	array(
	        		  "query" => "select 
									user_id
								  from 
									session
								  where
									id = ?i(id)"
	        		  , "binds" => array(
	        		  		"id" => $this->id
	        		  )
	        	)
	        );
			
			if (isset($res1['result'][0]['user_id'])) {
				$user_id = $res1['result'][0]['user_id'];
				sm_debug::write("Closing session for user ".$user_id, 7);
		
				// Close all session of this user
				$this->db->query(
					array(
						  "query" => "update
											session
										set
											dte = now()
										where
											dte is null
											and user_id = ?i(user_id)"
						  , "binds" => array(
								"user_id" => $user_id
						  )
					)
				);
			}

        } catch ( Exception $e ) {
        	sm_debug::write( "[session] End error (Connection session ID: #$this->id): ".$e->getMessage(), 1);
			throw new sm_exception( "[session] End error (Connection session ID: ".$this->id, 1 );
        }
    } // function end()

    /**
     * обновление информации о сессии пользователя: login и пароль
     *
     * @access public
     *
     */
    public function updateLoginPassword(){
        try {
	        $this->db->query(
	        	array(
	        		  "query" => "update
										session
									set
									  	login = ?s(login)
										,password = ?s(password)
									where
										id = ?i(id)"
	        		  , "binds" => $this->items
	        	)
	        );
        } catch ( Exception $e ) {
        	sm_debug::write( "[session] Update information error (Connection session ID: #$this->id): ".$e->getMessage(), 2);
            throw new sm_exception( '', self::ERROR_UPDATE_SESSION_ERROR );
        }
    } // function update()

    public function updateUserId(){
		try {
			$this->db->query(
				array(
					  "query" => "update
										session
									set
										user_id = ?i(user_id)
									where
										id = ?i(id)"
					  , "binds" => $this->items
				)
			);
		} catch ( Exception $e ) {
			sm_debug::write( "[session] Update information error (Connection session ID: #$this->id): ".$e->getMessage(), 2 );
			throw new sm_exception( 'Session error: Update user ID', 1  );
		}
    } // function update()

	// Close sessions that are past maxlifetime or don't have user_id
	public static function cleanup() {
        try {
			$interval = sm_config::$session_params['maxlifetime'];
		
			// Close all open sessions by date
			sm_config::$db->query(
				array(
					  "query" => "update
										session
									set
										dte = now()
									where
										dte is null
										and dtb < DATE_SUB(now(),INTERVAL $interval SECOND)"
				)
			);
			
			// Close all sessions with no user_id, i.e. user does not exist
			sm_config::$db->query(
				array(
					  "query" => "update
										session
									set
										dte = now()
									where
										dte is null
										and user_id is null"
				)
			);
			
        } catch ( Exception $e ) {
        	sm_debug::write( "Cleanup error: ".$e->getMessage(), 1);
			throw new sm_exception( "Cleanup error: ".$e->getMessage(), 1 );
        }
	}


} // class ent_session

?>
