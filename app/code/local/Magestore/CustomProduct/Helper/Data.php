<?php

class Magestore_CustomProduct_Helper_Data extends Mage_Core_Helper_Abstract
{
	public function getLoginUrl()
    {
        $default_url = Mage::helper('custompromotions/configuration')->getCmsMessageLogin();

        if($default_url == null)
            return Mage::getUrl('customer/account/login');
        else {
            $_url = Mage::getUrl('custompromotions/customer/redirect');
            return str_replace('{{login_url}}',$_url,$default_url);
        }
    }

    public function isLoggedIn()
    {
        return Mage::getSingleton('customer/session')->isLoggedIn();
    }

    public function getShopNowUrl($product)
    {
        if($this->isLoggedIn())
            return str_replace('{{customer_id}}',Mage::getSingleton('customer/session')->getCustomer()->getId(),$product->getShopNow());
        else return '#';
    }
}