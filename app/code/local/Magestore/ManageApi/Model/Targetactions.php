<?php

class Magestore_ManageApi_Model_Targetactions extends Mage_Core_Model_Abstract
{
	public function _construct(){
		parent::_construct();
		$this->_init('manageapi/targetactions');
	}
}