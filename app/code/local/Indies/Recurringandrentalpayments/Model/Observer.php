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

class Indies_Recurringandrentalpayments_Model_Observer extends Mage_Core_Model_Config_Data 
{
	const HASH_SEPARATOR = ":::";
	  
	public function AddToCartAfter(Varien_Event_Observer $observer)
	{
		$item = $observer->getQuoteItem();
		if ($item->getParentItem()) {$item = $item->getParentItem();}
		
		$postdata = Mage::app()->getRequest()->getPost();
		$cartItem = $observer->getEvent()->getQuoteItem();
		
		if(empty($postdata))
		{
			$infoBuyRequest = $item->getOptionByCode('info_buyRequest');
			$buyInfo_custom = new Varien_Object(unserialize($infoBuyRequest->getValue()));
			$postdata = $buyInfo_custom->getData();
		}
		$buyInfo = $cartItem->getBuyRequest();
		$product  = $cartItem->getProduct();

		//$price = $this->calculateParentBundlePrice($postdata,$item);
		
		$idForgroup = $buyInfo->getProduct();
		if(empty($idForgroup))  // This is for grouped product
		{
			$product_id = $product->getId();
		}
		else
		{
			$product_id = $buyInfo->getProduct() ;
		}

		$plans_product = Mage::getModel('recurringandrentalpayments/plans_product')->load($product_id,'product_id');
		$additionaprice = Mage::getModel('recurringandrentalpayments/plans')->load($plans_product->getPlanId(),'plan_id');
		
		if($additionaprice->getData())
		{
			if($postdata['indies_recurringandrentalpayments_subscription_type']) 
			{
				if($postdata['indies_recurringandrentalpayments_subscription_type'] >=0)
				{
					if ($postdata['bundle_option'])
					{
						$this->calculateBundleItemPrice($postdata,$item);
						$price = Mage::helper('recurringandrentalpayments')->calculateParentBundlePrice($postdata,$item);
					}
					else
					{	
						if($item->getProduct()->getFirstPeriodPrice() > 0 )
						{
							$price = $item->getProduct()->getFirstPeriodPrice() ;
						}
						else
						{
							$termid = $postdata['indies_recurringandrentalpayments_subscription_type'];
							$qty = $buyInfo->getQty();
							$productId = $buyInfo->getProduct();
							$types = Mage::getModel('recurringandrentalpayments/terms')->load($termid);
							$price = $types->getPrice();

							$custom_option_price = $item->getProduct()->getFinalPrice() - $item->getProduct()->getPrice();
							/* Put condition for a case when special price is applied to product. */
							if($custom_option_price < 0)  
							{
								$custom_option_price = 0;
							}
							if($types->getPriceCalculationType() == 1)
							{
								$price = $item->getProduct()->getPrice() * $types->getPrice()/100 + $custom_option_price;
							}
							else
							{
								$price = $types->getPrice() + $custom_option_price;
							}
						}
					}
					$price = Mage::helper('directory')->currencyConvert($price, Mage::app()->getStore()->getBaseCurrencyCode(), Mage::app()->getStore()->getCurrentCurrencyCode());
			   		
					// Set the custom price
			
					/** Add this condition because if customer add 2nd time configured product 
					with same option which he added previously that time,quatinty have to 
					increase but that is not increasing.Thats why this condiging 
					we have put */
					if($item->getProductType() == 'configurable')
					{
						$item->setCustomPrice($price);
						$item->setOriginalCustomPrice($price);
						
						$item = $observer->getQuoteItem();
						$item->setQty($postdata['qty']);
					}
					else
					{
						$item->setCustomPrice($price);
						$item->setOriginalCustomPrice($price);
				
					}
					
					// Enable super mode on the product.
					$item->getProduct()->setIsSuperMode(true);
					if($additionaprice->getStartDate() == 1)
					{
						if($postdata['indies_recurringandrentalpayments_subscription_start'])
						{
							if(strtotime($postdata['indies_recurringandrentalpayments_subscription_start']) < time())
							{
								$buyInfo->setIndiesRecurringandrentalpaymentsSubscriptionStart(date('m-d-Y'));
							}
						}
					}
				}
				else
				{
					if($item->getProductType() == 'configurable')
					{
						$item->setCustomPrice($item->getProduct()->getFinalPrice());
						$item->setOriginalCustomPrice($item->getProduct()->getFinalPrice());
						
						$item = $observer->getQuoteItem();
						$item->setQty($postdata['qty']);
						
					}
					else
					{
						// Get the quote item
						$item = $observer->getQuoteItem();
						// Ensure we have the parent item, if it has one
						//$item = ( $item->getParentItem() ? $item->getParentItem() : $item );
						$product = Mage::getModel('catalog/product')->load($item->getProduct()->getId());
						// Set the custom price
						$item->setQty($buyInfo->getQty());
						$item->setCustomPrice($item->getProduct()->getFinalPrice());
						$item->setOriginalCustomPrice($item->getProduct()->getFinalPrice());
						// Enable super mode on the product.
						$item->getProduct()->setIsSuperMode(true);
					}
				}
			}
			else
			{
				if($additionaprice->getPlanStatus() != 2) 
				{
					Mage::getSingleton('checkout/session')->addNotice('Please specify the products option(s)');
					$response = Mage::app()->getFrontController()->getResponse();
					$response->setRedirect($product->getProductUrl());
					$response->sendResponse();
					exit;
				}
				elseif(Mage::getStoreConfig(Indies_Recurringandrentalpayments_Helper_Config::XML_PATH_GENERAL_ANONYMOUS_SUBSCRIPTIONS) == 2 )
				{
					return ;
				}
				else  // If plan is Disable
				{
					return ;
				}
			}
		}
	}
	
