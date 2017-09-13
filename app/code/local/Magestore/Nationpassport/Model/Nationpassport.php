<?php

class Magestore_Nationpassport_Model_Nationpassport extends Mage_Core_Model_Abstract
{
	public function _construct(){
		parent::_construct();
		$this->_init('nationpassport/nationpassport');
	}
}