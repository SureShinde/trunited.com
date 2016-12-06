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

class Indies_Recurringandrentalpayments_Block_Checkout_Cart_Item_Renderer_Downloadable extends Mage_Downloadable_Block_Checkout_Cart_Item_Renderer
{
    protected function _getRnrOptions()
    {
        $product = $this->getProduct();
		$startDateLabel = $this->__("Subscription Start Date ");
       	$buyInfo = $this->getItem()->getBuyRequest();
       	if($buyInfo!=null)
		{
			Mage::getSingleton('core/session')->setRecurringOption($buyInfo);
		}
		if($buyInfo==null)
		{
			$buyInfo=Mage::getSingleton('core/session')->getRecurringOption();
		}
		if($buyInfo!=null)
		{
			$subscription_options = array();
			$custom = $buyInfo->getIndiesRecurringandrentalpaymentsSubscriptionType();
			$terms = Mage::getModel('recurringandrentalpayments/terms')->load($custom);
			$plan = Mage::getModel('recurringandrentalpayments/plans_product')->load($product->getId(),'product_id');
			
			if ($custom && $custom > 0  && ($plan->getProductId() == $product->getId())) {
				$subscription_options[] = array(
					'label' => $this->__('Subscription Term'),
					'value' => $terms->getLabel()
				);
				if ($custom) {
					$date = now();				
					$s = Mage::getModel('recurringandrentalpayments/plans')->load($terms->getPlanId());
					
					if($s->getStartDate() == 1)
					{
						$date = new Zend_Date($buyInfo->getIndiesRecurringandrentalpaymentsSubscriptionStart(), 'MM-dd-Y');
					}
					if($s->getStartDate() == 3)
					{
						
						$startdate= date('MM-01-Y');
						//$date = new Zend_Date($startdate, 'MM-dd-Y');
						$date = new Zend_Date(date('M-01-Y', strtotime("+1 months", strtotime(date("Y-m-d")))));
					}
					$subscription_options[] = array('label' => $startDateLabel, 'value' => Mage::helper('core')->formatDate($date, Mage_Core_Model_Locale::FORMAT_TYPE_LONG));
				}
			}
		}
        return $subscription_options;
	}

    public function getOptionList()
    {
        return array_merge($this->_getRnrOptions(), parent::getOptionList());
    }
}