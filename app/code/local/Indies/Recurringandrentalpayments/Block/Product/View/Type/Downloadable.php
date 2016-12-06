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

class Indies_Recurringandrentalpayments_Block_Product_View_Type_Downloadable extends Mage_Downloadable_Block_Catalog_Product_View_Type
{
    protected $_subscriptionInstance;

    public function __construct()
    {
        $this->_subscriptionInstance = new Indies_Recurringandrentalpayments_Block_Product_View_Type_Subscription($this);
        return parent::__construct();
    }

    public function getSubscription()
    {
        return $this->_subscriptionInstance;
    }

    /**
     * Return current template
     * @return string
     */
    public function getTemplate()
    {
        if (parent::getTemplate()) return parent::getTemplate();
        return $this->getSubscription()->getTemplate();
    }
	public function getDisplayCalendar($id)
	{
		$isavailable = Mage::getStoreConfig(Indies_Recurringandrentalpayments_Helper_Config::XML_PATH_GENERAL_ANONYMOUS_SUBSCRIPTIONS);	
		$plans_product = Mage::getModel('recurringandrentalpayments/plans_product')->load($id,'product_id');
		$plan = Mage::getModel('recurringandrentalpayments/plans')->load($plans_product->getPlanId(),'plan_id');

		
		$customer_group = explode(',',Mage::getStoreConfig(Indies_Recurringandrentalpayments_Helper_Config::XML_PATH_CUSTOMER_GROUP));
		$groupId = Mage::getSingleton('customer/session')->getCustomerGroupId();
		if(($isavailable == 1 ) || ($isavailable == 2 ) || (($isavailable == 3) && in_array($groupId,$customer_group)))
		{
			if(($plan->getPlanStatus() == 1))
			{
				return $plan->getStartDate();
			}
		}
		return 0;
	}
}

?>