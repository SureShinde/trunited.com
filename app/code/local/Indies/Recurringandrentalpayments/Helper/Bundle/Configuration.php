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
class Indies_Recurringandrentalpayments_Helper_Bundle_Configuration extends Mage_Bundle_Helper_Catalog_Product_Configuration
{
	public function getSelectionFinalPrice(Mage_Catalog_Model_Product_Configuration_Item_Interface $item,
        $selectionProduct)
    {
        $selectionProduct->unsetData('final_price');
		$buyinfo = $item->getBuyRequest();
		$optionprice = $item->getProduct()->getPriceModel()->getSelectionFinalTotalPrice(
            $item->getProduct(),
            $selectionProduct,
            $item->getQty() * 1,
            $this->getSelectionQty($item->getProduct(), $selectionProduct->getSelectionId()),
            false,
            true
        );
		
		if(Mage::helper('recurringandrentalpayments')->canRun())
		{
		   if(Mage::helper('recurringandrentalpayments')->isEnabled())
		   {
				$helper = Mage::helper('recurringandrentalpayments');
				$plans_product = Mage::getModel('recurringandrentalpayments/plans_product')->load($buyinfo->getProduct(),'product_id');
				$additionaprice = Mage::getModel('recurringandrentalpayments/plans')->load($plans_product->getPlanId(),'plan_id');
				if($additionaprice->getData())
				{
					if($buyinfo->getIndiesRecurringandrentalpaymentsSubscriptionType() && $buyinfo->getIndiesRecurringandrentalpaymentsSubscriptionType() >=0) 
					{
						$term = $buyinfo->getIndiesRecurringandrentalpaymentsSubscriptionType();
						$price = $helper->getBundleItemsPrice($term,$optionprice);
						return $price;
					}
				}
		   }
		}
		return $optionprice;
    }
}