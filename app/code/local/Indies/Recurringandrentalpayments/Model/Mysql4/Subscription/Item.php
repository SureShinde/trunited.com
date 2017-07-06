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

class Indies_Recurringandrentalpayments_Model_Mysql4_Subscription_Item extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init('recurringandrentalpayments/subscription_item', 'id');
    }

    /**
     * Returns according order item
     * @return Mage_Sales_Model_Order_Item
     */
    public function getOrderItem()
    {
        if (!$this->getData('order_item')) {
            $this->setOrderItem(Mage::getModel('sales/order_item')->load($this->getPrimaryOrderItemId()));
        }
        return $this->getData('order_item');
    }

    /**
     * Returns according order
     * @return Mage_Sales_Model_Order
     */
    public function getOrder()
    {
        if (!$this->getData('order')) {
            $this->setOrder(Mage::getModel('sales/order')->load($this->getPrimaryOrderId()));
        }
        return $this->getData('order');
    }
}

