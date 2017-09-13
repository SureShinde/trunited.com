<?php

class Magestore_Interest_Model_Mysql4_Interest_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
	public function _construct(){
		parent::_construct();
		$this->_init('interest/interest');
	}
}