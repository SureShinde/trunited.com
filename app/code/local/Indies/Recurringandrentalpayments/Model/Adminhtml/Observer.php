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

class Indies_Recurringandrentalpayments_Model_Adminhtml_Observer 
{
	public function CreateProcessDataBefore($observer)
	{
		$postData = Mage::app()->getRequest()->getPost();
		$quote = Mage::getSingleton('adminhtml/session_quote')->getQuote();
		if (!isset($postData['item'])) {
			return;
		}
		if (isset($postData['update_items']) && $postData['update_items']) 
		{
			$items = $postData['item'];
			foreach ($quote->getAllItems() as $id => $item) 
			{
					if (isset($items[$item->getId()]['indies_recurringandrentalpayments_subscription_type'])) {
					$options = $item->getOptions();
					foreach ($options as $option) {

						if($option->getCode() == 'info_buyRequest')
						{
							$unserialized = unserialize($option->getValue());
							$unserialized['indies_recurringandrentalpayments_subscription_type'] = $items[$item->getId()]['indies_recurringandrentalpayments_subscription_type'];
							$unserialized['indies_recurringandrentalpayments_subscription_start'] = $items[$item->getId()]['indies_recurringandrentalpayments_subscription_start'];
							$option->setValue(serialize($unserialized));
						}
					}
					$item->setOptions($options)->save();
				}
			}
		}
	}
	
	/* Set term price to bundle items at the time of placing an order */
	
	public function assignTermpriceToBundleItems($observer)
	{
		/* Update value in 'sales_flat_quote_item_option'  */
		$cart = Mage::getSingleton('adminhtml/session_quote')->getQuote();
		$ids = array();
		foreach ($cart->getAllItems() as $i)
		{
			if ($i->getParentItem())
			{
				$parent_type = Mage::getModel('sales/quote_item')->load($i->getParentItemId())->getData();
				if($parent_type['product_type'] == 'bundle') 
				{
					$op = Mage::getModel('sales/quote_item_option')->load($i->getParentItemId(),'item_id')->getValue();
					$sub_term = unserialize($op);
					if ($sub_term['indies_recurringandrentalpayments_subscription_type'] >  0 )
					{
						$ids[] = $i->getId();
						$options = $i->getOptions();
						$term = $sub_term['indies_recurringandrentalpayments_subscription_type'];
						foreach ($options as $option)
						{
							if($option->getCode() == 'bundle_selection_attributes')
							{
								$unserialized = unserialize($option->getValue());
								$price = Mage::helper('recurringandrentalpayments')->getBundleItemsPrice($term,$unserialized['price']);
								$unserialized['price'] = number_format($price, 2, '.', ',');
								$option->setValue(serialize($unserialized));
							}
						}
					}
					try {	
						$i->setOptions($options)->save(); 
					} catch (Exception $e) 	{ Mage::log('Ex  '.$e->getMessage());	}
				}
			  }
			 
		}

		/*   Update value in 'sales_flat_order_item'  */
		$order = $observer->getEvent()->getOrder();
		$items = $order->getAllItems();
		foreach ($items as $item) 
		{
			if (in_array($item->getQuoteItemId(),$ids))
			{
				$op = Mage::getModel('sales/order_item')->load($item->getParentItemId())->getProductOptions();
				$term = $op['info_buyRequest']['indies_recurringandrentalpayments_subscription_type'];
				
				$options = $item->getProductOptions();
				if (isset($options['bundle_selection_attributes']))
				{
					$unserialized = unserialize($options['bundle_selection_attributes']);
					$price = Mage::helper('recurringandrentalpayments')->getBundleItemsPrice($term,$unserialized['price']);
					$unserialized['price'] = $price;
					$options['bundle_selection_attributes'] = serialize($unserialized);
				}
				$item->setProductOptions($options)->save();
			
			}
		}
	}	
}