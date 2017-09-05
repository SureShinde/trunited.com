<?php

class Magestore_Interest_Model_Mysql4_Interest extends Mage_Core_Model_Mysql4_Abstract
{
	public function _construct(){
		$this->_init('interest/interest', 'interest_id');
	}
}