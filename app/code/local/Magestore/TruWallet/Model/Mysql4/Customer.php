<?php

class Magestore_TruWallet_Model_Mysql4_Customer extends Mage_Core_Model_Mysql4_Abstract
{
	public function _construct(){
		$this->_init('truwallet/customer', 'truwallet_id');
	}
}