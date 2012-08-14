<?php
/**
 * Class sm_db
 * работа с базой данных
 * 
 * @package ConcertTerminal
 * @version 1.0.0.
 * @category php5 only
 * @copyright Copyright &copy; November 2007, UNITECSYS
 *
 * @uses sm_config
 * @uses sm_exception
 */

class sm_db {	


    /**
     * Экземпляр драйвера конкретного механизма БД
     *
     * @access private
     * @var object
     */
    private $driver = null; 
    	
    			
    /**
     * Список поддерживаемых placeholders
     *
     * @access private
     * @var array
     */
    private $valid_placeholders = array( "i", "d", "s", "b", "n", "t", "f", "a" );


    /**
     * Transaction level
     *
     * @var integer
     * @access private
     */
    protected $transaction_level;    


    /**
     * Признак отката транзакций
     *
     * @var bool
     * @access protected
     */
    protected $rollback;


    /**
     * Список поддерживаемых значений параметра datepart для функции datediff: ?f(datediff;datepart;startdate;enddate)
     * 'y' - year, 'm' - month, 'd' - day, 'h' - hour, 'n' - minute, 's' - second
     *
     * @access private
     * @var array
     */
    private $valid_dateparts = array( "y", "m", "d", "h", "n", "s" );


    /**
     * Список поддерживаемых значений параметра datepart для функции datediff: ?f(datediff;datepart;startdate;enddate)
     * 'y' - year, 'm' - month, 'd' - day, 'h' - hour, 'n' - minute, 's' - second
     *
     * @access public
     * @var array
     */
    public $translated_query = '';

    /**
     * Конструктор класса
     *
     * @access public
     * @param array  $params - настройки для переопределения конфигурации
     *                         'driver' - объект-драйвер для работы с базой данных
     */
    function __construct( $params = array() ) {
			
        $this->driver = (
            isset( $params['driver'] )
            ? $params['driver']
            : sm_config::db_driver( $params )
        );		        

    } // function __construct()



    /**
     * Sends an unique query to the currently active database 
     *
     * @access public
     * @param array $params = array (
     *                  "query" => value      - запрос
     *                , "binds" => array()    - массив для подстановки в запрос
     *                , "limit" => value      - значения для ограничения выборки  
     *                , "callback" => string  - имя функции, которой будет
     *                                          передана для обработки
     *                                          каждая строка получаемого рекордсета
     *                                          Параметры - номер строки, сама строка
     *                                          Результат - строка, которую нужно
     *                                          положить в рекордсет
     *              )
     * @return array ( 
     *     "affected_rows" => value
     * 	 , "result"        => array()
     * )						
     */
    public function query( $params ) {
		
        if( isset( $params["binds"] ) && !is_array( $params["binds"] ) ) {
            throw new sm_exception( "Incorrect parameter 'binds' for query method" );
        }

        if( !isset( $params["query"] ) ) {
            throw new sm_exception( "Undefined parameter 'query' for query method" );
        }        
        
        $query = $params["query"];
        $limit = ( isset( $params["limit"] ) ? $params["limit"] : null );
        
        if( !isset( $params["binds"] ) || count( $params["binds"] ) == 0 ) {
            return $this->getResult( $query, $limit );
        }

        foreach( $params["binds"] AS $index => $bind ) {

            // Placeholder
            // ?<parameter type>(<parameter name>)
            // Example: ?i(limit)
            //
            $pattern = '/\?('.join( '|', $this->valid_placeholders ).')\('.$index.'\)/';

            if( preg_match_all( $pattern, $query, $s_matches ) ) {

                $s_matches = array_unique( $s_matches[1] );

                foreach( $s_matches as $match ) {

                    $patt = '?'.$match.'('.$index.')';

                    $method_name = "placeholder_".$match;

                    $value =
                        method_exists( $this->driver, $method_name )
                        ? $this->driver->$method_name( $bind )
                        : $this->$method_name( $bind )
                    ;
					
                    $query = str_replace( $patt, $value, $query );

                }
	                

            } // if( preg_match_all( $pattern, $query, $s_matches ) )

        } // foreach( $binds AS $index => $bind )

        $this->translated_query = $query;

        $callback = isset( $params["callback"] ) ? $params["callback"] : "";

        return $this->getResult( $query, $limit, $callback );
		
    } // function query()


