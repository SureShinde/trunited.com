<?php

class Magestore_ManageApi_Model_Mysql4_Linkshareadvertisers_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
	public function _construct(){
		parent::_construct();
		$this->_init('manageapi/linkshareadvertisers');
	}
}