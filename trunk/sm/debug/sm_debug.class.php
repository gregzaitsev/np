<?php
/**
 * Class sm_debug
 *
 * Supports writing debug messages into
 *   .../logs/debug/$filenameprefix_datestamp.csv
 * in this csv format:
 *   <timestamp>, <class::method> | <file: line:>, LV <verbosity>, <message>
 *  
 * Debug Levels (a.k.a Verbosity)
 * -------------
 * 0 = no output
 * 1 = critical errors only
 * 2 = all errors
 * 3 = all errors and warnings
 * 4 =
 * 5 = all errors and warnings and most important successful 
 *     operations (hardware commands and transactions)
 * 6 = 
 * 7 = all debug messages
 *
 * @package ConcertTerminal
 * @version 1.0.0.
 * @category php5 only
 * @copyright Copyright &copy; December 2010, ARCA
 */

class sm_debug {

	private static $requestId = null;

    /**
     * write 
     *
     * Write the message to <datestamp>_debug.csv.
     *
     * @static
     * @access public
     * @param string $message - the debug message to write to the file
     * @param int    $verbosity - (0=no output, 1=critical only, 7=all(default)
     * @return - none
     */
    static public function write( $message, $verbosity = 7) {
        sm_debug::writefnp($message, "debug", $verbosity);
    }


    /**
     * dumpVar
     *
     * Prints an array or message to the write() method, above, so
     * that they end up in the same primary debug log files.
     *
     * @static
     * @access public
     * @param mixed $var - the debug array or message
     * @param int    $verbosity - (0=no output, 1=critical only, 7=all(default)
     * @return - none
     */
    static public function dumpVar($var, $verbosity = 7) {

        if (is_array($var)) {

            sm_debug::write("Array", $verbosity);

            foreach ($var as $key => $val){
                sm_debug::write("[$key] = ", $verbosity);
                sm_debug::dumpVar($val, $verbosity); // recursive
            }
    
        } else {
            sm_debug::write("  $var", $verbosity);
        }
    }

    /**
     * writeEx
     *
     * Writes a debug message to a file with a specific filenameprefix. This
     * method simply passes through to writefnp (private method), and could 
     * be removed if writefnp is made public.
     *
     * @static
     * @access public
     * @param string $message - the debug message to write to the file
     * @param string $filenameprefix - prefix for output filename
     * @param int    $verbosity - (0=no output, 1=critical only, 7=all(default)
     * @return - none
     */
    static public function writeEx( $message, $filenameprefix, $verbosity = 7) {
        sm_debug::writefnp($message, $filenameprefix, $verbosity);
    }


    /**
     * writefnp
     *
     * Writes a debug message to a file in csv format.
     * The output file is .../logs/debug/$filenameprefix_datestamp.csv.
     *
     * This method also rotates the log file (to <filename>.csv.old) when it
     * exceeds the maximum configured size, and checks for/removes out of date
     * log files.
     *
     * @static
     * @access private
     * @param string $message - the debug message to write to the file
     * @param string $filenameprefix - prefix for output filename
     * @param int    $verbosity - (0=no output, 1=critical only, 7=all(default)
     * @return - none
     */
    static private function writefnp( $message, $filenameprefix, $verbosity = 7) {

        // verbosity = 0 means to print no logs at all
        if ($verbosity == 0) return;

        // Don't print logs with higher verbosity that the current level.
        if ($verbosity > sm_config::$debug_level) return;

        // All timestamps in logs are on EST? 
        date_default_timezone_set('America/New_York');

        // $dt is date/timestamp for the log (including milliseconds)
        $utimestamp = microtime(true);
        $timestamp = floor($utimestamp);
        $milliseconds = number_format(($utimestamp - $timestamp) * 1000, 3);
        $dt = date("y-M-d,G:i:s").sprintf(".%03d", $milliseconds);

		// Make a bar graph of '*' to reflect the verbosity of the message. 
		$verbosityBar = self::getVerbosityBar($verbosity);

        // Assign $caller to (in order of preference) :
        // (1) class::function
        // (2) -- file: filename line: lineNum
        // (3) --
        $caller = '';
        $backtrace = debug_backtrace();

        // Crawl up the stack until we arrive at a method outside of sm_debug
        // class.  Knowing that write() was called from sm_debug is useless and
        // decreases utility of the output.  2 is correct in most cases, unless
        // called from varDump, print_r, or tquery.
        $crawl_stack = 2;
        while (isset($backtrace[$crawl_stack]['class']) && $backtrace[$crawl_stack]['class'] == 'sm_debug') {
            $crawl_stack++;
        }
        if (count($backtrace) - 1 < $crawl_stack) {
            $crawl_stack = count($backtrace) - 1;
        }

        if (isset($backtrace[$crawl_stack]['class']) && isset($backtrace[$crawl_stack]['function'])) {
            $caller = $backtrace[$crawl_stack]['class']."::".$backtrace[$crawl_stack]['function'];

        } else if (isset($backtrace[$crawl_stack]['file']) && isset($backtrace[$crawl_stack]['line'])) {
            $caller = '-- file: '.$backtrace[$crawl_stack]['file']." line: ".$backtrace[2]['line'];

        } else {
            $caller = '-- ';
        }

		$reqId = self::$requestId;

        // This file is in .../Terminal/term/trunk/sm/debug. Find path
        // to .../Terminal/logs/debug.
        $root = realpath(dirname( __FILE__ ).'/../../../');
        $path = "$root/logs/php/";
		//$path = "Z:/";

        // Create the full pathname to the output file.
        $debug_file_name = date("Ymd")."_".$filenameprefix.".csv";
        $fn = $path.$debug_file_name;

        // What will the size of the log file be (in Mb) after writing to it?
        $bytes_per_mb = 1048576; // 1024 * 1024
        $total_log_size = (sm_debug::GetFileSize($fn) + strlen($message))/$bytes_per_mb;

        // Will the log file be bigger than the configured amount? (150Mb on 2011-Jan)
        if ($total_log_size > sm_config::$max_log_size){

        // Make the current log file into the new backup log file
        $old_fn = $fn.".old";
        if (file_exists($old_fn)){
            unlink($old_fn);
        }
        rename($fn, $old_fn);
        }

        // Write the log file in csv format.
        $f = fopen($fn, "a+");
        if ($f == FALSE) 
            die ("Cannot open or create debug log file at: $path. Check if the folder exists");
        fwrite($f, "$dt $verbosity $verbosityBar $reqId $caller, $message\n");
        fclose($f);

		// Only run remove_old_log once per HTTP request
		static $runOnce = false;
		if (! $runOnce) {
			$runOnce = true;
        	sm_debug::remove_old_log($path);
		}
    }


