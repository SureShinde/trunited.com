<?php

class Magestore_ManageApi_Model_Linkshare extends Mage_Core_Model_Abstract
{
	public function _construct(){
		parent::_construct();
		$this->_init('manageapi/linkshare');
	}
}