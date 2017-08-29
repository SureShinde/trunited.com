<?php

class Magestore_Affiliatepluspayperlead_Model_Mysql4_Lead_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
	public function _construct(){
		parent::_construct();
		$this->_init('affiliatepluspayperlead/lead');
	}
}