<?php

class Magestore_Custompromotions_Model_Phonecode extends Mage_Core_Model_Abstract
{
	public function _construct(){
		parent::_construct();
		$this->_init('custompromotions/phonecode');
	}
}