<?php

class Magestore_Affiliatepluspayperlead_Model_Lead extends Mage_Core_Model_Abstract
{
	public function _construct(){
		parent::_construct();
		$this->_init('affiliateplus/transaction');
	}
	
	public function loadByAccountAndCustomer($customer_email,$action){
		$model = Mage::getModel('affiliatepluspayperlead/lead');
		$collection = $this->getCollection()
							//->addFieldToFilter('account_id',$account_id)
							->addFieldToFilter('customer_email',$customer_email)
							->addFieldToFilter('type',$action)
							;
		if(count($collection)){
			$model = $model -> load($collection->getFirstItem()->getId());
		}
		return $model;
	}
}