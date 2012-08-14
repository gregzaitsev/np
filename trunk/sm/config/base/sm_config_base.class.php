<?php

class sm_config_base {

	public static $root = '';

	// Timeout for central server connection, drop connection after 1 hour
	public static $CS_TIMEOUT = 240;

	// Current connection to database (connection within current atm)
	public static $db = null;

	// Main application title
	public static $applicationTitle = "Project tracking system";

	// Application release / version
	public static $applicationVersion = "1.0";

	// Language ID
	public static $language = "en";

	// Locale array
	public static $localization = null;

	// Maximum size of log file in megabytes.  Please note that this cannot be
	// made into a config table option b/c sm_debug::write() in places where
	// the database connection has been made yet
	public static $max_log_size = 150;

	// Number of days that a log can remain on disk.
    public static $log_longevity = 30;

	// Debug mode flag
	public static $debug = true;

	// Current atm/acp
	public static $atm = '';
	public static $acp = '';

	public static $parameters = array();

	private function __construct() {

	} // function __construct()


	// GZ DP101: Function session_driver has been removed when simplifying session code
	public static function db_driver( $params = array() ) {
		return new sm_db_MySQL( $params );
	} // function db_driver()

	public static function show404() {

		if( sm_config::$debug ) {
			sm_config::show404_debug();
		} else {
			sm_config::show404_product();
		}

	} // function show404()

	public static function show404_debug() {

		$request_uri = $_SERVER['REQUEST_URI'];
		echo "ERROR 404: $request_uri";
		echo "<hr>";

		echo "<pre>";
		print_r( $_SERVER );
		echo "</pre>";
		die();

	} // function show404_debug()


	public static function show404_product() {

		ob_end_clean();
		header( "HTTP/1.1 404 Not Found" );
		header( "Status: 404 Not Found"	 );
		die();

	} // function show404_product()


} // class sm_config_base

?>
