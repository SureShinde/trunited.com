<?php

class Magestore_ManageApi_Model_Mysql4_Vacationactions extends Mage_Core_Model_Mysql4_Abstract
{
	public function _construct(){
		$this->_init('manageapi/vacationactions', 'vacation_actions_id');
	}
}