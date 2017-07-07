<?php
/**
*
* Do not edit or add to this file if you wish to upgrade the module to newer
* versions in the future. If you wish to customize the module for your
* needs please contact us to https://www.milople.com/magento-extensions/contacts/
*
* @category     Ecommerce
* @package      Indies_Recurringandrentalpayments
* @copyright    Copyright (c) 2015 Milople Technologies Pvt. Ltd. All Rights Reserved.
* @url          https://www.milople.com/magento-extensions/recurring-and-subscription-payments.html
*
* Milople was known as Indies Services earlier.
*
**/

class Indies_Recurringandrentalpayments_Block_Customer_Subscription_Edit_Shipping extends Mage_Directory_Block_Data
{

    protected $_order;


    public function _construct()
    {
        $this->setTemplate('customer/address/edit.phtml');
        return $this;
    }

    public function getTitle()
    {
        return $this->__("Subscription Details - Shipping Address");
    }


    public function getCountryId()
    {
        $countryId = $this->getAddress()->getCountryId();
        return $countryId;
    }

    /**
     * Returns billing address for order
     * @return object
     */
    public function getAddress()
    {
        return $this->getSubscription()->getOrder()->getShippingAddress();
    }

    public function isDefaultBilling()
    {
        return $this->getAddress()->getId() && $this->getAddress()->getId() == Mage::getSingleton('customer/session')->getCustomer()->getDefaultBilling();
    }

    public function isDefaultShipping()
    {
        return $this->getAddress()->getId() && $this->getAddress()->getId() == Mage::getSingleton('customer/session')->getCustomer()->getDefaultShipping();
    }

    public function canSetAsDefaultBilling()
    {
        return false;
    }

    public function canSetAsDefaultShipping()
    {
        return false;
    }

    public function getSaveUrl()
    {
        return Mage::getUrl('recurringandrentalpayments/customer/save', array('section' => 'shipping', 'id' => $this->getRequest()->getParam('id')));
    }

    /**
     * Returns back url
     * @return string
     */
    public function getBackUrl()
    {
        return Mage::getUrl('*/*/view', array('id' => $this->getRequest()->getParam('id')));
    }
}