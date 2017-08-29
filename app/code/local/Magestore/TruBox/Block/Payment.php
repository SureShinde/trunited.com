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
 * @package     Magestore_TruBox
 * @module      TruBox
 * @author      Magestore Developer
 *
 * @copyright   Copyright (c) 2016 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 *
 */

/**
 * TruBox Core Block Template Block
 * You should write block extended from this block when you write plugin
 *
 * @category    Magestore
 * @package     Magestore_TruBox
 * @author      Magestore Developer
 */
class Magestore_TruBox_Block_Payment extends Mage_Core_Block_Template {

    CONST COUNTRY_DEFAULT_SHIPING = 'US';
    protected $_address;

    //construct function
    public function __construct() {
        parent::__construct();
    }

    //prepare layout
    public function _prepareLayout() {
        parent::_prepareLayout();
        return $this;
    }


    /**
     * check trubox system is enabled or not
     *
     * @return boolean
     */
    public function isEnable() {
        return Mage::helper('trubox')->isEnable();
    }

    public function getTruBox() {
        return Mage::helper('trubox')->getCurrentTruBoxCollection();
    }

    public function savePaymentUrl() {
        return $this->getUrl('*/*/savePayment');
    }

    public function getCurrentCustomer()
    {
        return Mage::getModel('customer/customer')->load(Mage::getSingleton('customer/session')->getCustomer()->getId());
    }

    public function getPaymentTruBox() {
        $cards = Mage::getModel('tokenbase/card')->getCollection()
            ->addFieldToFilter( 'active', 1 )
            ->addFieldToFilter( 'customer_id', $this->getCurrentCustomer()->getId())
            ->addFieldToFilter( 'method', 'authnetcim')
            ->setOrder('use_in_trubox', 'desc')
            ->setOrder('id', 'desc')
        ;

        return $cards;
    }

    public function getCardUrl()
    {
        return $this->getUrl('customer/paymentinfo/');
    }

}
