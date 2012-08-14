<?php
/**
 * Class ent_visitor
 *
 * @package ConcertTerminal
 * @version 1.0.0.0
 * @category php5 only
 * @copyright Copyright &copy; November 2007, UNITECSYS
 *
 */

class ent_visitor extends ent_visitor_base {

	/**
	 * Login
	 * @access public
	 * @var int
	 */
	public $login = null;

	/**
	 * SHA1 of Password
	 * @access public
	 * @var int
	 */
	public $password = null;
	
	/**
	 * ������������� ������������
	 *
	 * @access public
	 * @var int
	 */
	public $user_id = NULL;

	/**
	 * ������������� ����������
	 *
	 * @access public
	 * @var int
	 */
	public $employee_id = NULL;

	/**
	 * ������������� ��������� ������������� �����
	 *
	 * @access public
	 * @var int
	 */
	public $pos_id = NULL;

	/**
	 * ����� ��������� ����������� �������� ��� ���������� ����������
	 *
	 * @access public
	 * @var string
	 */
	public $uri = NULL;

	/**
	 * ������� ��������������
	 *
	 * @access public
	 * @var bool
	 */
	public $is_admin = false;

	/**
	 * ������������� �������� ������ ������������
	 *
	 * @access public
	 * @var bool
	 */
	public $session_id = 0;

	/**
	 * ������������� �������� ������� �����
	 *
	 * @access public
	 * @var int
	 */
	public $work_session_id = 0;

	/**
	 * Work Session last returned by SyncTerminal.
	 *
	 * @access public
	 * @var int
	 */
	public $bag_open_work_session_id = 0;

	/**
	 * ������������� ������� ����������
	 *
	 * @access public
	 * @var bool
	 */
	public $current_transaction_id = 0;

	/**
	 * �������������� �������������� �������� ���������� (������ ��������)
	 *
	 * @access public
	 * @var array
	 */
	public $current_transaction_ids = NULL;

	/**
	 * ��� ������������
	 *
	 * @access public
	 * @var string
	 */
	public $user_name = NULL;


	/**
	 * ����� (unix timestamp) ����������� �������� ������������ �� �������� ��������� ������ ����� (Return Till)
	 *
	 * @access public
	 * @var int
	 */
	public $finishingDepositTimeout = 0;

	/**
	 * ����� (unix timestamp) ����������� �������� ������������ � ������ ������������� ���������� �������� ��� ������ ����� (Return Till)
	 *
	 * @access public
	 * @var int
	 */
	public $problemDepositTimeout = 0;


	/**
	 * ����� ��������� ���������� ��������� ����� �����
	 *
	 * @access public
	 * @var time
	 */
	public $led_banknotes_output = NULL;

	/**
	 * ����� ��������� ���������� ��������� ����� �����
	 *
	 * @access public
	 * @var time
	 */
	public $led_coins_output = NULL;

	/**
	 * ��������� ��������� �����������
	 *
	 * @static
	 * @access public
	 * @var array
	 */
	public $leds_last_state = array(
		   0 => 0
		 , 1 => 0
		 , 2 => 0
		 , 3 => 0
		 , 4 => 0
	 );

	/**
	 * Additional parameters for current PHP SESSION
	 * @var array
	 */
	public $parameters = array(
		 "info_message_start_ts" => 0 // information message for start screen
	);

	/**
	 * �������� ��������� "�������� ������ �� ��������� �����"
	 *
	 * @access public
	 * @var time
	 */
	public $getBanknotesFromOutputEmulate = false;

	/**
	 * User has been bio identified within this session
	 *
	 * @access public
	 * @var int
	 */
	public $bioIDd = 0;

	/**
	 * User uses barcode to initiate the session if barcode_id is non zero
	 *
	 * @access public
	 * @var int
	 */
	public $barcode_id = 0;

	/**
	 * ���������� �� ��������, ������� ���������� ���������� (��������, atm_machine::acp_checkStatus)
	 *
	 * @access public
	 * @var array
	 */
	public $operation = null;

	/**
	 * bagList data for Bags->List Bags->List Open Bags and List Closed Bags
	 *
	 * @access public
	 * @var array
	 */
	 public $bagListData;

	/**
	 * �������� ������� ����������
	 *
	 * @access public
	 */
	public function newInstance() {

		 self::$instance = new ent_visitor();

	} // function newInstance()

	/**
	 * Current Transaction ID for New Transaction System.
	 *
	 * @access public
	 * @var int
	 */
	public $currentTransactionId = 0;

	public $transData = array();


} // class ent_visitor

?>
