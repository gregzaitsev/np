<?php
/**
 * Class sm_config
 * 
 * @package ConcertTerminal
 * @version 1.0.0.
 * @category php5 only
 * @copyright Copyright &copy; December 2010, ARCA
 *
 */

class sm_config extends sm_config_base {

	// Constructor
	private function __construct() {
	} // function __construct()

	/* Debug Levels (a.k.a Verbosity)
	 * -------------
	 * 0 = no output
	 * 1 = critical errors only
	 * 2 = all errors
	 * 3 = all errors and warnings
	 * 4 =
	 * 5 = default debug level for systems in the field
	 * 6 = 
	 * 7 = all debug messages
	 *
	 * @static
	 * @public
	 */
	public static $debug_level = 7;

	/* Path to mysql exe's.  Leave blank for default behavior of:
	 * \xampp\mysql\bin\
	 * 
	 * @static
	 * @access public
	 */
	public static $mysql_path;

	/**
	 * List of session parameters
	 *
	 * Usually it is setup as domail parameter
	 *
	 * 'save_place'  - ÔËÁÌ‡Í, ÓÔÂ‰ÂÎˇ˘ËÈ „‰Â ı‡ÌËÚ¸ ‰‡ÌÌ˚Â ÒÂÒÒËË, ‚ ·‡ÁÂ ('db') ËÎË ‚ Ù‡ÈÎ‡ı ('files')
	 * 'save_path'   - ÔÛÚ¸ Í Ù‡ÈÎ‡Ï ÒÂÒÒËË
	 * 'maxlifetime' - Ï‡ÍÒËÏ‡Î¸ÌÓÂ ‚ÂÏˇ ÊËÁÌË ÒÂÒÒËË
	 * 'table_properties' - Ï‡ÒÒË‚ Ì‡ÒÚÓÂÍ ‰Îˇ ÓÔÂ‰ÂÎÂÌËˇ Ú‡·ÎËˆ˚ ÒÂÒÒËË Ë ÔÓÎÂÈ Ò ‰‡ÌÌ˚ÏË
	 * 'db_type'     - ÚËÔ ËÒÔÓÎ¸ÁÛÂÏÓÈ ·‡Á˚ ‰‡ÌÌ˚ı ‰Îˇ ÒÂÒÒËË ('MySQL', 'MSSQL')
	 *
	 * @static
	 * @access public
	 * @var array
	 */
	public static $session_params = array(
		"save_place"    => "files"
	  , "save_path"     => "sessions"
	  , "maxlifetime"   => 86400
	  , "table_properties" => array(
			"name"   => "sessions"
		  , "f_id"   => "session"
		  , "f_time" => "session_starts"
		  , "f_data" => "session_data"
		)
	);
	
	
	/**
	 * List of DB connection parameters
	 *
	 * 'dbhost'  - database host
	 * 'dbname'  - database name
	 * 'dbuser'  - database user
	 * 'dbpass'  - database password
	 * 'port'    - db host port number
	 * 'charset' - character set for MySQL connection
	 * 'parameters' - parameters of MySQL connection
	 *
	 * @static
	 * @access public
	 * @var array
	 */
	public static $db_params = array(
			"dbhost"	 => "localhost"
		  , "dbname"	 => "whatdoido"
		  , "dbuser"	 => "root"
		  , "dbpass"	 => ""
		  , "port"		 => 3306
		  , "charset"	 => "utf8"
		  , "parameters"	=> 2
	);
	
	public static $default_password = "8cb2237d0679ca88db6464eac60da96345513964"; // sha1("12345")
	
} // class sm_config

?>
