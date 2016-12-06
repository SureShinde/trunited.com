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

class Indies_Recurringandrentalpayments_Helper_Data extends Mage_Core_Helper_Abstract
{
 	
	public function checkoutAllowedForGuest()
    {
        if (Mage::getStoreConfig(Indies_Recurringandrentalpayments_Helper_Config::XML_PATH_GENERAL_ANONYMOUS_SUBSCRIPTIONS, Mage::app()->getStore()) == '1')
            return true;
        return false;
    }
	
	public function isOrderStatusValidForActivation($order)
    {
     	if (
            ($order->hasInvoices() && Mage::getStoreConfig(Indies_Recurringandrentalpayments_Helper_Config::XML_PATH_ACTIVE_ORDER_STATUS, Mage::app()->getStore()) == "processing" && $order->getStatus() != 'closed' && $order->getStatus() != 'canceled') ||
            ($order->hasShipments() && Mage::getStoreConfig(Indies_Recurringandrentalpayments_Helper_Config::XML_PATH_ACTIVE_ORDER_STATUS, Mage::app()->getStore()) == "complete" && $order->getStatus() != 'closed' && $order->getStatus() != 'canceled') ||
            ($order->getIncrementId() && Mage::getStoreConfig(Indies_Recurringandrentalpayments_Helper_Config::XML_PATH_ACTIVE_ORDER_STATUS, Mage::app()->getStore()) == "pending" && $order->getStatus() != 'closed' && $order->getStatus() != 'canceled')
        )
    	{
    	  return true;
   		 }
    	    return false;
   	 }

	public static function isSubscriptionType($typeId)
    {
       
		if ($typeId instanceof Mage_Catalog_Model_Product)
		{
			$typeId = $typeId->getTypeId();
        } 
		elseif (($typeId instanceof Mage_Sales_Model_Order_Item) || ($typeId instanceof Mage_Sales_Model_Quote_Item))
		{
			$plans_product = Mage::getModel('recurringandrentalpayments/plans_product')->load($typeId->getId(),'product_id');
			
			$additionaprice = Mage::getModel('recurringandrentalpayments/plans')->load($plans_product->getPlanId(),'plan_id');

			if(Mage::getModel('recurringandrentalpayments/plans')->load($plans_product->getPlanId(),'plan_id'))
			{
				$typeId = $typeId->getProductType();
				return true;
			}
        }
        return false;
    }
	
	public function isSubscriptionItemInvoiced(Indies_Recurringandrentalpayments_Model_Subscription_Item $item)
    {
        $invoiced = (float)$item->getOrderItem()->getQtyInvoiced();
        if ($invoiced)
            return true;
        return false;
    }
	
	public function getAllSubscriptionTypes()
    {
        $types = Mage::getModel('recurringandrentalpayments/terms')->getCollection();
        $out = array();
        foreach ($types as $type) {
           $out[] = $type->getLabel();
        }
        return $out;
    }
	
	public function getDomain ()
    {
        $domain = $_SERVER['SERVER_NAME'];

        $temp = explode('.', $domain);
        $exceptions = array(
            'co.uk',
            'com.au',
			'com.hk',
			'co.nz',
			'co.in',
			'com.sg'
            );

            $count = count($temp);
            $last = $temp[($count-2)] . '.' . $temp[($count-1)];

            if(in_array($last, $exceptions))	{
                $new_domain = $temp[($count-3)] . '.' . $temp[($count-2)] . '.' . $temp[($count-1)];
            }
            else	{
                $new_domain = $temp[($count-2)] . '.' . $temp[($count-1)];
            }
            return $new_domain;
    }


    public function checkEntry ($domain, $serial)
    {
        $key = sha1(base64_decode('UmVjdXJyaW5nQW5kU3Vic2NyaXB0aW9uUGF5bWVudHM='));
        if(sha1($key.$domain) == $serial)
		{
            return true;
        }
        return false;
    }

	public function getRnrSubscriptionPrice($product, $includeCatalogRule = false)
    {
        return Mage::getSingleton('recurringandrentalpayments/product_price')->getRnrSubscriptionPrice($product, $includeCatalogRule);
    }


    public function getRnrFirstPeriodPrice($product)
    {
        return Mage::getSingleton('recurringandrentalpayments/product_price')->getRnrFirstPeriodPrice($product);
    }
	
    public function canRun($temp = '')
    {
		if($_SERVER['SERVER_NAME'] == "localhost" || $_SERVER['SERVER_NAME'] == "127.0.0.1")
		{
			return true;
		}
		if(!$temp)
		{
			$temp = Mage::getStoreConfig('recurringandrentalpayments/license_status_group/serial_key',Mage::app()->getStore());
		}
		$url = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB);
		$parsedUrl = parse_url($url);
		$host = explode('.', $parsedUrl['host']);
		$subdomains = array_slice($host, 0, count($host) - 2 );
		
		if(sizeof($subdomains) && ($subdomains[0] == 'test'|| $subdomains[0] == 'demo' || $subdomains[0] == 'dev'))
		{
			return true;
		}
		$original = $this->checkEntry($_SERVER['SERVER_NAME'], $temp);
        $wildcard = $this->checkEntry($this->getDomain(), $temp);