	public function CheckoutCartUpdateItemComplete(Varien_Event_Observer $observer)
	{
		
		$postdata = Mage::app()->getRequest()->getPost();
		$cartItem = $observer->getEvent()->getItem();
		$buyInfo = $cartItem ->getBuyRequest();
		$product  = $cartItem->getProduct();
		$plans_product = Mage::getModel('recurringandrentalpayments/plans_product')->load($buyInfo->getProduct(),'product_id');
		$additionaprice = Mage::getModel('recurringandrentalpayments/plans')->load($plans_product->getPlanId(),'plan_id');

		/*fatch value of selected configured product for add final product price*/
		
		$allAttributes='';
		try
		{
			if(isset($postdata['super_attribute']))
			{
				$allAttributes=$postdata['super_attribute'];
			}
		}
		catch(Exception $e)
		{
            throw $e;
        }
		
		$productID = $buyInfo->getProduct();//$postdata['product']; //Replace with your method to get your product Id.
		$product = Mage::getModel('catalog/product')->load($productID);
	
		$original_qty=0;
		$original_qty+=$buyInfo->getQty();//$postdata['qty'];
		/* Calculate selected Configured product's Price*/
		$configure_price = 0;
		if($product->getTypeID()=='configurable')
		{		
			$productAttributeOptions = $product->getTypeInstance(true)->getConfigurableAttributesAsArray($product);
			$attributeOptions = array();
			
			foreach ($productAttributeOptions as $productAttribute) 
			{
				foreach ($productAttribute['values'] as $attribute) 
				{
					if(in_array($attribute['value_index'],$allAttributes))
					{
						$configure_price+=$attribute['pricing_value'];
					}
				}
			} 
		}
		$quote = Mage::helper('checkout/cart')->getCart()->getQuote();
		$attributePrice = 0;
		/* Calculate Custom Options Price */
		
		foreach ($quote->getItemsCollection() as $item)
		{
			if($item->getId() == $cartItem->getId())
			{
				if ($optionIds = $item->getProduct()->getCustomOption('option_ids')) 
				{   
					$attributePrice = 0;
					foreach (explode(',', $optionIds->getValue()) as $optionId) 
					{
						if ($option = $item->getProduct()->getOptionById($optionId)) 
						{
							$confItemOption = $item->getProduct()->getCustomOption('option_'.$option->getId());
							$group = $option->groupFactory($option->getType())
								->setOption($option)
								->setConfigurationItemOption($confItemOption);

							$attributePrice += $group->getOptionPrice($confItemOption->getValue(), 0);
						}
					}
				}
			}
		}
		$final_extra_price = $attributePrice + $configure_price ;
		
		/* pricing of attribue finish */
		if($additionaprice->getData())
		{
			if($buyInfo->getIndiesRecurringandrentalpaymentsSubscriptionType()) 
			{
				if($buyInfo->getIndiesRecurringandrentalpaymentsSubscriptionType()>=0)
				{
					if($postdata['bundle_option'])
					{
						$this->calculateUpdateBundleItemPrice($postdata,$cartItem);
						$price = Mage::helper('recurringandrentalpayments')->calculateParentBundlePrice($postdata,$cartItem);
						
						//Mage::dispatchEvent('sales_quote_remove_item', array('quote_item' => $cartItem));
						
						/*Mage::dispatchEvent('checkout_cart_product_add_before',$observer);
						return;*/
						/*$this->_getCart()->removeItem($cartItem->getId())
                   		 ->save();
               			 $this->_getSession()->setCartWasUpdated(true);*/
						 
					}
					elseif($product->getFirstPeriodPrice() > 0 )
					{
						$price = $product->getFirstPeriodPrice() ;
					}
					else
					{
						$termid = $buyInfo->getIndiesRecurringandrentalpaymentsSubscriptionType();
						$qty = $buyInfo->getQty();
						$productId = $buyInfo->getProduct();
						$types = Mage::getModel('recurringandrentalpayments/terms')->load($buyInfo->getIndiesRecurringandrentalpaymentsSubscriptionType());
						$termprice = $types->getPrice();
	
						if($types->getPriceCalculationType() == 1)
						{
							$termprice = $product->getPrice() * $types->getPrice()/100;
						}
						
						$price = $termprice + $final_extra_price;
					}
					$price = Mage::helper('directory')->currencyConvert($price, Mage::app()->getStore()->getBaseCurrencyCode(), Mage::app()->getStore()->getCurrentCurrencyCode());
					// Get the quote item
					$item = $observer->getItem();
					// Ensure we have the parent item, if it has one
					$item = ( $item->getParentItem() ? $item->getParentItem() : $item );
			   
					// Set the custom price
					$item->setCustomPrice($price);
					$item->setOriginalCustomPrice($price);
					$item->save();
					
					// Enable super mode on the product.
					$item->getProduct()->setIsSuperMode(true);
					if($additionaprice->getStartDate() == 1)
					{
						if($buyInfo->getIndiesRecurringandrentalpaymentsSubscriptionStart())
						{
							if(strtotime($buyInfo->getIndiesRecurringandrentalpaymentsSubscriptionStart()) < time())
							{
								$buyInfo->setIndiesRecurringandrentalpaymentsSubscriptionStart(date('m-d-Y'));
							}
						}
					}
				}
				else
				{
					// Get the quote item
					$item = $observer->getItem();
					// Ensure we have the parent item, if it has one
					$item = ( $item->getParentItem() ? $item->getParentItem() : $item );
					$product = Mage::getModel('catalog/product')->load($item->getProduct()->getId());
					// Set the custom price
					$item->setQty($postdata['qty']);
					$item->setCustomPrice(($product->getPrice()+$attributePrice));//added value of custom option
					$item->setOriginalCustomPrice(($product->getPrice()+$attributePrice));//added value of custom option
					$item->save();
					// Enable super mode on the product.
					$item->getProduct()->setIsSuperMode(true);
				}
			}
			else
			{		
				if($additionaprice->getPlanStatus() != 2) 
				{
					Mage::getSingleton('checkout/session')->addNotice('Please specify the products option(s)');
					$response = Mage::app()->getFrontController()->getResponse();
					$response->setRedirect($product->getProductUrl());
					$response->sendResponse();
					exit;
				}
				else  // If plan is Disable 
				{
					return ;
				}
			}
		}
	}

