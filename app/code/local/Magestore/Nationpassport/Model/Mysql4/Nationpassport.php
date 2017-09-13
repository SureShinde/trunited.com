<?php

class Magestore_Nationpassport_Model_Mysql4_Nationpassport extends Mage_Core_Model_Mysql4_Abstract
{
	public function _construct(){
		$this->_init('nationpassport/nationpassport', 'nationpassport_id');
	}
}