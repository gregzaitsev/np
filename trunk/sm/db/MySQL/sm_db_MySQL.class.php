<?php
/**
 * Class sm_db_MySQL
 * работа с базой данных MySQL
 * 
 * @package ConcertTerminal
 * @version 1.0.0.
 * @category php5 only
 * @copyright Copyright &copy; November 2007, UNITECSYS
 *
 * @uses sm_exception
 * @uses sm_config
 */

class sm_db_MySQL {


    /**
     * Database host
     *
     * @var string
     * @access protected
     */	
    protected $dbhost;


    /**
     * Database name
     *
     * @var string
     * @access protected
     */
    protected $dbname;


    /**
     * Database user
     *
     * @var string
     * @access protected
     */
    protected $dbuser;


    /**
     * Database password
     *
     * @var string
     * @access protected
     */		
    protected $dbpass;


    /**
     * DB connection link
     *
     * @var resource
     * @access protected
     */		
    protected $dblink; 


    /**
     * Character set for MySQL connection
     * 
     * @var string
     * @access public
     */
    protected $charset;	


    /**
     * Port for MySQL connection
     * 
     * @var int
     * @access public
     */
    protected $port;


    /**
     * Parameters for MySQL connection
     * 
     * @var int
     * @access public
     */
    protected $parameters;	


    /**
     * Список соответствий поддерживаемых значений параметра datepart со значениями MySQL для функции dateadd
     * 'y' - year, 'm' - month, 'd' - day, 'h' - hour, 'n' - minute, 's' - second
     *
     * @access private
     * @var array
     */
    private $dateparts = array( "y" => "year"
                              , "m" => "month"
                              , "d" => "day"
                              , "h" => "hour"
                              , "n" => "minute"
                              , "s" => "second"
                          );


    /**
     * Конструктор класса
     *
     * @access public
     * @param array  $params - настройки для переопределения конфигурации
     */
    function __construct( $params = array() ) {

        $this->dbhost 	= ( isset( $params['dbhost'] ) ? $params['dbhost'] : sm_config::$db_params["dbhost"] );
        $this->dbname 	= ( isset( $params['dbname'] ) ? $params['dbname'] : sm_config::$db_params["dbname"] );
        $this->dbuser 	= ( isset( $params['dbuser'] ) ? $params['dbuser'] : sm_config::$db_params["dbuser"] );
        $this->dbpass 	= ( isset( $params['dbpass'] ) ? $params['dbpass'] : sm_config::$db_params["dbpass"] );
        $this->charset  = ( isset( $params['charset'] ) ? $params['charset'] : sm_config::$db_params["charset"] );
        $this->parameters  = ( isset( $params['parameters'] ) ? $params['parameters'] : sm_config::$db_params["parameters"] );

        $this->connect();

        $this->transaction_level = 0;
        $this->rollback = false;

    } // function __construct()


    /**
     * Деструктор класса
     *
     */
    function __destruct() {

    } // function __destruct()


    /**
     * Open a new connection to the MySQL server 
     * 
     * @access protected
     * @return bool
     */
    protected function connect() {

        $this->dblink = mysqli_init();

        $this->dblink->real_connect(
            $this->dbhost
          , $this->dbuser
          , $this->dbpass
          , $this->dbname
          , $this->port
          , null
          , $this->parameters
        );

        if( mysqli_connect_errno() ) {
            $this->dblink = null;
            throw new sm_exception( mysqli_connect_error(), mysqli_connect_errno() );
        }

        $this->query("SET NAMES '".$this->charset."'");

        return true;

    } // function connect()
	
	
    /**
     * Closes a previously opened database connection
     *
     * @access protected
     * @return bool
     */
    protected function disconnect() {

        if ( $this->dblink ) {
            $res = mysqli_close( $this->dblink );
            $this->dblink = null;
            return $res;
        }

        return true;

    } // function disconnect()


    /**
     * Escapes special characters in a string for use in a SQL statement
     *
     * @access public
     * @param string $str
     * @return string
     */
    public function escape_string( $str ) {

        return $this->dblink->real_escape_string( $str );

    } // function real_escape_string()


    /**
     * Returns last inserted id
     *
     * @access public
     * @return int      последний вставленный идентификатор
     */
    public function get_last_inserted_id() {
		
        return $this->dblink->insert_id;
		
    } // function get_last_inserted_id()


