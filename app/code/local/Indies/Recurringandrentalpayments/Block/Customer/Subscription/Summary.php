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

class Indies_Recurringandrentalpayments_Block_Customer_Subscription_Summary extends Mage_Core_Block_Template
{


    /**
     * Returns current customer
     * @return Mage_Customer_Model_Customer
     */
    public function getCustomer()
    {
        return Mage::getSingleton('customer/session')->getCustomer();
    }

    /**
     * Returns current order
     * @return Mage_Sales_Model_Order
     */
    public function getOrder()
    {
        if (!$this->getData('order')) {
            $this->setOrder($this->getSubscription()->getOrder());
        }
        return $this->getData('order');
    }

    /**
     * Returns current order
     * @return Mage_Sales_Model_Quote
     */
    public function getQuote()
    {
        if (!$this->getData('quote')) {
            $this->setQuote($this->getSubscription()->getQuote());
        }
        return $this->getData('quote');
    }

    public function getPaymentBlock()
    {
        if (!$this->getOrder()->getPayment()) {
            throw new Indies_Recurringandrentalpayments_Exception("Can't get order for subscription #{$this->getSubscription()->getId()}. DB Corrupt?");
        }
        return $this->helper('payment')->getInfoBlock($this->getOrder()->getPayment());
    }

    public function getSubscriptionStatusLabel()
    {
        return Mage::getModel('recurringandrentalpayments/source_subscription_status')->getLabel($this->getSubscription()->getStatus());
    }
	public function getTerm()
	{
		return Mage::getModel('recurringandrentalpayments/terms')->load($this->getSubscription()->getTermType());

	}
	public function getPlan()
	{
		$plan=Mage::getModel('recurringandrentalpayments/terms')->load($this->getSubscription()->getTermType())->getPlanId();
		return Mage::getModel('recurringandrentalpayments/plans')->load($plan);
	}
	public function getOrderData()
	{
		$id=$this->getSubscription()->getSubscriptionId();
		$orderid=Mage::getModel('recurringandrentalpayments/subscription_item')->load($id,'subscription_id');
		$order_collection = Mage::getModel('sales/order')->load($orderid->getPrimaryOrderId());
		return $order_collection;
	}
	public function getItemsCollection()
    {
        return $this->getOrderData()->getItemsCollection();
    }
 	public function getCustomerGroupName()
	{
	   if ($this->getOrder()) {
            return Mage::getModel('customer/group')->load((int)$this->getOrder()->getCustomerGroupId())->getCode();
        }
        return null;
	}

}