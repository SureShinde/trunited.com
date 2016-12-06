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

class Indies_Recurringandrentalpayments_Block_Customer_Subscription_Payments_Pending extends Mage_Core_Block_Template
{

    /**
     * Returns payments collection
     * @return Indies_Recurringandrentalpayments_Model_Mysql4_Sequence_Collection
     */
    public function getCollection()
    {
        if (!$this->getData('collection')) {

            $this->setCollection(Mage::getModel('recurringandrentalpayments/sequence')
                                         ->getCollection()
                                         ->addSubscriptionFilter($this->getSubscription())
                                         ->addStatusFilter(Indies_Recurringandrentalpayments_Model_Sequence::STATUS_PENDING)
                                         ->setOrder('date', 'asc')
            );

        }
        return $this->getData('collection');
    }

    /**
     * Returns next payment date
     * @return Zend_Date
     */
    public function getNextPaymentDate()
    {
        if ($lastPaidDate = $this->getSubscription()->getLastPaidDate()) {
			
            return $this->getSubscription()->getNextSubscriptionEventDate($lastPaidDate);
        }

        if ($this->getCollection()->getFirstItem()) {
            return new Zend_Date($this->getCollection()->getFirstItem()->getDate(), Indies_Recurringandrentalpayments_Model_Subscription::DB_DATE_FORMAT, Mage::app()->getLocale()->getLocaleCode());
        }
    }

    public function setActive($path)
    {
        $this->_activeLink = $this->_completePath($path);
        return $this;
    }

    protected function _completePath($path)
    {
        $path = rtrim($path, '/');
        switch (sizeof(explode('/', $path))) {
            case 1:
                $path .= '/index';
            // no break

            case 2:
                $path .= '/index';
        }
        return $path;
    }
}