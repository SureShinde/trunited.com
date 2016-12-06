<?php
/**
 * Magestore
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the Magestore.com license that is
 * available through the world-wide-web at this URL:
 * http://www.magestore.com/license-agreement.html
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category    Magestore
 * @package     Magestore_AffiliateplusCostPerMille
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * AffiliateplusCostPerMille Index Controller
 * 
 * @category    Magestore
 * @package     Magestore_AffiliateplusCostPerMille
 * @author      Magestore Developer
 */
class Magestore_AffiliateplusCostPerMille_IndexController extends Mage_Core_Controller_Front_Action
{
    /**
     * index action
     */
    public function indexAction()
    {
		if (!Mage::helper('magenotification')->checkLicenseKeyFrontController($this)) {
            return;
        }
        $this->loadLayout();
        $this->renderLayout();
    }
    
    protected function _getAccountHelper(){
		return Mage::helper('affiliateplus/account');
	}
    
    public function listCpmTransactionAction()
    {
		if (!Mage::helper('magenotification')->checkLicenseKeyFrontController($this)) {
            return;
        }
        if(!Mage::helper('affiliatepluscostpermille')->isDisplayed())
            return  $this->_redirect('affiliateplus/index/index');
        if ($this->_getAccountHelper()->accountNotLogin())
    		return $this->_redirect('affiliateplus/account/login');
    	$this->loadLayout();
    	$this->getLayout()->getBlock('head')->setTitle($this->__('Cpm Commissions'));
    	$this->renderLayout();
    }
}