<?php

class Magestore_Other_Model_Mysql4_Other extends Mage_Core_Model_Mysql4_Abstract
{
	public function _construct(){
		$this->_init('other/other', 'other_id');
	}
}