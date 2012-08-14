<?php

class ent_user extends sm_ent {

    public $items = array();

    public function getByLogin( $login ) {
        $res = $this->db->query(array(
			"query" => "
				SELECT
				  id
				, login
				, password
				, firstname
				, lastname
				, email
				FROM user
				WHERE login = ?s(login)"
			,"binds" => array(
				  "login" => $login
			)
		));

		//sm_debug::write($res["affected_rows"]." users found with login '$login'", 7);
        if ( $res["affected_rows"] == 0 ) {
        	return false;
		} else{
            $this->items = $res["result"][0];
            return true;
		}
    } // function getByLogin()

	
    public function get( $id ) {
        $res = $this->db->query(
            array(
                "query" => "select
                                  id
                                , login
                                , password
                                , firstname
                                , lastname
                                , email
                            from
                                user
                            where
                                id = ?i(id)"
                ,"binds" => array(
                      "id" => $id
                )
            )
        );
        if ( $res["affected_rows"] == 0 )
		{
			sm_debug::write("User $id not found", 1);
			throw new sm_exception( "User $id not found", 2 );
		}
		else
			return $res["result"][0];
    } // function get()

    public function getList( ) {
		$res = $this->db->query(
			array(
				"query" => "select
								  id
								, login
								, firstname
								, lastname
								, email
							from
								user"
				,"binds" => array(
				)
			)
		);
		return $res["result"];
    } // function get()

    /**
     * Добавление пользователя системы
     *
     * @access public
     */
    public function add() {
        try {
            $this->db->query(
                array(
                    "query" => "INSERT INTO
                                       user(
                                           id
                                           ,login
                                           ,password
                                   )
                                   VALUES(
                                           ?i(id)
                                           ,?s(login)
                                           ,?s(password)
                                   )"
                    ,"binds" => $this->items
                )
            );
        } catch ( Exception $e ) {
            throw new sm_exception( $e->getMessage() );
        }
    } // function add()

    public function update() {
        $this->db->query(
            array(
                "query" => "UPDATE
                                   user
                               SET
                                   login=?s(login)
                                   ,password=?s(password)
                               WHERE
                                   id=?i(id)"
                ,"binds" => $this->items
            )
        );
    } // function update()
	
	public function changePassword($login, $passwordold, $passwordnew){
		
        try {
			$res = $this->db->query(
				array(
					"query" => "update
									user
								set
									password=?s(passwordnew)
								where
									login=?s(login)
									and password=?s(passwordold)"
					,"binds" => array(
						"login" => $login
						, "passwordold" => $passwordold
						, "passwordnew" => $passwordnew
					)
				)
			);
			
			if ( $res["affected_rows"] == 0 ) throw new sm_exception("User $login not found or old password is incorrect");
			return;

        } catch ( Exception $e ) {
			sm_debug::write("Error updating password: ".$e->getMessage());
            throw new sm_exception( "Error updating password: ".$e->getMessage() );
        }
		
	}

} // class ent_user

?>
