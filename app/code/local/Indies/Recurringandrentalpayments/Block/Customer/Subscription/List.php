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

class Indies_Recurringandrentalpayments_Block_Customer_Subscription_List extends Mage_Core_Block_Template
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
     * Returns collection of subscriptions that are assigned to current customer
     * @return Indies_Recurringandrentalpayments_Model_Mysql4_Subscrtiption_Collection
     */
    public function getCollection()
    {
        if (!$this->getData('collection')) {
            $this->setCollection(
                Mage::getModel('recurringandrentalpayments/subscription')->getCollection()->addCustomerFilter($this->getCustomer())->setOrder('subscription_id', 'DESC')
            );
        }
        return $this->getData('collection');
    }

    /**
     * Returns toolbar with pages and so on
     * @return Mage_Page_Block_Html_Pager
     */
    public function getToolbarHtml()
    {
        return $this->getChildHtml('toolbar');
    }

    /**
     * Prepares block layout
     * @return
     */
    protected function _prepareLayout()
    {
        $toolbar = $this->getLayout()->createBlock('page/html_pager', 'customer_recurringandrentalpayments.toolbar')
                ->setCollection($this->getCollection());
        $this->setChild('toolbar', $toolbar);
        return parent::_prepareLayout();
    }

    public function getSubscriptionStatusLabel(Indies_Recurringandrentalpayments_Model_Subscription $subscription)
    {
        return Mage::getModel('recurringandrentalpayments/source_subscription_status')->getLabel($subscription->getStatus());
    }

    public function getPendingPaymentBlock()
    {
        return $this->getLayout()->createBlock('recurringandrentalpayments/customer_subscription_payments_pending', 'customer_recurringandrentalpayments.payments_pending' . rand(0, 999999999));
    }

    /**
     * Get back url in account dashboard
     *
     * This method is copypasted in:
     * Mage_Wishlist_Block_Customer_Wishlist  - because of strange inheritance
     * Mage_Customer_Block_Address_Book - because of secure url
     *
     * @return string
     */
    public function getBackUrl()
    {
        // the RefererUrl must be set in appropriate controller
        if ($this->getRefererUrl()) {
            return $this->getRefererUrl();
        }
        return $this->getUrl('customer/account/');
    }
	public function gettermlabel($id)
	{
			$terms = Mage::getModel('recurringandrentalpayments/terms')->load($id);
			return $terms->getLabel();
	}
}