    /**
     * Стандартный обработчик placeholder 'i'
     *
     * @param int $bind
     *
     * @return int
     * @access private
     */
    private function placeholder_i( $bind ) {

        if( $bind === null ) return 'null';

        if( !preg_match( '|^-?\d+$|', $bind ) ) {
            throw new sm_exception( "Incorrect type 'i': ".$bind, 0 );
        }
        return $bind;

    } // function placeholder_i()


    /**
     * Стандартный обработчик placeholder 'd'
     *
     * @param float $bind
     *
     * @return float
     * @access private
     */
    private function placeholder_d( $bind ) {

        if( $bind === null ) return 'null';

        if( !preg_match( '/^-?\d*(\.\d*)?\s*$/i', $bind ) ) {
            throw new sm_exception( "Incorrect type 'd': ".$bind, 0 );
        }
        return floatval($bind);

    } // function placeholder_d()

	
    /**
     * Стандартный обработчик placeholder 's'
     *
     * @param string $bind
     *
     * @return string
     * @access private
     */
    private function placeholder_s( $bind ) {

        if( $bind === null ) return 'null';

        return "'".$this->escape_string( $bind )."'";

    } // function placeholder_s()


    /**
     * Стандартный обработчик placeholder 'b'
     *
     * @param string $bind
     *
     * @return string
     * @access private
     */
    private function placeholder_b( $bind ) {

        if( $bind === null ) return 'null';

        return "'".$this->escape_string( $bind )."'";

    } // function placeholder_b()


    /**
     * Стандартный обработчик placeholder 'n'
     *
     * @param string $bind
     *
     * @return string
     * @access private
     */
    private function placeholder_n( $bind ) {

        return $bind;

    } // function placeholder_n()


    /**
     * Escapes special characters in a string for use in a SQL statement
     *
     * @access public
     * @param string $str
     * @return string
     */
    public function escape_string( $str ) {
		
        return $this->driver->escape_string( $str );
		
    } // function real_escape_string()
	
	
    /**
     * Returns last inserted id
     *
     * @access public
     * @return int      последний вставленный идентификатор
     */
    public function get_last_inserted_id() {
		
        return $this->driver->get_last_inserted_id();
		
    } // function get_last_inserted_id()


    /**
     * Возвращает результат выполнения запроса
     *
     * @access private
     * @param string $query
     * @param string $limit - значения для ограничения выборки
     * @param string $callback - имя функции, которой будет
     *                           передана для обработки
     *                           каждая строка получаемого рекордсета
     *                           Параметры - номер строки, сама строка
     *                           Результат - строка, которую нужно
     *                           положить в рекордсет
     * @return array ( 
     *     "affected_rows" => value
     *   , "result"        => array()
     * )
     */
    private function getResult( $query, $limit = null, $callback = "" ) {
		
        try {
            $res = $this->driver->getResult( $query, $limit, $callback );   
        } catch ( Exception $e ) {
            throw new sm_exception( $e->getMessage()."<br>SQL-query: ".$this->translated_query );
        }
        return $res;
		
    } // function getResult()


    /**
     * Функция открытия новой транзакции
     *
     * @access public
     * @return bool 
     */
    public function begin() {

        if ( !$this->transaction_level ) {
            $this->driver->begin();
        }
        
        $this->transaction_level++;
        
        return true;

    } // function begin()

		
    /**
     * Функция закрытия транзакции
     *
     * @access public
     * @return bool
     */
    public function commit() {

        if ( !$this->transaction_level ) {
            return false;
        }
        $this->transaction_level--;

        if ( !$this->transaction_level ) {
            if ( $this->rollback ) {
                $this->driver->rollback();
                $this->rollback = false;
            } else {
                $this->driver->commit();
            }
        }

        return true;

    } // function commit()
		

    /**
     * Функция отката транзакции
     *
     * @access public
     * @return bool
     */
    public function rollback() {

        if ( $this->transaction_level ) {
            $this->rollback = true;
            return true;
        }

        return false;

    } // function rollback()


