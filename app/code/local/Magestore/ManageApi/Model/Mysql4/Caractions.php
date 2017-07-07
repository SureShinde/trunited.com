<?php

class Magestore_ManageApi_Model_Mysql4_Caractions extends Mage_Core_Model_Mysql4_Abstract
{
	public function _construct(){
		$this->_init('manageapi/caractions', 'car_actions_id');
	}
}