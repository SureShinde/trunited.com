<?php

class Magestore_Interest_Model_Interest extends Mage_Core_Model_Abstract
{
	public function _construct(){
		parent::_construct();
		$this->_init('interest/interest');
	}
}