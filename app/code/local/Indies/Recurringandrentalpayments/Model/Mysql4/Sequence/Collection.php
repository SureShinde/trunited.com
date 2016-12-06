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

class Indies_Recurringandrentalpayments_Model_Mysql4_Sequence_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{


    public function _construct()
    {
        parent::_construct();
        $this->_init('recurringandrentalpayments/sequence');
    }

    /**
     * Adds filter by date
     * @param mixed $date [optional]
     * @return Indies_Recurringandrentalpayments_Model_Mysql4_Sequence_Collection
     */
    public function addDateFilter($date = null)
    {	
		if ($date) {
            $date = Mage::app()->getLocale()->date($date);
        } else {
            $date = Mage::app()->getLocale()->date();
        }
        $this->getSelect()
                ->where('date=?', $date->toString('Y-MM-dd'));
        return $this;
    }

    /**
     * Adds filter by date
     * @param mixed $date [optional]
     * @return Indies_Recurringandrentalpayments_Model_Mysql4_Sequence_Collection
     */
    public function addLessDateFilter($date = null)
    {
		if ($date) {
            $date = Mage::app()->getLocale()->date($date);
        } else {
            $date = Mage::app()->getLocale()->date();
        }
        $this->getSelect()
                ->where('date<=?', $date->toString('Y-MM-dd'));
        return $this;
    }

    /**
     * Adds filter by subscription
     * @return Indies_Recurringandrentalpayments_Model_Mysql4_Sequence_Collection
     */
    public function addSubscriptionFilter(Indies_Recurringandrentalpayments_Model_Subscription $subscription)
    {
        $this->getSelect()->where('subscription_id=?', $subscription->getId());
        return $this;
    }


    /**
     * Prepares collection for payment selection
     * @return Indies_Recurringandrentalpayments_Model_Mysql4_Sequence_Collection
     */
    public function prepareForPayment()
    {
		return $this->addStatusFilter(Indies_Recurringandrentalpayments_Model_Sequence::STATUS_PENDING);
    }

    /**
     * Adds filter by status
     * @param int $status
     * @return Indies_Recurringandrentalpayments_Model_Mysql4_Sequence_Collection
     */
    public function addStatusFilter($status)
    {
		$this->getSelect()->where('status=?', $status);
        return $this;
    }
}