    /**
     * remove_old_log
     *
     * Delete one log file older than than the limit, if one is found.
     * 
     * @static
     * @access private
     * @param string $path - The directory to search for old logs
     * @return - none
     */
     static private function remove_old_log($path) {

        $limit = sm_config::$log_longevity;

        $files = scandir($path);
        foreach ($files as $f) {

            // skip directories
            $full_path = $path."/".$f;
            if (!is_dir($full_path)) {

                // Has the file remained unmodified for the last $limit days?
                if (filemtime($full_path) < strtotime("- $limit days")) {

                    unlink($full_path);

                    // only delete one file per method call, so break out of foreach
                    break;
                }
            }
        }
    }
    
    
    /**
     * GetFileSize 
     * 
     * Returns the size of the given file.
     *
     * @static
     * @access private
     * @param string $fn - absolute pathname of file
     * @return int - file size in bytes, or 0 on failure
     */
    static private function GetFileSize($fn) {

        // Why wasn't native PHP fuction used?
        //return filesize($fn);
            
        $f = NULL;
        $retval = 0;
        
        // "a" = append
        // "t" = translate "\n" to "\r\n" on Windows
        $f = fopen($fn, "at");
    
        if ($f) {
            // if we've opened for appending, isn't the file
            // pointer already to the end of the file? Is
            // this fseek necessary?
            fseek($f, 0, SEEK_END);
    
            $retval = ftell($f);
            fclose($f);
        }

        return $retval;
    }

    /**
     * print_r 
     * 
     * Uses PHP's print_r to write formatted output to the debug log.
     *
     * @static
     * @access public
     * @param mixed  $val       - value to output
     * @param int    $verbosity - (0=no output, 1=critical only, 7=all(default)
     * @return int - file size in bytes, or 0 on failure
     * @author Russ Adams
     */
    static public function print_r($val, $verbosity = 7) {
        sm_debug::write("\n".print_r($val, true), $verbosity);
    }

    /**
     * tquery
     * 
     * Best effort attempt to print the last translated query to debug log.
     *
     * @static
     * @access public
     * @param mixed  $preface   - string to tag the query with for easy searchability.
     * @param int    $verbosity - (0=no output, 1=critical only, 7=all(default)
     * @return int - file size in bytes, or 0 on failure
     * @author Russ Adams
     */
    static public function tquery($preface = "", $verbosity = 7) {
        if (class_exists("sm_config")) {
            if (isset(sm_config::$db)) {
                $output = "";
                if ($preface != "") {
                    $output .= $preface . ": ";
                }
                $output .= sm_config::$db->translated_query;
                sm_debug::print_r($output, $verbosity);
            }
        }
    }

    /* backtrace
     * Print a backtrace to the debug log.
     *
     * @static
     * @access public
     */
    static public function backtrace() {
        sm_debug::print_r(debug_backtrace(false));
    }

    /**
     * getVerbosityBar
     * 
     *  Make a bar graph of '*' to reflect the verbosity of the message. Print more
	 *  *-s for more important messages (i.e. print nothing for verbosity = 7, and 
	 *  '******' for verbosity=1). Produce a fixed-width result
     * @static
     * @access private
     * @param int    $verbosity - (0=no output, 1=critical only, 7=all(default)
     * @return string - fixed-width string of '*'-s 
     */
	static private function getVerbosityBar($verbosity) {
		$numSymbols = sm_config::$debug_level - $verbosity;

		$syms = str_repeat('*', $numSymbols);
		$width = sm_config::$debug_level - 1;
		return sprintf("%-{$width}s", $syms);
	}

    /**
     * setDebugTransId
     * 
     * Make a unique id for all logs related to a particular HTTP request.

     * @static
     * @access public
     * @param string uri - used to make the unique id
     */
	static public function setDebugTransId ($uri) {
		
		// concatenate the HTTP request URI with the current time (including microseconds) 
		// to generate a moderately unique value, then run it through md5 to get something
		// that should be (near-cosmic) unique.
		$hashVal = md5($uri . microtime(true));

		// Set the last four digits as the requestId 
		self::$requestId = substr($hashVal, -4);
	}



 
}
?>
