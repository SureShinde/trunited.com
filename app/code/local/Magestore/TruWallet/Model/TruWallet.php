<?php

class Magestore_TruWallet_Model_TruWallet extends Mage_Core_Model_Abstract
{
	public function _construct(){
		parent::_construct();
		$this->_init('truwallet/truwallet');
	}
}