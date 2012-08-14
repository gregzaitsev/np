<?php
/**
 * Class sm_db_MSSQL
 * работа с базой данных MSSQL
 * 
 * @package ConcertTerminal
 * @version 1.0.0.
 * @category php5 only
 * @copyright Copyright &copy; November 2007, UNITECSYS
 *
 * @uses sm_exception
 * @uses sm_config
 */

class sm_db_MSSQL {


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
     * Transaction level
     *
     * @var integer
     * @access private
     */
    protected $transaction_level;   	


    /**
     * Список соответствий поддерживаемых значений параметра datepart со значениями MSSQL для функции datediff
     * 'y' - year, 'm' - month, 'd' - day, 'h' - hour, 'n' - minute, 's' - second
     *
     * @access private
     * @var array
     */
    private $dateparts = array( "y" => "yy"
                              , "m" => "mm"
                              , "d" => "dd"
                              , "h" => "hh"
                              , "n" => "n"
                              , "s" => "s"
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

        $this->connect();

        $this->transaction_level = 0;

    } // function __construct()


    /**
     * Деструктор класса
     *
     */
    function __destruct() {

    } // function __destruct()


    /**
     * Open MS SQL server connection
     * 
     * @access protected
     * @return bool
     */
    protected function connect() {

        $this->dblink = @mssql_connect( $this->dbhost
                                      , $this->dbuser
                                      , $this->dbpass
        );

        if ( !$this->dblink )
			throw new sm_exception( mssql_get_last_message() );

        $result = mssql_select_db( $this->dbname, $this->dblink );
        if ( !$result )
            throw new sm_exception( "Database was not selected" );

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
            $res = mssql_close( $this->dblink );
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

        return preg_replace("/'/", "''", $str);

    } // function real_escape_string()


    /**
     * Returns last inserted id
     *
     * @access public
     * @return int      последний вставленный идентификатор
     */
    public function get_last_inserted_id() {
		
        $res = $this->getResult( "SELECT @@IDENTITY as id" );
        if( $res["affected_rows"] < 1 ) {
            throw new sm_Exception( "Database error" );
        }
        return $res[ "result" ][0]["id"];
		
    } // function get_last_inserted_id()


    /**
     * Возвращает результат выполнения запроса
     *
     * @access public
     * @param string $query
     * @param string $limit - значения для ограничения выборки ('M, N' либо 'N')
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

        $with_limit = false; // if use limit in query
        if( strpos( $query, "select ") == 0 ) {
            if ( isset( $limit ) ) {
                $limits = explode( ",", $limit );
                if ( !isset( $limits[1] ) ) {
                    $limits[1] = intval( $limits[0] );
                    $limits[0] = 1;
                }

                $query = "declare @handle int, @rows int " .
                    "exec sp_cursoropen @handle OUT, '" . $query . "', 1, 1, @rows OUT " .
                    "exec sp_cursorfetch @handle, 16, " . $limits[0] . ", " . $limits[1] . " " .
                    "exec sp_cursorclose @handle";

                $with_limit = true;
            }
        }

        $result = $this->query( $query );

        $res = array();
        $res["affected_rows"] = mssql_rows_affected( $this->dblink );
        $res["result"] = array();

        if ( $with_limit ) {
            $res["affected_rows"] = 0;
            mssql_next_result( $result );
        }

        if ( !is_bool( $result ) ) {
            while( $row = mssql_fetch_assoc( $result ) ) {
                $res["result"][] = ($callback == "") ? $row : $callback($row);
                if ( $with_limit )
                    $res["affected_rows"]++;
            }
            mssql_free_result( $result );
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

        set_error_handler( array( "sm_db_MSSQL", "query_error_handler" ) );

        $result = mssql_query( $query, $this->dblink );

        restore_error_handler();

        if( !$result ) {
            throw new sm_exception( mssql_get_last_message()." - ".$query );
        }

        return $result;

    } // function query()


    /**
     * Обработчик ошибок, возникающих при выполнении запроса к базе
     *
     * Превращает ошибку в исключение (перенаправляет выполнение
     * по ветке обработки исключений)
     */
    public static function query_error_handler( $errno, $errstr, $errfile, $errline ) {

		sm_debug::write("DB Error: errstr: $errstr, errno: $errno, errfile: $errfile, errline: $errline");

        restore_error_handler();
        throw new sm_exception( $errstr );
        return;

    } // function query_error_handler()



    /**
     * Функция открытия новой транзакции
     *
     * @access public
     * @return bool 
     */
    public function begin() {

        return ( !$this->dblink ? false : $this->query( "begin transaction" ) );

    } // function begin()


    /**
     * Функция закрытия транзакции
     *
     * @access public
     * @return bool
     */
    public function commit() {

        return ( !$this->dblink ? false : $this->query( "commit transaction" ) );

    } // function commit()


    /**
     * Откат транзакции
     *
     * @access public
     * @return bool
     */
    public function rollback() {	

        return ( !$this->dblink ? false : $this->query( "rollback transaction" ) );

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

        return "getdate()";

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

        return "datediff(".$this->dateparts[$datepart].", ".$startdate.", ".$enddate.")";

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

        return "dateadd(".$this->dateparts[$datepart].", ".$number.", ".$date.")";

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

        return "datepart(".$this->dateparts[$datepart].", ".$date.")";

    } // function placeholder_f_datepart()


} // class sm_db_MSSQL
		
?>
