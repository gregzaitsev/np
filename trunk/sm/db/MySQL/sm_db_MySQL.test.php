<?php
/**
 * Class test_sm_db_MySQL
 * 
 * @package ConcertTerminal
 * @version 1.0.0.
 * @category php5 only
 * @copyright Copyright &copy; November 2007, UNITECSYS
 */

class test_sm_db_MySQL extends UnitTestCase {
	
	
	function test_createAndGetResult() {
		
		// Проверка исключений       
        try { 
        	$props = array( "dbname", "dbuser", "dbpass" );
        	$params = array( $props[rand( 0, 2 )] => "".rand() );
        	$obj = new sm_db_MySQL( $params );
            $this->fail();
        } catch( Exception $e ) {
            $this->pass();
        }			
        
        // Проверка успешного соединения
        $obj = new sm_db_MySQL();
        $this->assertIsA( $obj, 'sm_db_MySQL' );
        
        // Проверка правильно возвращаемого результата
        $index = rand( 2, 10 );
        $query = "";
        $result = array();
        $result["affected_rows"] = $index;
        $result["result"] = array();
        for ( $i = 1; $i <= $index; $i++ ) {
        	$query .= "select " . $i;
        	if ( $i != $index ) {
        		$query .= " union ";
        	}
        	$result["result"][] = array( 1 => $i );
        }
        
        $this->assertEqual( $obj->getResult( $query ), $result );
        
        // Проверка работы запроса select вместе с limit
        $limit_start = rand( 2, 10 );
        $limit_end = rand( 2, 10 );
        
        if ( $limit_start < $limit_end ) {
        	$limit = $limit_start . ", " . $limit_end;
        } else {
        	$limit = $limit_end;
        	$limit_start = 0;
        }
        
        $index = 10;
        $query = "select * from (";
        $result = array();
        $result["affected_rows"] = 0;
        $result["result"] = array();
        for ( $i = 1; $i <= $index; $i++ ) {
        	$query .= "select " . $i;
        	if ( $i != $index ) {
        		$query .= " union ";
        	}
        	if ( $i > $limit_start && $i <= $limit_end + $limit_start ) {        	
        		$result["result"][] = array( 1 => $i );
        		$result["affected_rows"]++;
        	}	
        }                
        $query .= ") as tmp limit " . $limit;
        
        $this->assertEqual( $obj->getResult( $query ), $result );

	} // function test_createAndGetResult()


    function test_placeholders() {

        $obj = new sm_db_MySQL();
		// Проверка исключений       
        try { 
        	$bind = rand();
        	$obj->placeholder_t( $bind );
            $this->fail();
        } catch( Exception $e ) {
            $this->assertEqual( $e->getMessage(), "Incorrect type 't': ".$bind );
        }

        try { 
        	$bind = "2000-".rand(13, 20)."-10 00:00:00";
        	$obj->placeholder_t( $bind );
            $this->fail();
        } catch( Exception $e ) {
            $this->assertEqual( $e->getMessage(), "Incorrect type 't': ".$bind );
        }

        $bind = rand(1970, 2030)."-".str_pad( rand(1, 12), 2, "0", STR_PAD_LEFT )."-".str_pad( rand(1, 28), 2, "0", STR_PAD_LEFT )." ".str_pad( rand(0, 23), 2, "0", STR_PAD_LEFT ).":".str_pad( rand(0, 59), 2, "0", STR_PAD_LEFT ).":".str_pad( rand(0, 59), 2, "0", STR_PAD_LEFT );
        $this->assertEqual( $obj->placeholder_t( $bind ), "'".$bind."'" );
        unset( $obj );

    } // function test_placeholders()


} // class test_sm_db_MySQL

?>
