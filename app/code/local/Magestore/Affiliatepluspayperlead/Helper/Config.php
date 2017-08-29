<?php

class Magestore_Affiliatepluspayperlead_Helper_Config extends Mage_Core_Helper_Abstract
{
	public function getGeneralConfig($code, $store = null){
		return Mage::getStoreConfig('affiliateplus/general/'.$code,$store);
	}
	
	public function getPaymentConfig($code, $store = null){
		return Mage::getStoreConfig('affiliateplus/payment/'.$code,$store);
	}
	
	public function getEmailConfig($code, $store = null){
		return Mage::getStoreConfig('affiliateplus/email/'.$code,$store);
	}
	
	public function getSharingConfig($code, $store = null){
		return Mage::getStoreConfig('affiliateplus/account/'.$code,$store);
	}
	
	public function getMaterialConfig($code, $store = null){
		return Mage::getStoreConfig('affiliateplus/general/material_'.$code,$store);
	}
	
	public function disableMaterials(){
		return (Mage::helper('affiliateplus/account')->accountNotLogin() || !$this->getMaterialConfig('enable'));
	}
	
	public function getReferConfig($code, $store = null){
		return Mage::getStoreConfig('affiliateplus/refer/'.$code,$store);
	}
	
	public function getPayperleadConfig($code, $store = null){
        if (!Mage::getStoreConfig('affiliateplus/payperlead/enable', $store)) {
            return '';
        }
		return Mage::getStoreConfig('affiliateplus/payperlead/'.$code,$store);
	}
    
    public function disableMenu() {
        // Changed By Adam 28/07/2014
        if(!Mage::helper('affiliateplus')->isAffiliateModuleEnabled()) return false;
        if (!$this->getPayperleadConfig('enable')) {
            return true;
        }
        return Mage::helper('affiliateplus/account')->accountNotLogin();
    }
}