	public function savePaymentInfoInSession($observer)
    {
		try
        {
   			if (!Indies_Recurringandrentalpayments_Model_Subscription::isIterating()) {
                $quote = $observer->getEvent()->getQuote();
                if (!$quote->getPaymentsCollection()->count())
                    return;
                $Payment = $quote->getPayment();
                if ($Payment && $Payment->getMethod()) {
                    if ($Payment->getMethodInstance() instanceof Mage_Payment_Model_Method_Cc) {
                        // Credit Card number
                        if ($Payment->getMethodInstance()->getInfoInstance() && ($ccNumber = $Payment->getMethodInstance()->getInfoInstance()->getCcNumber())) {
                            $ccCid = $Payment->getMethodInstance()->getInfoInstance()->getCcCid();
                            $ccType = $Payment->getMethodInstance()->getInfoInstance()->getCcType();
                            $ccExpMonth = $Payment->getMethodInstance()->getInfoInstance()->getCcExpMonth();
                            $ccExpYear = $Payment->getMethodInstance()->getInfoInstance()->getCcExpYear();
                            Mage::getSingleton('customer/session')->setrecurringandrentalpaymentsCcNumber($ccNumber);
                            Mage::getSingleton('customer/session')->setrecurringandrentalpaymentsCcCid($ccCid);
                        }
                    }
                }
            }
        } catch (Exception $e)
        {
            //throw($e);
        }
    }

    public function salesOrderItemSaveAfter($observer)
    {
	    $orderItem = $observer->getEvent()->getItem();
        $product = Mage::getModel('catalog/product')
                ->setStoreId($orderItem->getOrder()->getStoreId())
                ->load($orderItem->getProductId());
		if (method_exists($product->getTypeInstance(), 'prepareOrderItemSave'))
		{
			$product->getTypeInstance()->prepareOrderItemSave($product, $orderItem);
		}
    }


