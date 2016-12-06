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

class Indies_Recurringandrentalpayments_Model_Mysql4_Subscription_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{

    public function _construct()
    {
        parent::_construct();
        $this->_init('recurringandrentalpayments/subscription');
    }


    /**
     * Adds filter by customer
     * @param mixed $customer
     * @return Indies_Recurringandrentalpayments_Model_Mysql4_Subscription_Collection
     */
    public function addCustomerFilter($customer)
    {
        if (!is_int($customer)) {
            $id = $customer->getId();
        } else {
            $id = $customer;
        }
        $this->getSelect()->where('customer_id=?', $id);
        return $this;
    }

    /**
     * Adds filter by product/product SKU
     * @param mixed $product
     * @return Indies_Recurringandrentalpayments_Model_Mysql4_Subscription_Collection
     */
    public function addProductFilter($product)
    {
        if ($product instanceof Mage_Catalog_Model_Product) {
            $id = $product->getSku();
        } else {
            $id = $product;
        }
        $this->getSelect()->where('product_sku=?', $id);

        return $this;
    }

    /**
     * Adds only active subscriptions filter to collection
     * @return Indies_Recurringandrentalpayments_Model_Mysql4_Subscription_Collection
     */
    public function addActiveFilter()
    {
        $this->getSelect()->where('status=?', Indies_Recurringandrentalpayments_Model_Subscription::STATUS_ENABLED)->limit(1);
        return $this;
    }

    /**
     * Adds filter for all subscriptions that matching $date
     * @param object $date [optional]
     * @return Indies_Recurringandrentalpayments_Model_Mysql4_Subscription_Collection
     */
    public function addDateFilter($date = null)
    {
		$sequence = Mage::getModel('recurringandrentalpayments/sequence')->getCollection()->prepareForPayment()->addDateFilter($date);
        $in = array();
	        foreach ($sequence as $record) {
            $in[] = $record->getSubscriptionId();
        }
        if (!sizeof($in)) {
            // No subscriptions are present
            $in[] = -1;
        }
        $this->getSelect()->where('id IN (' . implode(',', $in) . ')');
        return $this;
    }

    /**
     * Adds filter for all subscriptions that matching today
     * @return Indies_Recurringandrentalpayments_Model_Mysql4_Subscription_Collection
     */
    public function addLessTodayFilter($date = null)
    {
	    $sequence = Mage::getModel('recurringandrentalpayments/sequence')->getCollection()->prepareForPayment()->addLessDateFilter($date);
        $in = array();
		
        foreach ($sequence as $record) {
			
            $in[] = $record->getSubscriptionId();
        }
        if (!sizeof($in)) {
            // No subscriptions are present
            $in[] = -1;
        }
        $this->getSelect()->where('id IN (' . implode(',', $in) . ')');
        return $this;
    }

    /**
     * Adds filter for all subscriptions that matching today
     * @return Indies_Recurringandrentalpayments_Model_Mysql4_Subscription_Collection
     */
    public function addTodayFilter()
    {
        return $this->addDateFilter();
    }


    /**
     * Attaches flat data to collection
     * @return Indies_Recurringandrentalpayments_Model_Mysql4_Subscription_Collection
     */
    protected function _initselect()
    {
        parent::_initSelect();
        $this->getSelect()->joinLeft(array('f' => Mage::getResourceModel('recurringandrentalpayments/subscription_flat')->getMainTable()),
                                     'f.subscription_id=id'
        );
        return $this;
    }

}