    /**
     * Возвращает результат выполнения запроса
     *
     * @access public
     * @param string $query
     * @param string $limit - значения для ограничения выборки
     * @param string $callback - имя функции, которой будет
     *                           передана для обработки
     *                           каждая строка получаемого рекордсета
     *                           Параметры - номер строки, сама строка
     *                           Результат - строка, которую нужно
     *                           положить в рекордсет
     * @return array (
     *             "affected_rows" => value
     *           , "result"        => array()
     *         )
     */
    public function getResult( $query, $limit = null, $callback = "" ) {

        if( strpos( $query, "select ") == 0 ) {
            $query .= ( isset( $limit ) ? " limit " . $limit : "" );
        }

        $result = $this->query( $query );

        $res = array();
        $res["affected_rows"] = $this->dblink->affected_rows;
        $res["result"] = array();

        if ( !is_bool( $result ) ) {
            while( $row = $result->fetch_assoc() ) {
                $res["result"][] = ($callback == "") ? $row : $callback($row);
            }
        }

        return $res;

    } // function getResult()


    /**
     * Выполняет запрос
     *
     * @access private
     * @param string $query - запрос
     * @return array ( 
     *     "affected_rows" => value
     *   , "result"        => array()
     * )	
     */
    private function query( $query ) {

        $result = $this->dblink->query( $query );
        if ( !$result )
            throw new sm_exception( $this->dblink->error." - ".$query, $this->dblink->errno );

        return $result;

    } //function query()


    /**
     * Функция открытия новой транзакции
     *
     * @access public
     * @return bool 
     */
    public function begin() {

        return ( !$this->dblink ? false : $this->query( "start transaction" ) );

    } // function begin()


    /**
     * Функция закрытия транзакции
     *
     * @access public
     * @return bool
     */
    public function commit() {

        return ( !$this->dblink ? false : $this->query( "commit" ) );

    } // function commit()


    /**
     * Откат транзакции
     *
     * @access public
     * @return bool
     */
    public function rollback() {

        return ( !$this->dblink ? false : $this->query( "rollback" ) );

   } // function rollback()


    /**
     * Обработчик placeholder 't'
     *
     * @param object $bind
     *
     * @return string
     * @access public
     */
    public function placeholder_t( $bind ) {

        if( $bind === null ) {
            return "null";
        }

        if( ! ($bind instanceof sm_date) ) {
            try {
                $bind = new sm_date( $bind );
            } catch( Exception $e ) {
                throw new sm_exception( "Incorrect type of parameter with placeholder 't': ".get_class($bind) );
            }
        }

        $datestring = $bind->getDateAsString();

        if ( !preg_match( "/([0-9]{4}-[0-9]{2}-[0-9]{2}( [0-9]{2}:[0-9]{2}:[0-9]{2})?)/i", $datestring ) ) {
            throw new sm_exception( "Incorrect type 't': ".$datestring );
        } else {
            $arr = date_parse( $datestring );
            if ( $arr['error_count'] + $arr['warning_count'] > 0 ) {
                throw new sm_exception( "Incorrect type 't': ".$datestring );
            }
        }
        return "'".$datestring."'";

    } // function placeholder_t()


    /**
     * Обработчик placeholder 'f', функция now
     *
     * @return string
     * @access public
     */
    public function placeholder_f_now() {

        return "now()";

    } // function placeholder_f_now()


    /**
     * Обработчик placeholder 'f', функция datediff
     * получение разницы между датами
     * startdate вычитается из enddate. Если startdate позже, чем enddate, то возвращается отрицательное значение
     *
     * @param $datepart string
     * @param $startdate string
     * @param $enddate string
     * @return string
     * @access public
     */
    public function placeholder_f_datediff( $datepart, $startdate, $enddate ) {

        if ( in_array( $datepart, array( "y", "m" ) ) ) {
            $result = "(extract(year from ".$enddate.") - extract(year from ".$startdate."))";
            if ( $datepart == "m" )
                $result .= "*12 + extract(month from ".$enddate.") - extract(month from ".$startdate.")";
        } else {
            $result = "datediff(".$enddate.", ".$startdate.")";

            switch ( $datepart ) {
                case "s" :
                    $result .= "*24*60*60";
                    break;
                case "n" :
                    $result .= "*24*60";
                    break;
                case "h" :
                    $result .= "*24";
                    break;
                default :
                    break;
            }
        }

        return $result;

    } // function placeholder_f_datediff()


    /**
     * Обработчик placeholder 'f', функция dateadd
     * добавление интервала к дате
     *
     * @param $datepart string
     * @param $number int
     * @param $date string
     * @return string
     * @access public
     */
    public function placeholder_f_dateadd( $datepart, $number, $date ) {

        return "date_add(".$date.", interval ".$number." ".$this->dateparts[$datepart].")";

    } // function placeholder_f_dateadd()


    /**
     * Обработчик placeholder 'f', функция datepart
     * получение части даты
     *
     * @param $datepart string
     * @param $date string
     * @return string
     * @access public
     */
    public function placeholder_f_datepart( $datepart, $date ) {

        return "extract(".$this->dateparts[$datepart]." from ".$date.")";

    } // function placeholder_f_datepart()


} // class sm_db_MySQL
		
?>
