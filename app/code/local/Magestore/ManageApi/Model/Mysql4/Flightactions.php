<?php

class Magestore_ManageApi_Model_Mysql4_Flightactions extends Mage_Core_Model_Mysql4_Abstract
{
	public function _construct(){
		$this->_init('manageapi/flightactions', 'flight_actions_id');
	}
}