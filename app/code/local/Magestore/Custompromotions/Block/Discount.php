<?php

class Magestore_Custompromotions_Block_Discount extends Mage_Core_Block_Template
{
	public function _prepareLayout(){
		return parent::_prepareLayout();
	}

	public function getDiscountUrl()
	{
		$customer_id = $this->getCurrentCustomerId();
		if($customer_id != null){
			return $customer_id;
		} else {
			return '#';
		}
	}

	public function getCurrentCustomerId()
	{
		if ($this->isLogged()) {
			return Mage::getSingleton('customer/session')->getCustomer()->getId();
		} else {
			return null;
		}
	}

	public function isLogged()
	{
		return Mage::getSingleton('customer/session')->isLoggedIn();
	}

	public function getMessageUrl()
	{
		$default_url = Mage::helper('custompromotions/configuration')->getCmsMessageLogin();
		if($default_url == null)
			return Mage::helper('custompromtions')->__('You have to login first !');
		else {
			$_url = Mage::getUrl('custompromotions/customer/redirect');
			return str_replace('{{login_url}}',$_url,$default_url);
		}
	}
}