<?php

class Magestore_ManageApi_Model_Mysql4_Linkshareadvertisers extends Mage_Core_Model_Mysql4_Abstract
{
	public function _construct(){
		$this->_init('manageapi/linkshareadvertisers', 'linkshare_advertisers_id');
	}
}