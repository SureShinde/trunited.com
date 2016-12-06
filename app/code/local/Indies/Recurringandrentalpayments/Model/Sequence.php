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

class Indies_Recurringandrentalpayments_Model_Sequence extends Mage_Core_Model_Abstract
{

    protected $_product;
    /** Status 'pending' */
    const STATUS_PENDING = 'pending';
    /** Status 'pending for payment' */
    const STATUS_PENDING_PAYMENT = 'pending_payment';
    /** Status 'paid' */
    const STATUS_PAYED = 'paid';
    /** Status 'failed' */
    const STATUS_FAILED = 'failed';
    /** Status 'archived' */
    const STATUS_ARCHIVED = 'archived';

    protected function _construct()
    {
        $this->_init('recurringandrentalpayments/sequence');
    }

    /**
     * Returns assigned order instance
     * @return Mage_Sales_Order
     */
    public function getOrder()
    {
        if (!$this->getData('order') && $this->getOrderId()) {
            $this->setOrder(
                Mage::getModel('sales/order')->load($this->getOrderId())
            );
        } elseif (!$this->getData('order')) {
            $this->setOrder(
                Mage::getModel('sales/order')
            );
        }
        return $this->getData('order');
    }

    public function _beforeSave()
    {
        if ($this->exists() && !$this->getId()) {
            $this->_dataSaveAllowed = false;
        }
        return parent::_beforeSave();
    }

    /**
     * Tests if this subscription already exists
     * @return bool
     */
    public function exists()
    {
        $collection = $this->getCollection();

        $select = $collection->getSelect();
        $select
                ->where('date=?', $this->getDate())
                ->where('subscription_id=?', $this->getSubscriptionId());
        return !!$collection->count();
    }

    /**
     * Returns subscription object
     * @return Indies_Recurringandrentalpayments_Model_Subscription
     */
    public function getSubscription()
    {
        return Mage::getModel('recurringandrentalpayments/subscription')->load($this->getSubscriptionId());
    }

    /**
     * Returns orders array by subscription
     * @param Indies_Recurringandrentalpayments_Model_Subscription $subscription
     * @return array
     */
    public function getOrdersBySubscription($subscription)
    {
        $subscriptionId = $subscription->getId();

        $collection = $this->getCollection();

        $select = $collection->getSelect()
                ->where('subscription_id=?', $subscriptionId);

        $orders = array();
        foreach ($collection as $item)
        {
            if ($item->getOrderId())
                $orders[] = Mage::getModel('sales/order')->load($item->getOrderId());
        }
        return $orders;
    }
}