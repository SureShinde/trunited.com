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

abstract class Indies_Recurringandrentalpayments_Model_Payment_Method_Abstract extends Varien_Object implements Indies_Recurringandrentalpayments_Model_Payment_Method_Interface
{

    /**
     * Returns order id for transfered order
     * @param mixed $Order
     * @return
     */
    public function getOrderId($Order)
    {
        if (is_int($Order)) {
            return $Order;
        } else {
            return $Order->getId();
        }
    }

    /**
     * Returns order object for transfered order
     * @param mixed $Order
     * @return Mage_Sales_Model_Order
     */
    public function getOrder($Order)
    {
        if (is_int($Order)) {
            return Mage::getModel('sales/order')->load($Order);
        } else {
            return $Order;
        }
    }

    /**
     * This function is run when subscription is created and new order creates
     * @param Indies_Recurringandrentalpayments_Model_Subscription $Subscription
     * @param Mage_Sales_Model_Order     $Order
     * @param Mage_Sales_Model_Quote     $Quote
     * @return Indies_Recurringandrentalpayments_Model_Payment_Method_Abstract
     */
    public function onSubscriptionCreate(Indies_Recurringandrentalpayments_Model_Subscription $Subscription, Mage_Sales_Model_Order $Order, Mage_Sales_Model_Quote $Quote)
    {
        return $this;
    }

    /**
     * This function is run when subscription is created and new order creates
     * @param Indies_Recurringandrentalpayments_Model_Subscription $Subscription
     * @param Mage_Sales_Model_Order     $Order
     * @param Mage_Sales_Model_Quote     $Quote
     * @return Indies_Recurringandrentalpayments_Model_Payment_Method_Abstract
     */
    public function onSubscriptionCancel(Indies_Recurringandrentalpayments_Model_Subscription $Subscription)
    {
        return $this;
    }


    /**
     * Cancels subscription at paypal
     * @param Indies_Recurringandrentalpayments_Model_Subscription $Subscription
     * @return Indies_Recurringandrentalpayments_Model_Payment_Method_Abstract
     */
    public function onSubscriptionSuspend(Indies_Recurringandrentalpayments_Model_Subscription $Subscription)
    {
        return $this;
    }

    /**
     * Cancels subscription at paypal
     * @param Indies_Recurringandrentalpayments_Model_Subscription $Subscription
     * @return Indies_Recurringandrentalpayments_Model_Payment_Method_Abstract
     */
    public function onSubscriptionReactivate(Indies_Recurringandrentalpayments_Model_Subscription $Subscription)
    {
		return $this;
    }


    /**
     * Checks if payment method can perform transaction now
     * @return bool
     */
    public function isValidForTransaction(Indies_Recurringandrentalpayments_Model_Sequence $Sequence)
    {
        return true;
    }

}