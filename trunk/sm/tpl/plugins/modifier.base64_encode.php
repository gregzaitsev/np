<?php
/**
* Smarty plugin
* @package Smarty
* @subpackage plugins
*/

/**
* Smarty base64_encode modifier plugin
*
* Type: modifier<br>
* Name: base64_encode<br>
* Purpose: base64 encode
* @link
* base64_encode ()
* @param string
* @return string
*
* usage: {% $data|base64_encode %}
*
*/
function smarty_modifier_base64_encode( $data ){
	return base64_encode( $data );
}
?>