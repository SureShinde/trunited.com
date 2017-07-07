<?php

class Magestore_ManageApi_Model_Mysql4_Hotelactions extends Mage_Core_Model_Mysql4_Abstract
{
	public function _construct(){
		$this->_init('manageapi/hotelactions', 'hotel_actions_id');
	}
}