    /**
     * Откатывает все транзакции
     *
     * @access public
     * @return bool
     */
    public function reset() {

        if ( $this->transaction_level ) {
            $this->rollback = false;
            $this->transaction_level = 0;
            $this->driver->rollback();
            return true;
        }

        return false;

    } // function reset()


    /**
     * Стандартный обработчик placeholder 'f'
     *
     * @param string $bind = function_name;param1;param2;...;paramN
     *
     * @return string
     * @access private
     */
    private function placeholder_f( $bind ) {

        $params = explode( ";", $bind );
        switch ( $params[0] ) {
            // получение текущей даты
            // $bind = "now"
            case "now"      :
                if ( isset( $params[1] ) ) {
                    throw new sm_exception( "Incorrect type 'f': ".$bind.": function name = '".$params[0]."'. Expected no parameters");
                    break;
                }
                return $this->driver->placeholder_f_now();
                break;
            // получение разницы между датами (datepart, startdate, enddate)
            // $bind = "datediff;datepart;startdate;enddate"
            // startdate вычитается из enddate. Если startdate позже, чем enddate, то возвращается отрицательное значение
            case "datediff" :
                if ( !isset( $params[3] ) ) {
                    throw new sm_exception( "Incorrect type 'f': ".$bind.": function name '".$params[0]."'. Expected 3 parameters" );
                    break;
                }
                if ( !in_array( $params[1], $this->valid_dateparts ) ) {
                    throw new sm_exception( "Incorrect type 'f': ".$bind.": function name '".$params[0]."'. Invalid datepart: '".$params[1]."'" );
                    break;
                }
                return $this->driver->placeholder_f_datediff( $params[1], $params[2], $params[3] );
                break;
            // добавление интервала к дате
            // $bind = "dateadd;datepart;number;date"
            case "dateadd"  :
                if ( !isset( $params[3] ) ) {
                    throw new sm_exception( "Incorrect type 'f': ".$bind.": function name '".$params[0]."'. Expected 3 parameters" );
                    break;
                }
                if ( !in_array( $params[1], $this->valid_dateparts ) ) {
                    throw new sm_exception( "Incorrect type 'f': ".$bind.": function name '".$params[0]."'. Invalid datepart: '".$params[1]."'" );
                    break;
                }
                return $this->driver->placeholder_f_dateadd( $params[1], $params[2], $params[3] );
                break;
            // получение части даты
            // $bind = "datepart;datepart;date"
            case "datepart" :
                if ( !isset( $params[2] ) ) {
                    throw new sm_exception( "Incorrect type 'f': ".$bind.": function name '".$params[0]."'. Expected 2 parameters" );
                    break;
                }
                if ( !in_array( $params[1], $this->valid_dateparts ) ) {
                    throw new sm_exception( "Incorrect type 'f': ".$bind.": function name '".$params[0]."'. Invalid datepart: '".$params[1]."'" );
                    break;
                }
                return $this->driver->placeholder_f_datepart( $params[1], $params[2] );
                break;
            default         :
                throw new sm_exception( "Incorrect type 'f': ".$bind.": undefined function name '".$params[0]."'" );
                break;
        }

    } // function placeholder_f()


