<?php

class Magestore_Custompromotions_Model_Verifymobile extends Mage_Core_Model_Abstract
{
	const STATUS_VERIFIED = 1;
	const STATUS_UNVERIFIED = 2;

	const VERIFY_ERROR_NON_EXIST = 1;
	const VERIFY_ERROR_VERIFIED = 2;
	const VERIFY_ERROR_OTHER = 3;
	const VERIFY_SUCCESS = 4;

	public function _construct(){
		parent::_construct();
		$this->_init('custompromotions/verifymobile');
	}
}