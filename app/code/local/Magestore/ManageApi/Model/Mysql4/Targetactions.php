<?php

class Magestore_ManageApi_Model_Mysql4_Targetactions extends Mage_Core_Model_Mysql4_Abstract
{
	public function _construct(){
		$this->_init('manageapi/targetactions', 'target_actions_id');
	}
}