		if(!$original && !$wildcard)
		{
            return false;
        }
        return true;
    }
	
	public function getMessage ()
	{
		return base64_decode('PGRpdiBzdHlsZT0iYm9yZGVyOjNweCBzb2xpZCAjRkYwMDAwOyBtYXJnaW46MTVweCAwOyBwYWRkaW5nOjVweDsiPkxpY2Vuc2Ugb2YgPGI+UmVjdXJyaW5nIGFuZCBTdWJzY3JpcHRpb24gUGF5bWVudHM8L2I+IGV4dGVuc2lvbiBoYXMgYmVlbiB2aW9sYXRlZC4gVG8gZ2V0IHNlcmlhbCBrZXkgcGxlYXNlIGNvbnRhY3QgdXMgb24gPGI+aHR0cHM6Ly93d3cubWlsb3BsZS5jb20vbWFnZW50by1leHRlbnNpb25zL2NvbnRhY3RzLzwvYj48L2Rpdj4=');
	}
	public function getAdminMessage ()
	{
		return $this->__(base64_decode('PGRpdj5MaWNlbnNlIG9mIDxiPk1pbG9wbGUgUmVjdXJyaW5nIGFuZCBTdWJzY3JpcHRpb24gUGF5bWVudHM8L2I+IGV4dGVuc2lvbiBoYXMgYmVlbiB2aW9sYXRlZC4gVG8gZ2V0IHNlcmlhbCBrZXkgcGxlYXNlIGNvbnRhY3QgdXMgb24gPGI+aHR0cHM6Ly93d3cubWlsb3BsZS5jb20vbWFnZW50by1leHRlbnNpb25zL2NvbnRhY3RzLzwvYj48L2Rpdj4='));
	}

	public function isEnabled()
  	{
   		if(Mage::getStoreConfig(Indies_Recurringandrentalpayments_Helper_Config::XML_PATH_MODULE_STATUS, Mage::app()->getStore()) == '1'
		&& (!Mage::getStoreConfig('advanced/modules_disable_output/Indies_Recurringandrentalpayments', Mage::app()->getStore())))
		{
	  		 return true;
   		}
  	 return false; 
  	}
	public function getFilter($data) {
        $result = array();
        $filter = new Zend_Filter();
        $filter->addFilter(new Zend_Filter_StringTrim());

        if ($data) {
            foreach ($data as $key => $value) {
                if (is_array($value)) {
                    $result[$key] = $this->getFilter($value);
                } else {
                    $result[$key] = $filter->filter($value);
                }
            }
        }
        return $result;
    }
		public function isApplyDiscount()
	{
		 if (Mage::getStoreConfig(Indies_Recurringandrentalpayments_Helper_Config::XML_PATH_APPLY_DISCOUNT, Mage::app()->getStore()) == '1')
            return true;
        return false;
		
	} 

	public function discountAvailableTo()
	{
		return Mage::getStoreConfig('recurringandrentalpayments/discount_group/discount_available_to', Mage::app()->getStore());
	}
	public function selectedCustomerGroup()
	{
		return Mage::getStoreConfig('recurringandrentalpayments/discount_group/apply_discount_group', Mage::app()->getStore());
	} 
	public function applyDiscountOn()
	{
		return Mage::getStoreConfig('recurringandrentalpayments/discount_group/apply_discount_on', Mage::app()->getStore());
	} 
	public function applyDiscountType()
	{
		return Mage::getStoreConfig('recurringandrentalpayments/discount_group/discount_cal_type', Mage::app()->getStore());
	} 
	public function discountAmount()
	{
		return Mage::getStoreConfig('recurringandrentalpayments/discount_group/discount_amount', Mage::app()->getStore());
	} 
	public function convertDiscountAmount($amount)
	{
		$current_currency_code = Mage::app()->getStore()->getCurrentCurrencyCode(); 
		$base_currency_code = Mage::app()->getStore()->getBaseCurrencyCode();
		if($current_currency_code != $base_currency_code)
		{
			$currencyRates = Mage::getModel('directory/currency')->getCurrencyRates($base_currency_code, $current_currency_code);
			$currentCurrencyRate = $currencyRates[$currentCurrencyCode];
			$amount = $amount / $currentCurrencyRate;

		}
		return $amount;
	}
	/* Check about valid to display terms and conditions link on cart page */
	public function displayTermsandConditions()
	{
		$enable = Mage::getStoreConfig('recurringandrentalpayments/clause_settings/enable');
		$clause_detail = Mage::getStoreConfig('recurringandrentalpayments/clause_settings/clause');	
		$quote = Mage::getSingleton('checkout/session')->getQuote()->getAllVisibleItems();
		foreach ($quote as $item)
		{
			 $infoBuyRequest = $item->getOptionByCode('info_buyRequest');
			 $buyRequest = new Varien_Object(unserialize($infoBuyRequest->getValue()));
			 $isSubscribed = 0 ;
			 if ($buyRequest->getIndiesRecurringandrentalpaymentsSubscriptionType())
			 {
				$isSubscribed = 1 ;
				 break;
			 }
		}
		if($this->canRun() && $this->isEnabled() && $enable && $isSubscribed)
		{
			return true;
		}
		return false;
	}
	public function checkAvailabilityForTermsDisplay($id)
	{
		$isavailable = Mage::getStoreConfig(Indies_Recurringandrentalpayments_Helper_Config::XML_PATH_GENERAL_ANONYMOUS_SUBSCRIPTIONS);
		$plans_product = Mage::getModel('recurringandrentalpayments/plans_product')->load($id);
		$plan = Mage::getModel('recurringandrentalpayments/plans')->load($plans_product->getPlanId());
		$_loggedIn = Mage::getSingleton('customer/session')->isLoggedIn();
		if($plan->getPlanStatus()==1)
		{	
			if($isavailable ==1 ||( ($isavailable ==2 ) && $_loggedIn))
			{ 
				$available = 1; 
			}
			elseif($isavailable == 3 && $_loggedIn)
			{
				$available = 0;
				$groupId = Mage::getSingleton('customer/session')->getCustomerGroupId();
				$customer_group = explode(',',Mage::getStoreConfig(Indies_Recurringandrentalpayments_Helper_Config::XML_PATH_CUSTOMER_GROUP));
				if (in_array($groupId,$customer_group))		 $available = 1;
				else $available = 3; 
			}
			else
			{
				   $available = 0; 
			}
		}
		else
		{
			$available = 3 ;
		}
		return $available;
	}
	public function getBundleItemsPrice($termid,$item_amount)
	{
		$types = Mage::getModel('recurringandrentalpayments/terms')->load($termid);
		$price = $types->getPrice();
		if($types->getPriceCalculationType() == 1)
		{	
			$price = $item_amount * $types->getPrice()/100;
		}
		$price = Mage::helper('directory')->currencyConvert($price, Mage::app()->getStore()->getBaseCurrencyCode(), Mage::app()->getStore()->getCurrentCurrencyCode());
		return $price;
	}
	public function calculateParentBundlePrice($params,$item)
	{
		$termid = $params['indies_recurringandrentalpayments_subscription_type'];
		$types = Mage::getModel('recurringandrentalpayments/terms')->load($termid);
		$cal = 0 ;
		if($types->getPriceCalculationType() == 1)	$cal = 1;
		
		$sel_bundle_options = $item->getProduct()->getTypeInstance(true)->getOrderOptions($item->getProduct());
		$price = 0 ;
		$amount = 0 ;
		$product = Mage::getModel('catalog/product')->load($params['product']);
		
		if ($product->getPrice() > 0 ) // Fixed Price Product
		{
			$amount = $types->getPrice();
			if($cal == 1)
			{
				$amount = $product->getPrice() * $types->getPrice()/100;
			}
		}
		
		// Change Bundle Product Price
		if ($sel_bundle_options)
 		{
    		if (isset($sel_bundle_options['bundle_options']))
    		{
				$options = $sel_bundle_options['bundle_options'];
				foreach ($options as $opt)
				{
					$value = $opt['value'];
					foreach ($value as $v )
					{
						if($item->getProduct()->getFirstPeriodPrice() > 0)
						{
							$price = $item->getProduct()->getFirstPeriodPrice();
						}
						else
						{
							$price = $types->getPrice() * $v['qty'];
							if($cal == 1)
							{
								$price = ($v['price'] * $types->getPrice()/100) ;
							}
						}
						$amount = $amount + $price ;
						$item->setCustomPrice($price);
						$item->setOriginalCustomPrice($price);
					}
				}
			}
		}
	    return $amount;
	}
	public function calCustomOptionPrice($info,$orderItem)
	{
		# Start : to add custom option price in next order price
		$options = $info->getOptions();
		$totalCustomOptionPrice = 0;

		if(sizeof($options))
		{
			foreach ($options as $key => $value)
			{
				$optionData = $orderItem->getProduct()->getOptionById($key);
				if ($optionData['type'] == 'field')
				{
					$totalCustomOptionPrice = $optionData['price'];
				}
				else
				{
				   # if check box then $value will be an array in if condition will fire
				   if(is_array($value))
				   {
						foreach($value as $vKey => $optionId)
						{
							if($optionData != NULL)// check is option exist, if exist then $optionData will not be null
							{
								 foreach ($optionData->getValues() as $v)
								 {
									 if ($v['option_type_id'] == $optionId)
									 {
											$totalCustomOptionPrice += $v->getPrice();
									 }
								 }
							}
						}
					}
					else # else for drop down where only one value
					{
						if($optionData != NULL)// check is option exist, if exist then $optionData will not be null
						{
							foreach ($optionData->getValues() as $v)
							 {
								 if ($v['option_type_id'] == $value)
								 {
										$totalCustomOptionPrice += $v->getPrice();
								 }
							 }
						}
					}
				}
			}
		}
		return $totalCustomOptionPrice;
	}
}