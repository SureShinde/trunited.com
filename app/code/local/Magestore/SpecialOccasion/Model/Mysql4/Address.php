<?php

class Magestore_SpecialOccasion_Model_Mysql4_Address extends Mage_Core_Model_Mysql4_Abstract
{
	public function _construct(){
		$this->_init('specialoccasion/address', 'address_id');
	}
}