    /**
     * Assigns subscription of product to current user
     * @param object $observer
     * @return
     */
    public function assignSubscriptionToCustomer($observer)
    {
		$order = $observer->getEvent()->getOrder();
        $quote = $observer->getEvent()->getQuote();
		$this->setReucrringOrderInfo($order, $quote);
	}
    public function paypalExpressCheckout($observer)
    {
       $order = $observer->getEvent()->getOrder();
	   if($order->getPayment()->getMethod() == "paypal_express")
	   {
		  $store_id = Mage::getSingleton('core/store')->load($order->getStoreId());
		  $quote = Mage::getModel('sales/quote')->setStore($store_id )->load($order->getQuoteId());
		  $this->setReucrringOrderInfo($order,$quote);
	   }
     }
	
	/*for multishiping change log 24-2*/
	public function setMultishippingQuote($observer)
	{
		$orderIds=$observer->getEvent()->getOrderIds();
		foreach($orderIds as $orderId)
		{
			$order=Mage::getModel('sales/order')->load($orderId);
			$quote = Mage::getModel('sales/quote')->load($order->getQuoteId());
			$this->setReucrringOrderInfo($order, $quote);
		}
	}
    public function setReucrringOrderInfo($order, $quote)
    {
		if(Mage::getSingleton('core/session')->getIsaccepted()):
			Mage::getSingleton('core/session')->unsIsaccepted(); 
		endif;
    	$items = $order->getAllVisibleItems();

		$paymentMethod = $order->getPayment()->getMethod();
        $period_date_hashs = array();
		$Subscription=false;
	    if (!Mage::getSingleton('recurringandrentalpayments/subscription')->getId()) 
		{
            /***
             * Joins set of products to one subscription if this can be done..
             **/
            foreach ($items as $item)
            {	
				$buyInfo = $item->getBuyRequest();
				if (Mage::helper('recurringandrentalpayments')->isSubscriptionType($item))
				{
				   	$period_type = $buyInfo->getIndiesRecurringandrentalpaymentsSubscriptionType();
					$Options =Mage::getModel('recurringandrentalpayments/terms')->load($period_type);
				   	$x = Mage::getModel('recurringandrentalpayments/plans')->load($Options->getPlanId())->getStartDate();
				   	$startdate = now();
					
					if($x==1)
				   	{
						$startdate = $buyInfo->getIndiesRecurringandrentalpaymentsSubscriptionStart();
					}
					if($x==3)
					{
						$startdate= date('Y-m-01');
						$startdate = new Zend_Date(date('M-01-Y', strtotime("+1 months", strtotime(date("Y-m-d")))));

					}
                   if($x != 2)  // x = Subscription Start Date 
					{
						if (preg_match('/[\d]{4}-[\d]{2}-[\d]{2}/', $startdate))
						{
							$date = new Zend_Date($startdate);
						}
						else{
							$date = new Zend_Date($startdate, Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT));						
						}
						$date_start = $date->toString(Indies_Recurringandrentalpayments_Model_Subscription::DB_DATE_FORMAT);	
					}
					else   // add this  because when admin set subscription start date as 'Moment of purchase' that time issue is generated.
					{
						// We take magento's date.
						$date_start = Mage::getModel('core/date')->date('Y-m-d');
					}
					if ($period_type > 0) {
                        if (!isset($period_date_hashs[$period_type . self::HASH_SEPARATOR . $date_start])) {
                            $period_date_hashs[$period_type . self::HASH_SEPARATOR . $date_start] = array();
                        }
                        $period_date_hashs[$period_type . self::HASH_SEPARATOR . $date_start][] = $item;
                    }
                }
            }
			foreach ($period_date_hashs as $hash => $OrderItems)
            {
                $Options = $this->_getOrderItemOptionValues($item);
				
				$discountamount = 'null';
				$applydiscounton = 0;

				if(Mage::helper('recurringandrentalpayments')->isApplyDiscount())   // (enable/disable)
				{
					$amount = Mage::helper('recurringandrentalpayments')->discountAmount() ;
					$calculation_type = Mage::helper('recurringandrentalpayments')->applyDiscountType();
					if(Mage::helper('recurringandrentalpayments')->discountAvailableTo() == 3 )   // Specific customer group
					{
						$customer_group = explode(',',Mage::helper('recurringandrentalpayments')->selectedCustomerGroup());
						$groupId = Mage::getSingleton('customer/session')->getCustomerGroupId();
						if(in_array($groupId,$customer_group))
						{
							$add_discount = 1;
						}
						else
						{
							$add_discount = 0;
						}
					}
					else
					{ 
					   $add_discount = 1;
					}
					
					if($add_discount == 1)
					{
						if(Mage::helper('recurringandrentalpayments')->applyDiscountOn()!= 3) 
						{
							if($calculation_type == 1)  //Fixed
								$discountamount = $amount;
							else
								$discountamount = $amount.'%';

							$applydiscounton = Mage::helper('recurringandrentalpayments')->applyDiscountOn();
						}
						else
						{
							if($calculation_type == 1)  //Fixed
								$discountamount = $amount;
							else
								$discountamount = $amount.'%';

							$applydiscounton = 3;
						}
					}
				}

                list($period_type, $date_start) = explode(self::HASH_SEPARATOR, $hash);
			    $Subscription = Mage::getModel('recurringandrentalpayments/subscription')
                        ->setCustomer(Mage::getModel('customer/customer')->load($order->getCustomerId()))
                        ->setPrimaryQuoteId($quote->getId())
                        ->setDateStart($date_start)
                        ->setStatus((Mage::helper('recurringandrentalpayments')->isOrderStatusValidForActivation($order)) ? Indies_Recurringandrentalpayments_Model_Subscription::STATUS_ENABLED : Indies_Recurringandrentalpayments_Model_Subscription::STATUS_SUSPENDED)
                        ->setTermType($period_type)
                        ->initFromOrderItems($OrderItems, $order)
						->setDiscountAmount($discountamount)
						->setApplyDiscountOn($applydiscounton)
						->save();

			// Start : Date : 2015-01-06 : Make change for add first sequence as a paid of order when it place	
			    Mage::getModel('recurringandrentalpayments/sequence')
                            ->setSubscriptionId($Subscription->getId())
                            ->setDate($date_start)
							->setOrderId($order->getId())
							->setStatus(Indies_Recurringandrentalpayments_Model_Sequence::STATUS_PAYED)
					 		->setTransactionStatus('Success')
							->setMailsent(1)
                            ->save();  
			// End : Date : 2015-01-06 : Make change for add first sequence as a paid of order when it place 
		    // Run payment method trigger
				
                $Subscription
                        ->getMethodInstance($paymentMethod)
                        ->onSubscriptionCreate($Subscription, $order, $quote);
				
				
				if(isset($Subscription) && $Subscription)
				{
					$alert= Mage::getModel('recurringandrentalpayments/alert_event');
					if(Mage::getStoreConfig(Indies_Recurringandrentalpayments_Helper_Config::XML_PATH_ORDER_STATUS_NEW) == '1')
					{
						$alert->send($Subscription,Mage::getStoreConfig(Indies_Recurringandrentalpayments_Helper_Config::XML_PATH_ORDER_STATUS_NEW_TEMPLATE),0);
					}
					if(Mage::getStoreConfig(Indies_Recurringandrentalpayments_Helper_Config::XML_PATH_SEND_ORDER_CONFORMATION_EMAIL) == '1')
					{
						$alert->send($Subscription,
									Mage::getStoreConfig(Indies_Recurringandrentalpayments_Helper_Config::XML_PATH_SEND_ORDER_CONFORMATION_EMAIL_TEMPLATE),
									0,
									Mage::getStoreConfig(Indies_Recurringandrentalpayments_Helper_Config::XML_PATH_SEND_ORDER_CONFORMATION_EMAIL_SENDER),
									Mage::getStoreConfig(Indies_Recurringandrentalpayments_Helper_Config::XML_PATH_SEND_ORDER_CONFORMATION_EMAIL_CC_TO)
									);
					} 	
				}
				else
				{
					$Subscription = '';
					//break;
				}
						
            }
			Mage::getSingleton('core/session')->unsBillingAgreementId();
    
			/* Start :  When order is placed from Prolong Action then related subscriptions needs to cancel  */
			if($buyInfo->getProlongForId() != '')
			{
			  $model = Mage::getModel('recurringandrentalpayments/subscription')->load($buyInfo->getProlongForId());
			  $model->cancel()->save();
			  $sender = Mage::getStoreConfig(Indies_Recurringandrentalpayments_Helper_Config::XML_PATH_ORDER_STATUS_CHANGE_SENDER);
			  $ccto = Mage::getStoreConfig(Indies_Recurringandrentalpayments_Helper_Config::XML_PATH_ORDER_STATUS_CHANGE_CC_TO);
				
			  $alert= Mage::getModel('recurringandrentalpayments/alert_event');
			  $alert->send($model,Mage::getStoreConfig(Indies_Recurringandrentalpayments_Helper_Config::XML_PATH_ORDER_STATUS_CANCLE_TEMPLATE),1,$sender,$ccto);
			}
        }
    }

    /**
     * Returns product SKU for specified options set
     * @param Mage_Catalog_Model_Product $Product
     * @param mixed                      $options
     * @return string
     */
    public function getProductSku(Mage_Catalog_Model_Product $Product, $options = null)
    {
        if ($options) {
            $productOptions = $Product->getOptions();
            foreach ($options as $option)
            {
                if ($ProductOption = @$productOptions[$option['option_id']]) {
                    $values = $ProductOption->getValues();
                    if (($value = $values[$option['option_value']]) && $value->getSku()) {
                        return $value->getSku();
                    }
                }
            }
        }
       return $Product->getSku();
    }

    /**
     * Returns current customer
     * @return Mage_Customer_Model_Customer
     */
    public function getCustomer()
    {
        if (!$this->getData('customer'))
		{
            $customer = Mage::getSingleton('customer/session')->getCustomer();
            $this->setCustomer($customer);
        }
        return $this->getData('customer');
    }

    /**
     * Return sales item as object
     * @param Mage_Sales_Model_Order_Item $item
     * @return Varien_Object
     */
    protected function _getOrderItemOptionValues(Mage_Sales_Model_Order_Item $item)
    {
        $buyRequest = $item->getProductOptionByCode('info_buyRequest');
        $obj = new Varien_Object;
        $obj->setData($buyRequest);
        return $obj;
    }

    /**
     * Activates or suspends subscription on order status change
     * @param object $observer
     * @return
     */
    public function updateSubscriptionStatus($observer)
    {
		
		$Order = $observer->getOrder();
        $status = $Order->getStatus();
        $items = Mage::getModel('recurringandrentalpayments/subscription_item')->getCollection()->addOrderFilter($Order);
        $items->getSelect()->group('subscription_id');
		
		/**
         * this is for primary order
         */
        if ($items->count())
		{
            foreach ($items as $item)
            {
   				$Subscription = Mage::getModel('recurringandrentalpayments/subscription')->load($item->getSubscriptionId());
                // If order is complete now and subscription is suspeneded - activate subscription
                if (Mage::helper('recurringandrentalpayments')->isOrderStatusValidForActivation($Order) && ($Subscription->getStatus() == Indies_Recurringandrentalpayments_Model_Subscription::STATUS_SUSPENDED)) 
				{
                    if (($status == Mage_Sales_Model_Order::STATE_PROCESSING) && !Mage::helper('recurringandrentalpayments')->isSubscriptionItemInvoiced($item)) 
					{
						Mage::getModel('recurringandrentalpayments/subscription_flat')
							->load($Subscription->getId(), 'subscription_id')
							->setFlatLastOrderStatus($Subscription->getLastOrder()->getStatus())
							->save();
                     	continue;
                    }
                    $Subscription->setStatus(Indies_Recurringandrentalpayments_Model_Subscription::STATUS_ENABLED)->save();
                }
				elseif ($Subscription->isActive() && !Mage::helper('recurringandrentalpayments')->isOrderStatusValidForActivation($Order))
                {
				    $Subscription->setStatus(Indies_Recurringandrentalpayments_Model_Subscription::STATUS_SUSPENDED)->save();
                }
                else
				{
                    Mage::getModel('recurringandrentalpayments/subscription_flat')
                            ->load($Subscription->getId(), 'subscription_id')
                            ->setFlatLastOrderStatus($Subscription->getLastOrder()->getStatus())
          	                ->save();
				}
            }
		}
        /**
         * If payment failed(e.g. order status is not completed), suspend affected subscription
         */

        if ($Sequence = Mage::getModel('recurringandrentalpayments/sequence')->load($Order->getId(), 'order_id'))
		{
            if ($Sequence->getId()) 
			{
                $Subscription = $Sequence->getSubscription();
                // If order is complete now and subscription is suspeneded - activate subscription
                if (Mage::helper('recurringandrentalpayments')->isOrderStatusValidForActivation($Order)) {
                    $Sequence->setStatus(Indies_Recurringandrentalpayments_Model_Sequence::STATUS_PAYED)->save();
                    $Subscription
                            ->setStatus(Indies_Recurringandrentalpayments_Model_Subscription::STATUS_ENABLED)
                            ->setFlagNoSequenceUpdate(1)
                            ->save()
                            ->setFlagNoSequenceUpdate(0);
                } 
				elseif ($Subscription->isActive() && !Mage::helper('recurringandrentalpayments')->isOrderStatusValidForActivation($Order))
                {
					$Subscription
                            ->setStatus(Indies_Recurringandrentalpayments_Model_Subscription::STATUS_SUSPENDED)
                            ->setFlagNoSequenceUpdate(1)
                            ->save()
                            ->setFlagNoSequenceUpdate(0);
                }
            }
       }
    }

    /**
     * Checks if subscription is suspended. Remove download links if product attribute 'Download access
     * based on the subscription status' is true
     * @param object $observer
     * @return
     */
    public function rnrSubscriptionSuspend($observer)
    {
		$subscription = $observer->getEvent()->getSubscription();
        $purchasedLinks = Mage::getResourceModel('downloadable/link_purchased_item_collection');
        $orders = Mage::getModel('recurringandrentalpayments/sequence')->getOrdersBySubscription($subscription);
        $orders[] = $subscription->getOrder();
        foreach ($orders as $order)
        {
            foreach ($order->getAllItems() as $item)
            {
				$product = Mage::getModel('catalog/product')->load($item->getProductId());
                if ($product->getTypeId() != 'downloadable')
                    continue;
                if ($product->getIndiesrecurringandrentalpaymentsDownloadByStatus()) {
                    foreach ($purchasedLinks as $link)
                        if ($link->getOrderItemId() == $item->getId())
                            $link->setStatus(Mage_Downloadable_Model_Link_Purchased_Item::LINK_STATUS_PENDING_PAYMENT)->save();
                }
            }
        }
    }

    /**
     * Checks if subscription is reactivated. Add download links
     * @param object $observer
     * @return
     */
    public function rnrSubscriptionReactivate($observer)
    {
		$subscription = $observer->getEvent()->getSubscription();
        $purchasedLinks = Mage::getResourceModel('downloadable/link_purchased_item_collection');
        $orders = Mage::getModel('recurringandrentalpayments/sequence')->getOrdersBySubscription($subscription);
        $orders[] = $subscription->getOrder();
        foreach ($orders as $order)
        {
			foreach ($order->getAllItems() as $item)
            {
				$product = Mage::getModel('catalog/product')->load($item->getProductId());
                if ($product->getTypeId() != 'downloadable')
                    continue;
                foreach ($purchasedLinks as $link)
                    if ($link->getOrderItemId() == $item->getId())
                        $link->setStatus(Mage_Downloadable_Model_Link_Purchased_Item::LINK_STATUS_AVAILABLE)->save();
            }
        }
    }
    /**
     * returns all sequences with given status for particular subscription
     * @return Indies_recurringandrentalpayments_Model_Mysql4_Sequence_Collection
     */
    public function getSequenceItems($subscription, $status)
    {
        return Mage::getModel('recurringandrentalpayments/sequence')
                ->getCollection()
                ->addSubscriptionFilter($subscription)
                ->addStatusFilter($status);
    }

    public function paymentIsAvailable($observer)
    {
		$method = $observer->getMethodInstance();
        $quote = $observer->getQuote();
        if (is_null($quote)) {
           return;
        }
        if (!$quote instanceof Mage_Sales_Model_Quote) {
            $observer->getResult()->isAvailable = false;
            return;
        }
        $haveItems = false;
        foreach ($quote->getAllItems() as $item)
        {
		  $buyInfo = $item ->getBuyRequest();
		  $SubscriptionType = $buyInfo->getIndiesRecurringandrentalpaymentsSubscriptionType();
          if (Mage::helper('recurringandrentalpayments')->isSubscriptionType($item) && (!is_null($SubscriptionType) && ($SubscriptionType > 0))) {
                $haveItems = true;
                break;
            }
        }
        if ($haveItems && !Mage::getModel('recurringandrentalpayments/subscription')->hasMethodInstance($method->getCode()))
		{
			$observer->getResult()->isAvailable = false;
		}
    }

    public function onepageCheckoutSaveOrderBefore($observer)
    {
		$quote = $observer->getQuote();
        $order = $observer->getOrder();
        $haveItems = false;
        foreach ($quote->getAllVisibleItems() as $item)
        {
			$buyInfo = $item ->getBuyRequest();
		  	$SubscriptionType = $buyInfo->getIndiesRecurringandrentalpaymentsSubscriptionType();
			//$SubscriptionType = Mage::getSingleton("core/session")->getRecurringandrentalpaymentsSubscriptionType();
            if (!is_null($SubscriptionType))
			{
				$haveItems = true;
                break;
            }
        }
        if (!Mage::getSingleton('recurringandrentalpayments/subscription')->getId() && $haveItems) 
		{
            switch($order->getPayment()->getMethod()) {
				
                case Indies_Recurringandrentalpayments_Model_Payment_Method_Authorizenet::PAYMENT_METHOD_CODE:
					$paymentModel = Mage::getModel('recurringandrentalpayments/payment_method_authorizenet'); 
                    $service = $paymentModel->getWebService();
                    $service->setPayment($quote->getPayment());
                    $subscription = new Varien_Object();
                    $subscription->setStoreId($order->getStoreId());
                    $service->setSubscription($subscription);
                    try{
                        $service->createCIMAccount();
                    }
                    catch(Exception $e){
                        throw new Indies_Recurringandrentalpayments_Exception($e->getMessage());
                    }
                default:
            }
        }
    }
	public function SalesOrderInvoicePay($observer)
	{
		$invoice = $observer->getEvent()->getInvoice();
		$this->sentPaymentConfirmationmail($invoice);
	}
	public function SalesOrderPyamentPay($observer)
	{
		$invoice = $observer->getInvoice();
		$this->sentPaymentConfirmationmail($invoice);
	}
	
	public function sentPaymentConfirmationmail($invoice)
	{
		$subscription = Mage::getModel('recurringandrentalpayments/subscription_item')->load($invoice->getOrderId(),'primary_order_id');
		$data = $subscription->getData();
		
		if(!empty($data))
		{
			switch ($invoice->getState()) 
			{
				case Mage_Sales_Model_Order_Invoice::STATE_PAID :
				if(Mage::getStoreConfig(Indies_Recurringandrentalpayments_Helper_Config::XML_PATH_NEXT_PAYMNET_CONFORMATION, Mage::app()->getStore()) == '1')
				{
					$alert = Mage::getModel('recurringandrentalpayments/alert_event');
					$customer = Mage::getModel('customer/customer')->load($invoice->getCustomerId());
					$model = Mage::getModel('recurringandrentalpayments/subscription')->load($subscription->getSubscriptionId());
					$alert->send($model,Mage::getStoreConfig(Indies_Recurringandrentalpayments_Helper_Config::XML_PATH_NEXT_PAYMNET_CONFORMATION_TEMPLATE),0, Mage::getStoreConfig(Indies_Recurringandrentalpayments_Helper_Config::XML_PATH_NEXT_PAYMNET_CONFORMATION_SENDER));	
				}
				break;
			}
		}
		return $this;
	}
	
	public function calculateBundleItemPrice($params,$item)
	{
		$termid = $params['indies_recurringandrentalpayments_subscription_type'];
		$cart = Mage::helper('checkout/cart')->getCart()->getQuote()->getAllItems();
	  
		/* Change bundle items price */
		$price = '';
		$helper = Mage::helper('recurringandrentalpayments');
		foreach ($cart as $i)
		{
			if($i->getId() == '')  // Dont getting id for currently adding bundle item.
			{
				$options = $i->getOptions();
				foreach ($options as $option)
				{
					if($option->getCode() == 'bundle_selection_attributes')
					{
						$unserialized = unserialize($option->getValue());
						if($item->getProduct()->getFirstPeriodPrice() > 0)
							$price = $item->getProduct()->getFirstPeriodPrice();
						else
							$price = $helper->getBundleItemsPrice($termid,$unserialized['price']);
						$unserialized['price'] = number_format($price, 2, '.', ',');
						$option->setValue(serialize($unserialized));
					}
				}
				try {	
					$i->setOptions($options)->save(); 
				} catch (Exception $e) 	{	}
					$i->setCustomPrice($price);
                	$i->setOriginalCustomPrice($price); 		
			}
		}
		return;
	}
	public function calculateUpdateBundleItemPrice($params,$item)
	{
		$quote = Mage::helper('checkout/cart')->getCart()->getQuote();
		$termid = $params['indies_recurringandrentalpayments_subscription_type'];
  		$helper = Mage::helper('recurringandrentalpayments');
		
		$cart = Mage::helper('checkout/cart')->getCart()->getQuote()->getAllItems();

		$all_array = array();
		foreach ($cart as $i)
		{
			if ($i->getParentItemId() == $item->getId())
			{
				$options = $i->getOptions();
				
				foreach ($options as $option)
				{
					if($option->getCode() == 'bundle_selection_attributes')
					{
						$unserialized = unserialize($option->getValue());
						if($item->getProduct()->getFirstPeriodPrice() > 0)
							$price = $i->getProduct()->getFirstPeriodPrice();
						else
							$price = $helper->getBundleItemsPrice($termid,$unserialized['price']);
						$unserialized['price'] = number_format($price, 2, '.', ',');
						$data = Mage::getModel('sales/quote_item_option')->load($option->getId())->setValue(serialize($unserialized))->save();
					}
				}
				try {
				} catch (Exception $e) 	{Mage::log($e->getMesage());}
				$product = Mage::getModel('catalog/product')->load($params['product']);
				$i->setCustomPrice($price);
				$i->setOriginalCustomPrice($price); 
				if ($product->getPrice() == 0 )
				{ 
					$i->setPrice($price);
					$i->save();
				}
			}
		}
	}
	
	public function save()
    {
  		$data = $this->_getData('fieldset_data');
  		$obj = Mage::helper('recurringandrentalpayments');
		if($data['serial_key'] != '')
  		{
  			 $serialkey = $data['serial_key'];
  			 if($obj->canRun($serialkey))
   			{
        		  return parent::save();
   			}
  			 else
   			{
   				Mage::throwException($obj->getAdminMessage());
   			}
  		}
 		 else
 		 {
 			  Mage::throwException($obj->getAdminMessage());
		}
    }	
}
