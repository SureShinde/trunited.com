<?php

class Magestore_ManageApi_Model_Mysql4_Linkshare extends Mage_Core_Model_Mysql4_Abstract
{
	public function _construct(){
		$this->_init('manageapi/linkshare', 'linkshare_id');
	}
}