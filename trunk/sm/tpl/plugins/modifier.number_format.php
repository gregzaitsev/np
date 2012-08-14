<?php
/**
* Smarty plugin
* @package Smarty
* @subpackage plugins
*/

/**
* Smarty number_format modifier plugin
*
* Type: modifier<br>
* Name: number_format<br>
* Purpose: format number via number_format
* @link http://smarty.php.n­et/manual/en/languag­e.modifier.number.fo­rmat.php
* number_format (Smarty online manual)
* @param float
* @param int
* @param string
* @param string
* @return string
*
* usage: {% $number|number_format:"2":",":" " %}
*/
function smarty_modifier_number_format($number, $decimals=2, $dec_point='.', $thousands_sep=''){
	return number_format($number, $decimals, $dec_point, $thousands_sep);
}

?>