<?php
/**
 * Class sm_sess
 * работа с сессией
 *
 * @package ConcertTerminal
 * @category php5 only
 * @copyright Copyright &copy; ArcaTech Systems, 2011
 */

class sm_sess {

    /**
     * Путь к файлам сессии
     *
     * @access public
     * @var string
     */
    public $sess_save_path;

    /**
     * Максимальное время жизни сессии
     *
     * @access private
     * @var int
     */
    private $maxlifetime;

	
    /**
     * Параметр регулирующий действия с сессией
     * 
     * @access private
     * @var string
     */
    private $access;


    /**
     * Экземпляр класса
     *
     * @access private
     * @var object
     */
    private static $instance = null;    

    /**
     * Конструктор класса
     * 
     * @access private
     */
    protected function __construct() {
	
        // Установка пути к файлам сессии
		$save_path = isset( $params['save_path'] ) ? $params['save_path'] : sm_config::$session_params["save_path"];
		//sm_debug::write("save_path = $save_path", 7);
		
		$root = realpath(dirname( __FILE__ ).'/../../../');
		$save_path = "$root/".$save_path;
		
        session_save_path( $save_path );

    } // function __construct()
	
	
    /**
     * Функция получения экземпляра класса
     * 
     * @param string $access - 'no', 'read', 'write'
     * @param array  $params - настройки для переопределения конфигурации
     *                         'driver' - объект-драйвер для работы с сессией
     *                         'maxlifetime' - время жизни сессии
     * @return object
     */
    public static function getInstance( $access, $params = array() ) {		
        
        switch( $access ) {
            case "no"    :
            case "read"  :
            case "write" :            	
                break;
            default      :
                throw new sm_exception( "Incorrect ACP parameter 'sess'" );
        }    	
		
		//sm_debug::write("session_id = ".session_id(), 7);

        if( session_id() ) {
            session_write_close();
        }

        if( !isset( self::$instance ) ) {
			//sm_debug::write("Creating new sm_sess", 7);
            self::$instance = new sm_sess();
        }

        self::$instance->maxlifetime = (
            isset( $params['maxlifetime'] )
            ? $params['maxlifetime']
            : sm_config::$session_params["maxlifetime"]
        );
		
		//sm_debug::write("Max life time = ".self::$instance->maxlifetime, 7);

        self::$instance->access = $access;
		
		//sm_debug::write("access = $access", 7);

        // Установка функций для работы с сессией
        $seth = session_set_save_handler( 
            array(& self::$instance, '_open')
          , array(& self::$instance, '_close')
          , array(& self::$instance, '_read')
          , array(& self::$instance, '_write')
          , array(& self::$instance, '_destroy')
          , array(& self::$instance, '_gc')
        );	
		
		//if ($seth) sm_debug::write("session_set_save_handler successful", 7);
		//else sm_debug::write("session_set_save_handler FAILED", 7);

        register_shutdown_function( array(& self::$instance, 'shutdown') );

		//sm_debug::write("session_id = ".session_id(), 7);
		
        if( !session_id() ) {
			//sm_debug::write("Calling session_start", 7);
            session_start();
        }      	
		
		//sm_debug::write("After start: session_id = ".session_id(), 7);
        
        return self::$instance;
		
    } // function getInstance()
	


    /**
     * Функция завершения работы скрипта
     *
     * Сохраняет в сессии объект ent_visitor, после чего закрывает сессию
     */
    function shutdown() {

		//sm_debug::write("Session shutdown. session_id = ".session_id(), 7);
		
        $_SESSION["Visitor"] = ent_visitor::getInstance();
        session_write_close();

    } // function shutdown()

	


    /**
     * Functions which are used for storing and retrieving data associated with a session
     */	

    /**
     * Функция открытия сессии
     *
     * @param string $save_path - путь для хранения файлов сессии
     * @param string $session_name - название сессии
     * @return bool
     */
    function _open( $save_path, $session_name )	{
	
		sm_debug::write("save_path = $save_path", 7);
        $this->sess_save_path = $save_path;
        return true;
		
    } // function _open()


