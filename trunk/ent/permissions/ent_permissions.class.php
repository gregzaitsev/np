<?php
/**
 * Class ent_permissions
 *
 * @package ConcertTerminal
 * @version 1.0.0.
 * @category php5 only
 * @author GZ
 * @copyright Copyright &copy; 01/18/2010, ARCA
 *
 */
class ent_permissions extends sm_ent {

    /**
     * array of object parameters
     *
     * @access public
     * @var array
	 * @return true, if access given, false if not, 0 if no records.
     */
    public $items = array();
	
	private static function checkAccessByTermID($terminalID, $user_id, $role_id, $atm_name, $acp_name){
	
		sm_debug::write("started. Parameters: Terminal ID = $terminalID, user_id = $user_id, role_id = $role_id, atm_name = $atm_name, acp_name = $acp_name", 7);
	
		try {
			// Load all recorsd for this ATM and ACP, OR them.
			$results = sm_config::$db->query(
				array(
					"query" => "select
									id
									,access
								from
									permissions
								where
									atm_name = ?s(atm_name)
									and acp_name = ?s(acp_name)
									and user_id = ?i(user_id)
									and appointment_id = ?i(role_id)
									and machine_id = ?i(terminalID)"
					, "binds" => array(
						"atm_name" => $atm_name
						,"acp_name" => ($acp_name === null) ? "" : $acp_name
						,"user_id" => $user_id
						,"role_id" => $role_id
						,"terminalID" => $terminalID
					)
				)
			);
			
			$numRec = $results['affected_rows'];
			sm_debug::write("$numRec results found for $atm_name::acp_$acp_name", 7);
			if ($numRec == 0) return 0;
			
			$access = 0;
			for ($i=0; $i<$numRec; $i++)
				$access |= $results['result'][0]['access'];
			sm_debug::write("Access = $access", 7);
			if ($access) return true;
			else return false;
			
		} catch ( Exception $e ) {
			sm_debug::write("permissions error: ".$e->getMessage(), 1);
        }
		
		return false;
	}
	

    /**
     * Check permissions by atm and acp name.
	 *    - If ATM is not found in DB, the default access is false
	 *    - The default settings are the ones with zero machine_id, user_id, and empty acp_name
	 *    - Next, non-empty acp_name is checked, and if records are found, they override the default ones
	 *    - Next, non-zero Terminal ID is checked, and if records are found, they override the previously found ones
	 *    - Next, non-zero User ID is checked, and if records are found, they override the previously found ones
	 *
     * @access public
     * @param char atm_name, char acp_name
     * @return true, if access given, false if not
     */
    public static function checkAccess( $user_id, $atm_name, $acp_name ){
		sm_debug::write("started. Parameters: user_id = $user_id, atm_name = $atm_name, acp_name = $acp_name", 7);

		$terminalID = sm_config::$terminal_serial_number;
		$access = false;
		
		// Get the role ID for this user_id
		$user = new ent_user;
		$user->get($user_id);
		$employee_id = $user->employee_id;
		sm_debug::write("employee_id = $employee_id", 7);
		
		$employee = new ent_employee;
		$employee->get($employee_id);
		$role_id = $employee->appointment_id;
		sm_debug::write("role_id = $role_id", 7);
		
		// Load default-default access
		$access = ent_permissions::checkAccessByTermID(0, 0, $role_id, $atm_name, "");
		sm_debug::write("access = $access", 7);
		
		// Load access for non-zero acp
		if ($acp_name != "") {
			$acp_access = ent_permissions::checkAccessByTermID(0, 0, $role_id, $atm_name, $acp_name);
			
			if ($acp_access === true){ // Access is given
				$access = $acp_access;
				sm_debug::write("access = $access", 7);
			} else if ($acp_access === false) { // Access is denied
				$access = $acp_access;
				sm_debug::write("access = $access", 7);
			} else if ($acp_access === 0) { // No permissions record
				sm_debug::write("no records - no changes", 7);
			}
		}

		// Load access for non-zero terminal ID
		$trm_access = ent_permissions::checkAccessByTermID($terminalID, 0, $role_id, $atm_name, $acp_name);
		
		if ($trm_access === true){ // Access is given
			$access = $trm_access;
			sm_debug::write("access = $access", 7);
		} else if ($trm_access === false) { // Access is denied
			$access = $trm_access;
			sm_debug::write("access = $access", 7);
		} else if ($trm_access === 0) { // No permissions record
			sm_debug::write("no records - no changes", 7);
		}

		// Load access for non-zero user ID
		if ($user_id) {
			$usr_access = ent_permissions::checkAccessByTermID($terminalID, $user_id, $role_id, $atm_name, $acp_name);
			
			if ($usr_access === true){ // Access is given
				$access = $usr_access;
				sm_debug::write("access = $access", 7);
			} else if ($usr_access === false) { // Access is denied
				$access = $usr_access;
				sm_debug::write("access = $access", 7);
			} else if ($usr_access === 0) { // No permissions record
				sm_debug::write("no records - no changes", 7);
			}
		}

		return $access;
		
	} // function getRecordByName

} // class ent_permissions

?>
