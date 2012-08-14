<?php
/**
 * Class ent_config
 *
 * @package ConcertTerminal
 * @version 1.0.0.
 * @category php5 only
 * @copyright Copyright &copy; December 2010, ARCA
 *
 */
class ent_config {

    /**
     * Get the record by ID
     *
     * @access public
     * @param int $machine_id
     * @return array массив сотрудников, которые работают с машиной
     */
    public static function getRecord( $id ){
		sm_debug::write("started. Parameters: id = $id", 7);
		$fields = array("id", "name", "value", "description");
		return sm_config::$db->getRecord($id, $fields, 'config');
	}

    /**
     * Update the record by ID or create a new record if ID does not exist in DB
     *
     * @access public
     * @param int $machine_id
     * @return array массив сотрудников, которые работают с машиной
     */
    public static function updateRecord( $id, $data ){

		sm_debug::write("ent_config::updateRecord started. Parameters: id = $id", 7);
		sm_config::$db->updateRecord($id, $data, 'config');
	}

    /**
     * Получить список настроек для компании клиента или для терминала
     *
     * @access public
     * @param int $machine_id
     * @return array массив сотрудников, которые работают с машиной
     */
    public static function getList( $company_id, $machine_id ){

		sm_debug::write("ent_config::getList started. Parameters: $company_id, $machine_id", 7);

        try {
			if ($company_id == 0) {

				sm_debug::write("ent_config::getList: Loading by machine ID only", 7);

				$results = sm_config::$db->query(
					array(
						  "query" => "select
											id
											,name
											,value
											,description
										from
											config
										where
											machine_id = $machine_id"
					)
				);
			} else {

				sm_debug::write("ent_config::getList: Loading by company ID and machine ID", 7);

				$results = sm_config::$db->query(
					array(
						  "query" => "select
											id
											,name
											,value
											,description
										from
											config
										where
											machine_id = $machine_id
											and company_id = $company_id"
					)
				);
			}

			$tmp = $results['affected_rows'];
			sm_debug::write("ent_config::getList: $tmp results loaded", 7);

	        return $results['result'];
        } catch ( Exception $e ) {
            throw new sm_exception( $e->getMessage() );
        }

    }

	/**
	 * Get the config table value by its name IFF there is exactly 1 copy of
	 * that record.  Otherwise, error out with an appropriate message.
	 *
	 * @param int $name the name of the value to retrieve
	 * @return string the value of the record
	 * @author Russ Adams
	 * @access public
	 */
	public static function getRecordByName($name) {
		sm_debug::write("started. Parameters: name = $name", 7);

		if (empty($name)) {
			sm_debug::write("empty name parameter, was passed: $name",1);
			throw new sm_exception("Invalid config name provided.");
		}

		$machine_id = sm_config::$terminal_serial_number;
		
		try {
			$results = sm_config::$db->query(array(
				  "query" => "
					SELECT
					  id
					, name
					, value
					, description
					FROM config
					WHERE name = ?s(name)
					  AND machine_id = ?i(machine_id)"
				, "binds" => array(
					  "name" => $name
					, "machine_id" => $machine_id
				)
			));

			$affected_rows = $results['affected_rows'];

			if ($affected_rows == 1) {
				$val = $results['result'][0]['value'];
				sm_debug::write("$affected_rows results loaded: value = $val", 7);
				return $results['result'][0]['value'];
			} else if ($affected_rows <= 0) {
				sm_debug::write("configuration error: option $name not found", 1);
				throw new sm_exception( "configuration error: option $name not found" );
			} else if ($affected_rows > 1) {
				sm_debug::write("configuration error: duplicate option $name not found (found $affected_rows records)", 1);
				throw new sm_exception( "configuration error: duplicate option $name not found" );
			}

		} catch ( Exception $e ) {
			sm_debug::write("configuration error: can't read the parameter from the DB", 1);
			throw new sm_exception( $e->getMessage() );
		}

	}

	/**
     * Set the record by name
     * @static
     * @access public
     * @param string $name
     * @param string $value
     * @return void
     */
    public static function setRecordByName( $name, $value ){
        sm_debug::write("Set parameters: name = ".$name."; value=".$value, 7);

        try {

            $results = sm_config::$db->query(
                array(
                    "query" => "
                        UPDATE config
                        SET
                            value = ?s(value)
                        WHERE
                            name = ?s(name)"
                    , "binds" => array(
                        "name" => $name
                        , "value" => $value
                    )
                )
            );

            if ( $results['affected_rows'] == 1) {
                sm_debug::write("configuration is loaded: name=".$name." value = ".$value, 7);
            }
            elseif( $results['affected_rows'] > 1 ){
                throw new sm_exception( "configuration error: Too main \"$name\" options: ".$results['affected_rows'] );
            }
            else {
                sm_debug::write("configuration error: option $name not found", 1);
                throw new sm_exception( "configuration error: option $name not found" );
            }
        } catch ( Exception $e ) {
            sm_debug::write("configuration error: can't read the parameter from the DB", 1);
            throw new sm_exception( $e->getMessage() );
        }
    }
	
	/**
	 * Get Cassette Limits from `config` table for this machine.  Returns an
	 * array defined as: array("high" => .., "low" => ..) where each * contains
	 * a list of integers representing high and low limits.
	 *
	 * When no cassettes limits are found, an empty array is returned.
	 *
	 * @access public
	 * @author Russ Adams
	 * @return array 
	 */
	public static function getCassettesLimitsConfig(){
		try {
			$results = sm_config::$db->query(array(
				  "query" => "
					SELECT
					  name
					, value
					FROM config
					WHERE name LIKE 'cassette_limit_%'
					  AND machine_id = ?i(machine_id)"
				, "binds" => array(
					"machine_id" => sm_config::$terminal_serial_number
				)
			));
			if( $results['affected_rows'] ) {
				$cassettesLimitsConfig = array(
					  'low'  => array()
					, 'high' => array()
				);
				foreach ( $results['result'] as $value ) {
					$cassetteLimitName = explode( '_', $value['name'] );
					$cassettesLimitsConfig[$cassetteLimitName[2]][$cassetteLimitName[3]] = $value['value'];
				}
				return $cassettesLimitsConfig;
			} else {
				return array();
			}
		} catch ( Exception $e ) {
			throw new sm_exception( $e->getMessage() );
		}

	}
}
?>