    /**
     * Стандартный обработчик placeholder 'a'
     *
     * @access private
     * @param (string|array) $bind = value1,value2,...,valueN || $bind = array(value1,value2,...,valueN)
     * @return string - значения, которое можно использовать в IN (пример возврата: "('a','b')")
     */
    private function placeholder_a( $bind ) {

        if (is_string($bind)) {
            if ("" === $bind) {
                $data = array();
            } else {
                $data = explode(",", $bind);
            }
        } else {
            $data = $bind;
        }

        if (!is_array($data)) {
            throw new sm_exception( "Incorrect type 'a': ".(string)$bind, 0 );
        }

        if (!count($data)) {
            return "(null)";
        }

        foreach ($data as $k => $v) {
            $data[$k] = "'" . $this->escape_string($v) . "'";
        }

        return "(" . join(",", $data) . ")";

    } // function placeholder_a()
	
	
    /**
     * Get the record by ID
     *
     * @access public
     * @param int $machine_id
     * @return array 
     */
    public static function getRecord( $id, $fields, $table_name ){
	
		sm_debug::write("sm_db::getRecord started. Parameters: id = $id", 7);
        try {
		
			////////////////////////////////////////////////////
			// Compose SQL query
			$query = array("query" => "select ");
			
			$query["query"] .= $fields[0]." ";
			for ($i=1; $i < count($fields); $i++) {
				$query["query"] .= ",".$fields[$i]." ";
			}
			$query["query"] .= "from $table_name where id = $id";
			
			////////////////////////////////////////////////////
			// Execute SQL query and return results
		
			$results = sm_config::$db->query($query);

			$records = $results['affected_rows'];
			sm_debug::write("sm_db::getRecord: $records results loaded", 7);
			
			if ($records == 1) return $results['result'][0];
			else return false;
			
        } catch ( Exception $e ) {
            throw new sm_exception( $e->getMessage() );
        }
	}

    /**
     * Check if record ID exists
     *
     * @access public
     * @param int $machine_id
     * @return array 
     */
    public static function checkRecord( $id, $table_name ){
	
		sm_debug::write("sm_db::checkRecord started. Parameters: id = $id, table_name = $table_name", 7);
        try {
		
			$query = array("query" => "select * from $table_name where id=$id");
			$results = sm_config::$db->query($query);

			$records = $results['affected_rows'];
			sm_debug::write("sm_db::getRecord: $records results loaded", 7);
			
			if ($records == 1) return true;
			else return false;
			
        } catch ( Exception $e ) {
            throw new sm_exception( $e->getMessage() );
        }
	}
	
    /**
     * Update the record by ID or create a new record if ID does not exist in DB
     *
     * @access public
     * @param int $machine_id
     * @return none
     */
    public static function updateRecord( $id, $data, $table_name ){
	
		sm_debug::write("started. Parameters: id = $id", 7);
        try {
		
			///////////////////////////////////////////////
			// check if record exists
			$res = sm_db::checkRecord($id, $table_name);
			
			///////////////////////////////////////////////
			// Build SQL query

			$query = array("query" => "");

			$firstarg = true;
			$insert = "";
			$values = "";
			$set = "";
			
			if( isset( $data['is_deleted'] ) && $data['is_deleted'] == '1' ) {
				if ($res === false) {
					sm_debug::write("record $id does not exist on terminal and server. Do nothing...", 7);
					return;
				} else {
					sm_debug::write("record $id exists. Deleting...", 7);
					$query["query"] .= "delete from $table_name where id = $id";
				}
			} else {
			
				if (isset($data['is_deleted'])) unset($data['is_deleted']);

				foreach ($data as $key => $value) {

					/////////////////////////////////////////
					// determine the type
					//$is_number = is_numeric($value);
					$is_number = 0;
					
					// use intelligence. For some fields even the numbers are processed as strings
					
					////////
					if ($firstarg) {
						$insert .= $key." ";
						if ($is_number) {
							$values .= $value."";
							$set    .= $key." = ".$value." ";
						} else {
							$values .= "'".$value."' ";
							$set    .= $key." = '".$value."' ";
						}
						$firstarg = false;
					} else {
						$insert .= ",".$key." ";
						if ($is_number) {
							$values .= ",".$value."";
							$set    .= ",".$key." = ".$value." ";
						} else {
							$values .= ",'".$value."' ";
							$set    .= ",".$key." = '".$value."' ";
						}
					}
				}
				if ($res === false) {
					sm_debug::write("record $id does not exist yet. Creating...", 7);
					$query["query"] .= "insert into $table_name($insert) values($values)";				
				} else {
					sm_debug::write("record $id exists. Updating...", 7);
					$query["query"] .= "update $table_name set $set where id = $id";
				}
			}
			
			///////////////////////////////////////////////
			// Execute SQL query

			$results = sm_config::$db->query($query);

        } catch ( Exception $e ) {
            throw new sm_exception( $e->getMessage() );
        }
	}


} // class sm_db
		
?>
