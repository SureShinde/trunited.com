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

class Indies_Recurringandrentalpayments_Model_Mysql4_Subscription_Item_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{

    protected $_order_items;

    public function _construct()
    {
        parent::_construct();
        $this->_init('recurringandrentalpayments/subscription_item');
    }

    /**
     * Applies filter for subscription
     * @param Indies_Recurringandrentalpayments_Model_Subscription $Subscription
     * @return Indies_Recurringandrentalpayments_Model_Mysql4_Subscription_Item_Collection
     */
    public function addSubscriptionFilter(Indies_Recurringandrentalpayments_Model_Subscription $Subscription)
    {
	    if ($Subscription->getId()) {
            $this->getSelect()->where('subscription_id=?', $Subscription->getId());
        } else {
            throw new Mage_Core_Exception("Can't apply subscription filter for non-existing subscription");
        }
        return $this;
    } 

    /**
     * Converts to according order items collections
     * @return Sales_Model_Mysql4_Order_Item_Collection
     */
    public function getOrderItems()
    {
        if (!$this->_order_items) {
            $ids = array();
            foreach ($this as $Item) {
                $ids[] = $Item->getPrimaryOrderItemId();
            }
            $this->_order_items = Mage::getModel('sales/order_item')->getCollection();

            $this->_order_items->getSelect()->where('item_id IN (' . implode(",", $ids) . ')');
        }
        return $this->_order_items;
    }

    /**
     * Applies filter for order
     * @param Mage_Sales_Model_Order $Subscription
     * @return Indies_Recurringandrentalpayments_Model_Mysql4_Subscription_Item_Collection
     */
    public function addOrderFilter(Mage_Sales_Model_Order $Order)
    {
        $this->getSelect()->where('primary_order_id=?', $Order->getId());
        return $this;
    }

}