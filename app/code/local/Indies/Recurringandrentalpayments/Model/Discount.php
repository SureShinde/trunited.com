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
class Indies_Recurringandrentalpayments_Model_Discount extends Varien_Object {	

	public function getDiscount()
	{
		$quote = $this->getQuote();
		$items = $quote->getAllVisibleItems();
		$discount = 0;
		
		$amount = Mage::helper('recurringandrentalpayments')->discountAmount() ;
		$calculation_type = Mage::helper('recurringandrentalpayments')->applyDiscountType();

		$discount_amount = 0 ;
		foreach($items as $item) 
		{
		   if($this->isApplyDiscountAmount($item,$amount))
		   {
			    $item_price = $item->getCustomPrice();
			    if(Mage::getSingleton('admin/session')->isLoggedIn())
				{
					$item_price = $item->getOriginalCustomPrice();
				}
				if(Mage::helper('recurringandrentalpayments')->discountAvailableTo() == 3 )   // Specific customer group
				{
					$isavailable = Mage::helper('recurringandrentalpayments')->selectedCustomerGroup() ;
					
					$customer_group = explode(',',Mage::helper('recurringandrentalpayments')->selectedCustomerGroup());
					$groupId = Mage::getSingleton('customer/session')->getCustomerGroupId();

					if((($isavailable == 1 ) || ($isavailable == 2 ) || ($isavailable == 3)) && in_array($groupId,$customer_group))
					{
						if(Mage::helper('recurringandrentalpayments')->applyDiscountOn()== 1 || Mage::helper('recurringandrentalpayments')->applyDiscountOn()== 2 )
						{
							if($calculation_type == 1)  //Fixed
							{
								if($item_price > $amount)
								{
									$discount_amount = $discount_amount + ($amount * $item->getQty()) ;
								}
							}
							else
							{
								$discount_amount = $discount_amount + (($item_price * $amount)/100 );
							}
						}
					}
				}
				else
				{
					if(Mage::helper('recurringandrentalpayments')->applyDiscountOn()== 1 || Mage::helper('recurringandrentalpayments')->applyDiscountOn()== 2 )
					{
							if($calculation_type == 1)  //Fixed
							{
								if($item_price > $amount)    // CustomPrice is a Term price
								{
									$discount_amount = $discount_amount + ($amount * $item->getQty()) ;
								}
							}
							else
							{
								$discount_amount = $discount_amount + (($item_price * $amount)/100 );
							}
					}	
				}				
			}
			else
			{
				continue ;
			}
		}	
		return $discount_amount;
	}

	public function getQuote()
	{
		$modules = Mage::getConfig()->getNode('modules')->children();
		$modulesArray = (array)$modules;	
		$session = Mage::getSingleton('admin/session');
		if ($session->isLoggedIn()) {
			$quote = Mage::getSingleton('adminhtml/session_quote')->getQuote();
		}else{
			$quote = Mage::getSingleton('checkout/cart')->getQuote();
		}
		return $quote;
	}

	public function isApplyDiscountAmount($product,$amount)
	{
		$calculation_type = Mage::helper('recurringandrentalpayments')->applyDiscountType();
		
		if($calculation_type != 1 ){
			$amount = $product->getProduct()->getFinalPrice() * $amount/100;
		}	
		$infoBuyRequest = $product->getOptionByCode('info_buyRequest');
		$buyRequest = new Varien_Object(unserialize($infoBuyRequest->getValue()));
		
		if($buyRequest->getIndiesRecurringandrentalpaymentsSubscriptionType() != 'null' && $buyRequest->getIndiesRecurringandrentalpaymentsSubscriptionType() > 0)
		{
			$collection = Mage::getModel('recurringandrentalpayments/terms')->load($buyRequest->getIndiesRecurringandrentalpaymentsSubscriptionType());
			$price = $product->getPrice();
			
			if($collection->getPriceCalculationType() == 1)   //Percentage
			{				
				$termprice = $product->getPrice() * $collection->getPrice()/100;				
				$price = $product->getProduct()->getFinalPrice() -  $termprice ;				
				if($product->getProduct()->getFinalPrice() >= $amount)
				 $allow_discount_amount = 1 ;
				else
				 $allow_discount_amount = 0 ;
			}
			else
			{
				$price =  $collection->getPrice() - $amount ;
				$allow_discount_amount = 0 ;
				if ($price < $collection->getPrice())
				{
					$allow_discount_amount = 1 ;	
				}
			}
		}
		else
		{
			$allow_discount_amount = 0 ;
		}
		return $allow_discount_amount;
	}
}
 ?>