    /**
     * Функция закрытия сессии
     *
     * @return bool
     */
    function _close() {
	
		//sm_debug::write("", 7);
        $this->_gc( $this->maxlifetime );
        return true;

    } //function _close()
	
	
    /**
     * Функция чтения сессии
     *
     * @param string $id - идентификатор сессии
     * @return string
     */
    function _read( $id ) {

		$access = $this->access;
		$maxlifetime = $this->maxlifetime;

        $sess_file = $this->safePath($this->sess_save_path."/sess_".$id);
		
		//sm_debug::write("sess_file = $sess_file, access = $access", 7);
        if( $access == 'no') return '';
		
		if (!file_exists( $sess_file )) {
			sm_debug::write("Seesion file does not exist.", 5);
			return '';
		}
        
        $filetime = filemtime( $sess_file );
		
		//sm_debug::write("filetime = $filetime", 7);

        if ($filetime === FALSE) return '';
		
		$tmp = $filetime+$maxlifetime;
		//sm_debug::write("filetime + maxlifetime = $tmp"."time = ".time(), 7);

        if($filetime + $maxlifetime < time()) {
            return '';
        } else {
			$get_cont_return = (string) @file_get_contents( $sess_file );
			//sm_debug::write("Session file contents = $get_cont_return", 7);
			return $get_cont_return;
        }
  		
    } // function _read()

	
    /**
     * Функция записи сессии
     *
     * @param string $id - идентификатор сессии
     * @param string $sess_data - данные для записи
     * @return bool
     */
    function _write( $id, $sess_data ) {
	
		$access = $this->access;
	
        if( $access == 'write' ) {
            $sess_file = $this->safePath($this->sess_save_path."/sess_".$id); 
			
			//sm_debug::write("sess_file = $sess_file", 7);
			
            if( $fp = @fopen($sess_file, "w") ) {
				//sm_debug::write("Session file was open OK, writing session data: $sess_data", 7);
			
                $return = fwrite($fp, $sess_data);
				
				//sm_debug::write("Write return = $return", 7);
				
                fclose($fp);
                return $return;
            } else {
				sm_debug::write("Cannot open session file at path (Does the folder exist?): $sess_file", 1);
			}
        }
        return false;

    } // function _write()


    /**
     * Функция уничтожения сессии
     *
     * @param string $id - идентификатор сессии
     * @return bool
     */
    function _destroy( $id ) {
	
		//sm_debug::write("", 7);
        $sess_file = $this->safePath($this->sess_save_path."/sess_".$id);
        return ( @unlink( $sess_file ) );

    } // function _destroy()

	
    /**
     * Обработчки ошибок для сборщика мусора для сессии
     *
     * @param int $errno
     * @param string $errstr
     * @param string $errfile
     * @param int $errline
     */
    private static function gc_error_handler( $errno, $errstr, $errfile, $errline, $errcontext ){
        #restore_error_handler();
        #sm_debug::write( "[SESSION GC] [".$errno."] ".$errstr.'|'.$errfile.'|'.$errline.'|'.print_r($errcontext,1) );
        return;
    } // function gc_error_handler()

    /**
     * Функция сборки мусора сессии
     *
     * @param int $maxlifetime - максимальное "время жизни" сессии
     * @return bool
     */
    function _gc( $maxlifetime ) {

        set_error_handler( array( "sm_sess", "gc_error_handler" ) );
        foreach( glob($this->safePath($this->sess_save_path."/sess_*")) as $filename ) {
            $time = 0 + @filemtime($filename);
            if( $time > 0 && $time + $maxlifetime < time() ) {
                @unlink($filename);
            }
        }
        restore_error_handler();
        return true;

    } // function _gc()

    /**
     * End of Functions which are used for storing and retrieving data associated with a session
     *
     */

	private function safePath($path) {
		return str_replace("/", DIRECTORY_SEPARATOR, $path);
	}


} // class sm_sess

?>
