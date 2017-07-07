<?php

class Magestore_ManageApi_Model_Mysql4_Cjactions extends Mage_Core_Model_Mysql4_Abstract
{
	public function _construct(){
		$this->_init('manageapi/cjactions', 'cj_actions_id');
	}
}