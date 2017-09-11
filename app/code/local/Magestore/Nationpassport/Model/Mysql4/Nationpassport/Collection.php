<?php

class Magestore_Nationpassport_Model_Mysql4_Nationpassport_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
	public function _construct(){
		parent::_construct();
		$this->_init('nationpassport/nationpassport');
	}
}