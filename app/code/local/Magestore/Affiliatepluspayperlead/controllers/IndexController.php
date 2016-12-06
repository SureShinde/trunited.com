<?php

class Magestore_Affiliatepluspayperlead_IndexController extends Mage_Core_Controller_Front_Action
{
	protected function _getAccountHelper(){
		return Mage::helper('affiliateplus/account');
	}
	public function indexAction(){
		$this->loadLayout();
		$this->renderLayout();
	}
	public function listLeadTransactionAction(){
        if (!Mage::helper('affiliatepluspayperlead/config')->getPayperleadConfig('enable')) {
            return $this->_redirect('affiliateplus/index/index');
        }
		if(!Mage::helper('magenotification')->checkLicenseKeyFrontController($this)){ return; }
		if ($this->_getAccountHelper()->accountNotLogin())
    		return $this->_redirect('affiliateplus/account/login');
    	$this->loadLayout();
    	$this->getLayout()->getBlock('head')->setTitle($this->__('Commissions'));
    	$this->renderLayout();
	}
}