<?php

class Magestore_Other_Model_Mysql4_Other_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
	public function _construct(){
		parent::_construct();
		$this->_init('other/other');
	}
}