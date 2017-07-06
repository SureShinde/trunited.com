<?php

class Magestore_Custompromotions_Model_Mysql4_Phonecode_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
	public function _construct(){
		parent::_construct();
		$this->_init('custompromotions/phonecode');
	}
}