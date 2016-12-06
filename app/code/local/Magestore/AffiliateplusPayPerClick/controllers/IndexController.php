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
 * @package     Magestore_AffiliateplusPayPerClick
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * AffiliateplusPayPerClick Index Controller
 * 
 * @category    Magestore
 * @package     Magestore_AffiliateplusPayPerClick
 * @author      Magestore Developer
 */
class Magestore_AffiliateplusPayPerClick_IndexController extends Mage_Core_Controller_Front_Action {

    /**
     * index action
     */
    protected function _getAccountHelper() {
        return Mage::helper('affiliateplus/account');
    }

    public function indexAction() {
        if (!Mage::helper('magenotification')->checkLicenseKeyFrontController($this)) {
            return;
        }
        
        //Changed By Adam 30/07/2014
        $storeId = Mage::app()->getStore()->getId();
        if(!Mage::helper('affiliatepluspayperclick')->isPluginEnabled()) return;
        
        if ($this->_getAccountHelper()->accountNotLogin())
            return $this->_redirect('affiliateplus/account/login');
        $this->loadLayout();
        $this->renderLayout();
    }

    public function listClickTransactionAction() {
        
        if (!Mage::helper('magenotification')->checkLicenseKeyFrontController($this)) {
            return;
        }
        //Changed By Adam 30/07/2014
        $storeId = Mage::app()->getStore()->getId();
        if(!Mage::helper('affiliatepluspayperclick')->isPluginEnabled()) return;
        
        if ($this->_getAccountHelper()->accountNotLogin())
            return $this->_redirect('affiliateplus/account/login');
        $this->loadLayout();
        $this->getLayout()->getBlock('head')->setTitle($this->__('PPC Commissions'));
        $this->renderLayout();
    }

}