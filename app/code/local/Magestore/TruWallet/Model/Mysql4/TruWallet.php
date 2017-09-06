<?php

class Magestore_TruWallet_Model_Mysql4_TruWallet extends Mage_Core_Model_Mysql4_Abstract
{
	public function _construct(){
		$this->_init('truwallet/truwallet', 'truwallet_id');
	}
}