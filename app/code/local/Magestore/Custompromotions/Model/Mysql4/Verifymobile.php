<?php

class Magestore_Custompromotions_Model_Mysql4_Verifymobile extends Mage_Core_Model_Mysql4_Abstract
{
	public function _construct(){
		$this->_init('custompromotions/verifymobile', 'verify_id');
	}
}