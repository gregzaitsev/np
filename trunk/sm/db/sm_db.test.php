<?php
/**
 * Class test_sm_db
 * 
 * @package ConcertTerminal
 * @version 1.0.0.
 * @category php5 only
 * @copyright Copyright &copy; November 2007, UNITECSYS
 *
 * @uses sm_config
 * @uses sm_exception
 */

Mock::generate('sm_db_MySQL');

class test_sm_db extends UnitTestCase {
	
	
	/**
	 * Объект-заглушка для драйвера СУБД
	 *
	 * @var object
	 */
	public $driver;
	
	
	/**
	 * Объект для тестирования
	 *
	 * @var object
	 */
	public $obj;
	
	
	/**
	 * Число возвращаемых строк
	 *
	 * @var int
	 */
	public $affected_rows;	
	
	
    /**
     * Список поддерживаемых placeholders
     *
     * @var array
     */
    public $valid_placeholders = array( "i", "d", "s", "b", "n", "t" );	
    
    
    /**
     * Параметры для метода query
     *
     * @var array
     */
    public $params;            
    
	
	function setUp() {
		
		$this->driver = new Mocksm_db_MySQL();
		$this->obj = new sm_db( array( "driver" => $this->driver ) );
		$this->affected_rows = rand( 0, 10 );
		$this->driver->setReturnValue( 'getResult', array( 'affected_rows' => $this->affected_rows, 'result' => array() ) ); 
		$query = "".rand();
		$binds = array();
		
		if ( rand(0, 1) == 1 ) {
			$query .= "?".$this->valid_placeholders[rand(0, 5)]."(value)";
			$binds = array( "value" => rand() );
		}
		
		$this->params = array(
			"query" => $query
		  , "binds" => $binds
		  , "limit" => "".rand()
		);
		
	} // function setUp()

	
	function test_query() {

		// Проверка исключений       
        try { 
        	$new_params = $this->params;
        	unset( $new_params["query"] );
			$this->obj->query( $new_params );
            $this->fail();
        } catch( Exception $e ) {
            $this->assertEqual( $e->getMessage(), "Undefined parameter 'query' for query method" );
        }	
        
		try { 
			$new_params = $this->params;
        	$new_params["binds"] = rand();
			$this->obj->query( $new_params );
            $this->fail();
        } catch( Exception $e ) {
            $this->assertEqual( $e->getMessage(), "Incorrect parameter 'binds' for query method" );
        }        
        
        // Проверка выполнения запроса
        if ( count( $this->params["binds"] ) == 0 ) { 
        	$this->driver->expectOnce( 'getResult', array( $this->params["query"], $this->params["limit"] ) );
        } else {
        	$this->driver->expectOnce( 'getResult' );
        }
               
        $this->assertEqual(
            $this->obj->query( $this->params )
          , array( 'affected_rows' => $this->affected_rows, 'result' => array() )
        );             
		
	} // function test_query()
	
	
	function tearDown() {
		
		unset( $this->obj );
        unset( $this->driver );  
        
	} // function tearDown()
	
	
	function test_transactions() {

		$this->driver->expectCallCount( 'begin', 3 );
		$this->driver->expectOnce( 'commit' );
		$this->driver->expectCallCount( 'rollback', 2 );
		$this->driver->setReturnValue( "begin", array( true ) );
		$this->driver->setReturnValueAt( 0, 'commit', array( true ) );
		$this->driver->setReturnValueAt( 1, 'commit', array( true ) );
		$this->driver->setReturnValueAt( 2, 'commit', array( false ) );	
		
		$this->assertFalse( $this->obj->rollback() );
		$this->assertFalse( $this->obj->reset() );
		$this->assertTrue( $this->obj->begin() );
		$this->assertTrue( $this->obj->begin() );
		$this->assertTrue( $this->obj->commit() );
		$this->assertTrue( $this->obj->commit() );
		$this->assertFalse( $this->obj->commit() );
		$this->assertTrue( $this->obj->begin() );
		$this->assertTrue( $this->obj->rollback() );
		$this->assertTrue( $this->obj->commit() );
		$this->assertTrue( $this->obj->begin() );
		$this->assertTrue( $this->obj->reset() );

	} // function test_transactions()


} // class test_sm_db

?>
