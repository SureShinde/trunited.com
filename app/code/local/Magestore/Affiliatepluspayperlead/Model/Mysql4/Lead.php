<?php

class Magestore_Affiliatepluspayperlead_Model_Mysql4_Lead extends Mage_Core_Model_Mysql4_Abstract
{
	public function _construct(){
		$this->_init('affiliatepluspayperlead/lead', 'lead_id');
	}
}