<?php
/**
 * Class sm_ent
 *
 * @package ConcertTerminal
 * @version 1.1.0.
 * @category php5 only
 * @copyright Copyright &copy; December 2010, ARCA
 *
 */

class sm_ent extends sm_ent_base {

	/**
	 * Constructor
	 *
	 * @access public
	 */
	function __construct() {
		$this->db = sm_config::$db ? sm_config::$db : new sm_db();
	} // function __construct()

	/**
	 * переустановка объекта подключения к БД
	 * @param $db - объект подключения к БД
	 * @access public
	 */
	public function setDB( sm_db $db ){
		if( $this->db )
			unset( $this->db );
		$this->db = $db;
	}

	/**
	 * возврат значения свойства объекта
	 *
	 * @access public
	 * @return object
	 */
	public function __get( $name ){
		if (isset($this->items)) {
			return isset($this->items[$name])?$this->items[$name]:NULL;
		}
		throw new sm_exception("Error getting property! Property '$name' isn't defined for class ".get_class($this));
	}

	/**
	 * установка значения свойства объекта
	 *
	 * @access public
	 * @return object
	 */
	public function __set( $name, $value ){
		if (isset($this->items)) {
			$this->items[$name] = $value;
		} else {
			throw new sm_exception("Error setting property! Property '$name' isn't defined for class ".get_class($this));
		}
	}

	/**
	 * Returns bool to indicate whether member isset or not.
	 *
	 * @access public
	 * @return bool
	 */
	public function __isset ($name){
		if (isset($this->items)){
			return isset($this->items[$name]);
		}
		return false;
	}

} // class sm_ent

?>
