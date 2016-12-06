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

class Indies_Recurringandrentalpayments_Model_Adminhtml_Create extends Mage_Adminhtml_Model_Sales_Order_Create
{
 /**
     * Update quantity of order quote items
     *
     * @param   array $data
     * @return  Mage_Adminhtml_Model_Sales_Order_Create
     */
    public function updateQuoteItems($data)
    {
        if (is_array($data)) 
		{
            try 
			{
                foreach ($data as $itemId => $info)
				{
                    if (!empty($info['configured'])) {
                        $item = $this->getQuote()->updateItem($itemId, new Varien_Object($info));
                        $itemQty = (float)$item->getQty();
                    } else {
                        $item       = $this->getQuote()->getItemById($itemId);
                        $itemQty    = (float)$info['qty'];
                    }

                    if ($item) 
					{
                        if ($item->getProduct()->getStockItem()) {
                            if (!$item->getProduct()->getStockItem()->getIsQtyDecimal()) {
                                $itemQty = (int)$itemQty;
                            } else {
                                $item->setIsQtyDecimal(1);
                            }
                        }
						$itemQty    = $itemQty > 0 ? $itemQty : 1;
                        if (isset($info['custom_price'])) {
                            $itemPrice  = $this->_parseCustomPrice($info['custom_price']);
                        } else {
                            $itemPrice = null;
                        }
						
					   /* Start : 2015-02-18 : Make change to add selected term price  */
						$infoBuyRequest = $item->getOptionByCode('info_buyRequest');
						$buyRequest = new Varien_Object(unserialize($infoBuyRequest->getValue()));
						$customoptionPrice = 0 ;
						$price = 0 ;
					    /* End : 2015-02-18 : Make change to add selected term price  */
                        $noDiscount = !isset($info['use_discount']);
                        if (empty($info['action']) || !empty($info['configured']))
						{
                            $item->setQty($itemQty);
  						    if(isset($info['indies_recurringandrentalpayments_subscription_type']) && $info['indies_recurringandrentalpayments_subscription_type'] > 0)
							{
								if($item->getProduct()->getTypeId() == 'bundle')
								{
									$info['product'] = $item->getProduct()->getId();
									$price = Mage::helper('recurringandrentalpayments')->calculateParentBundlePrice($info,$item);
									$this->calculateUpdateBundleItemPrice($info,$item);
								}
								else
								{
							 		$terms = Mage::getModel('recurringandrentalpayments/terms')->load($info['indies_recurringandrentalpayments_subscription_type']);
									$term_price = $terms->getPrice();
									$firstPeriodPrice = Mage::getModel('catalog/product')->load($item->getProduct()->getId())->getFirstPeriodPrice();
									if($firstPeriodPrice > 0)
									{
										$price = $firstPeriodPrice;
									}
									elseif($types->getPriceCalculationType() == 1)
									{
										$price = $item->getProduct()->getPrice() * $types->getPrice()/100;
									}
									else
									{
										$price = $types->getPrice();
									}
								}
								$custom_option_price = Mage::helper('recurringandrentalpayments')->calCustomOptionPrice($buyRequest,$item);
								$price = $price + $custom_option_price;
							}
							else
							{   
								//Mage::log('else');
							}
							if ($price == 0)
							{
								$price = $itemPrice;
							}
							
						   $item->setCustomPrice($itemPrice);
  					       $item->setOriginalCustomPrice($price);
                            $item->setNoDiscount($noDiscount);
                            $item->getProduct()->setIsSuperMode(true);
                            $item->getProduct()->unsSkipCheckRequiredOption();
                            $item->checkData();
                        }
					    else 
						{
                            $this->moveQuoteItem($item->getId(), $info['action'], $itemQty);
                        }
                    }
                }
            } catch (Mage_Core_Exception $e) {
                $this->recollectCart();
                throw $e;
            } catch (Exception $e) {
                Mage::logException($e);
            }
            $this->recollectCart();
        }
        return $this;
    }
	public function calculateUpdateBundleItemPrice($info,$orderItem)
	{
		$product = Mage::getModel('catalog/product')->load($info['product']);
		$first_term_price = 0 ;
		if ($product->getFirstPeriodPrice() > 0)
		{
			$first_term_price = $product->getFirstPeriodPrice();
		}
			
		$sel_bundle_options = $orderItem->getProduct()->getTypeInstance(true)->getOrderOptions($orderItem->getProduct());
		$options = $sel_bundle_options['bundle_options'];
		
		$cart = Mage::getSingleton('adminhtml/session_quote')->getQuote();
		$termid = $info['indies_recurringandrentalpayments_subscription_type'];
		foreach ($cart->getAllItems() as $i)
		{
			if ($i->getParentItemId() == $orderItem->getId())
			{
				$options = $i->getOptions();
				foreach ($options as $option)
				{
					if($option->getCode() == 'bundle_selection_attributes')
					{
						$unserialized = unserialize($option->getValue());
						if($first_term_price  > 0)
								$price = $first_term_price ;
							else
								$price = Mage::helper('recurringandrentalpayments')->getBundleItemsPrice($termid,$price);
						$i->setCustomPrice($price);
  						$i->setOriginalCustomPrice($price);
					}
				}
				/* add for Percentage BUNDLE ITEMS */
			}
		}
	}
}
?>