<?php

class Magestore_TruGiftCard_Model_Mysql4_Transaction extends Mage_Core_Model_Mysql4_Abstract
{
	public function _construct(){
		$this->_init('trugiftcard/transaction', 'transaction_id');
	}


}