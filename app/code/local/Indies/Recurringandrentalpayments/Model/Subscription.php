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

class Indies_Recurringandrentalpayments_Model_Subscription extends Mage_Core_Model_Abstract
{
    /** Warning */
    const LOG_SEVERITY_WARNING = 4;

    const STATUS_ENABLED = 1;
    const STATUS_SUSPENDED = 2;
    const STATUS_CANCELED = -1;
    const STATUS_SUSPENDED_BY_CUSTOMER = 3;
    const STATUS_EXPIRED = 0;
    const STATUS_DISABLED = 0;

    const INTERNAL_DATE_FORMAT = 'yyyy-MM-dd'; // DON'T use Y(uppercase here)
    const DB_DATE_FORMAT = 'yyyy-MM-dd'; // DON'T use Y(uppercase here)
    const DB_DATETIME_FORMAT = 'yyyy-MM-dd H:m:s'; // DON'T use Y(uppercase here)

    const ITERATE_STATUS_REGISTRY_NAME = 'INDIES_RECURRINGANDRENTALPAYMENTS_PAYMENT_STATUS';
    const ITERATE_STATUS_RUNNING = 2;
    const ITERATE_STATUS_FINISHED = 12;

    protected $_product;
    protected $_virtualItems = array();
    protected static $_subscription;

    public static $_suspendTitle = 'You have suspended subscriptions!';

    protected function _construct()
    {
        $this->_init('recurringandrentalpayments/subscription');
    }

    /**
     * Determines if subscription Term is infinite
     * @return bool
     */
    public function isInfinite()
    {
        return $this->getTerm()->isInfinite();
    }


    /**
     * Says wether subscription is active
     * @return bool
     */
    public function isActive()
    {
		return $this->getStatus() == self::STATUS_ENABLED;
    }


    /**
     * Returns probably expire date
     * @return Zend_Date
     */
    public function getDateExpire()
    {
		if (!$this->getData('date_expire')) {
			if (!$this->isInfinite()) {
                foreach (Mage::getModel('recurringandrentalpayments/sequence')->getCollection()->addSubscriptionFilter($this)->setOrder('date', 'desc') as $SequenceItem) {

					$offset = $this->getTerm()->getPaymentBeforeDays();
				    $expirydate = new Zend_Date($SequenceItem->getDate(), self::DB_DATE_FORMAT);
                    
					Zend_Date::setOptions(array('extend_month' => true));
			
					switch ($this->getTerm()->getTermsper()) {
						case 'day':
							$method = 'addDayOfYear' ;
							break;
						case 'month':
							$method = 'addMonth';
							break;
						case 'week':
							$method = 'addWeek';
							break;
						case 'year':
							$method = 'addYear';
							break;
						default:
							throw new Mage_Core_Exception("Unknown subscription Term #" . $this->getTerm()->getId() . " for subscription #{$this->getId()}");
					}
      			  $expirydate = call_user_func(array($expirydate, $method), $this->getTerm()->getRepeateach());
	 
					return $expirydate;
				   // return $date->addDay($offset);
              	}
            }
			else
			{
                return $this->getNextSubscriptionEventDate(new Zend_Date);
            }
        }

        return new Zend_Date;
    }


    /**
     * Returns last paid date. Returns NULL if no payments made yet
     * @todo optimize that method
     * @return Zend_Date
     */
    public function getLastPaidDate()
    {
        foreach (Mage::getModel('recurringandrentalpayments/sequence')->getCollection()
                ->addStatusFilter(Indies_Recurringandrentalpayments_Model_Sequence::STATUS_PAYED)
                ->addSubscriptionFilter($this)->setOrder('date', 'desc')
            as $SequenceItem) {
			return new Zend_Date($SequenceItem->getDate(), self::DB_DATE_FORMAT);
        }
        return null;
    }

    /**
     * Returns subscription start date
     * @return Zend_Date
     */
    public function getDateStart()
    {
        return new Zend_Date($this->getData('date_start'), self::DB_DATE_FORMAT);
    }

    /**
     * Easy way to set customer as object
     * @param Mage_Customer_Model_Customer $Customer
     * @return Indies_Recurringandrentalpayments_Model_Subscription
     */
    public function setCustomer(Mage_Customer_Model_Customer $Customer)
    {
        $this->setCustomerId($Customer->getId());
        return $this;
    }

	/**
     * Returns current customer
     * @return Varien_Object
     */
    public function getCustomer()
    {
        if ($this->getCustomerId()) {
            return Mage::getModel('customer/customer')->load($this->getCustomerId());
        } else {
            $data = $this->getQuote()->getBillingAddress()->getData();
            $customer = new Varien_Object;
            foreach ($data as $k => $v) {
                $customer->setData($k, $v);
            }
            $customer->setName($customer->getFirstname() . ' ' . $customer->getLastname());
            return $customer;
        }
    }

    /**
     * Generates events for purchasing to subscription
     * @return Indies_Recurringandrentalpayments_Model_Subscription
     */
	protected function _generateSubscriptionEvents()
    {
		// Delete all sequencies
        if ($this->_origData['date_start'] != $this->_data['date_start'] ||
            $this->_origData['term_type'] != $this->_data['term_type'] ||
            (!$this->getIsNew() && $this->getIsReactivated())
        ) {
			//Mage::getResourceModel('recurringandrentalpayments/sequence')->deleteBySubscriptionId($this->getId());
        	$year = substr($this->getDateStart()->toString(self::DB_DATE_FORMAT),0,4) ;
			$month = substr($this->getDateStart()->toString(self::DB_DATE_FORMAT),5,2) ;
			$day = substr($this->getDateStart()->toString(self::DB_DATE_FORMAT),8,2) ;
			
			$datearray = array('year' => $year, 'month' => $month, 'day' => $day);
			$Date = new Zend_Date($datearray);
            
            switch ($this->getTerm()->getTermsper())
			{
                case 'day':
                    $method = 'addDayOfYear';
                    break;
                case 'month':
                    $method = 'addMonth';
                    break;
                case 'week':
                    $method = 'addWeek';
                    break;
                case 'year':
                    $method = 'addYear';
                    break;
                default:
                    throw new Mage_Core_Exception("Unknown subscription Term type for #" . $this->getTerm()->getId());
            }
            switch ($this->getTerm()->getTermsper()) 
			{
                case 'day':
                    $method_expire = 'addDayOfYear';
                    break;
                case 'month':
                    $method_expire = 'addMonth';
                    break;
                case 'week':
                    $method_expire = 'addWeek';
                    break;
                case 'year':
                    $method_expire = 'addYear';
                    break;
                default:
                    throw new Mage_Core_Exception("Unknown subscription expire Term type for #" . $this->getTerm()->getId());
            }
            $ExpireDate = clone $Date; 
            $expireMultiplier = $this->getTerm()->getNoofterms();
            if (!$expireMultiplier) {
                // 0 means infinite expiration date
                $expireMultiplier = 1;
            }
			else
			{
				$expireMultiplier =  ($expireMultiplier - 1) * $this->getTerm()->getRepeateach() ; 
			}
            $ExpireDate = call_user_func(array($ExpireDate, $method_expire), $expireMultiplier);
            // Substract delivery offset. This is
			
			$requirepayement = $this->getTerm()->getPaymentBeforeDays(); 
			$datefrm_paymentbeforedays = $Date->addDayOfYear(0 - $requirepayement);
            $ExpireDate->addDayOfYear(0 - $requirepayement);

            try 
			{
				$this->getTerm()->validate();
				// Create a new seq. if admin active suspended subscription for infinite term
				if($this->isInfinite() && !$this->getIsNew())
				{
					$today = new Zend_Date(Mage::getModel('core/date')->date('Y-m-d H:i:s'),self::DB_DATE_FORMAT);
					$final_today = clone $today ;
					$probably_next_date = $this->getNextSubscriptionEventDate($today);

					$is_exist_date = Mage::getModel('recurringandrentalpayments/sequence')->getCollection()
						 			  ->addFieldToFilter('subscription_id',$this->getId())
						 			  ->addFieldToFilter('date',array('gteq'=> $final_today->toString(self::DB_DATE_FORMAT)))
									  ->addFieldToFilter('date',array("lteq" => $probably_next_date->toString(self::DB_DATE_FORMAT)));
					 if(!sizeof($is_exist_date))
					 {
						 Mage::getModel('recurringandrentalpayments/subscription_flat')
					        ->load($this->getId(),'subscription_id')
					        ->setFlatNextPaymentDate($final_today->toString(self::DB_DATE_FORMAT))
                            ->save();
							
						 Mage::getModel('recurringandrentalpayments/sequence')
                            ->setSubscriptionId($this->getId())
                            ->setDate($final_today->toString(self::DB_DATE_FORMAT))
                            ->save();
					 }
						  
				}
				else
				{
					$i = 1;
					while ($Date->compare($ExpireDate) == -1) {

					/*start : when plan has a value of 'payment before days' that time control is going to IF cond */
						if($requirepayement > 0)
						{
							$year = substr($datefrm_paymentbeforedays->toString(self::DB_DATE_FORMAT),0,4) ;
							$month = substr($datefrm_paymentbeforedays->toString(self::DB_DATE_FORMAT),5,2) ;
							$day = substr($datefrm_paymentbeforedays->toString(self::DB_DATE_FORMAT),8,2) ;
						}
						else
						{
							$year = substr($this->getDateStart()->toString(self::DB_DATE_FORMAT),0,4) ;
							$month = substr($this->getDateStart()->toString(self::DB_DATE_FORMAT),5,2) ;
							$day = substr($this->getDateStart()->toString(self::DB_DATE_FORMAT),8,2) ;
						}
					/*end : when plan has a value of 'payment before days' that time control is going to IF cond */	

						$datearray = array('year' => $year, 'month' => $month, 'day' => $day);
						$Date = new Zend_Date($datearray);
						switch ($this->getTerm()->getTermsper()) {
							case 'day':
								$Date->addDay($i * $this->getTerm()->getRepeateach());
								break;
							case 'month':
								$Date->addMonth($i * $this->getTerm()->getRepeateach());
								break;
							case 'week':
								$Date->addWeek($i * $this->getTerm()->getRepeateach());	
								break;
							case 'year':
								$Date->addYear($i * $this->getTerm()->getRepeateach());	
								break;
							default:
								throw new Mage_Core_Exception("Unknown subscription Term type for #" . $this->getTerm()->getId());
						}
						Mage::getModel('recurringandrentalpayments/sequence')
								->setSubscriptionId($this->getId())
								->setDate($Date->toString(self::DB_DATE_FORMAT))
								->save();
						$i++;
					}
				}
            } catch (Indies_Recurringandrentalpayments_Exception $e) {
                Mage::log(
                    'Unable create sequences to subscription #' . $this->getId(),
                    self::LOG_SEVERITY_WARNING,
                    'Unable create sequences to subscription. Message: "' . $e->getMessage() . '"'
                );
            }
        }
        return $this;
    }
	
    /**
     * Generates next payment date
     * @param Zend_Date $CurrentDate
     * @return Zend_Date
     */
    public function getNextSubscriptionEventDate($CurrentDate = null)
    {		
		Zend_Date::setOptions(array('extend_month' => true)); // Fix Zend_Date::addMonth unexpected result
        if (!($CurrentDate instanceof Zend_Date)) {
            if (is_null($CurrentDate)) {
                if (!($CurrentDate = $this->getLastPaidDate())) {
                    throw new Indies_Recurringandrentalpayments_Exception("Failed to detect last paid date");
                }
            } else {
                throw new Indies_Recurringandrentalpayments_Exception("getNextSubscriptionEventDate accepts only Zend_Date or null");
            }
        }
       Zend_Date::setOptions(array('extend_month' => true));
        switch ($this->getTerm()->getTermsper()) {
            case 'day':
                $method = 'addDayOfYear';
                break;
            case 'month':
                $method = 'addMonth';
                break;
            case 'week':
                $method = 'addWeek';
                break;
            case 'year':
                $method = 'addYear';
                break;
            default:
                throw new Mage_Core_Exception("Unknown subscription Term #" . $this->getTerm()->getId() . " for subscription #{$this->getId()}");
        }

        $CurrentDate = call_user_func(array($CurrentDate, $method), $this->getTerm()->getRepeateach());
	    return $CurrentDate;
    }

    /**
     * Re-generates subscription events
     * @return Indies_Recurringandrentalpayments_Model_Subscription
     */
    protected function _generateAlertEvents()
    {
		$Alert = Mage::getModel('recurringandrentalpayments/alert');
		$Alert->generateSubscriptionEvents($this);
        return $this;
    }

    /**
     * After save handler
     * @return
     */
    public function _afterSave()
    {
		if (!empty($this->_virtualItems)) {
            // Save virtual items
			foreach ($this->_virtualItems as $Item) {
                $Item->setSubscriptionId($this->getId())->save();
            }
        }
	  	if (!$this->getFlagNoSequenceUpdate()) {
            $this->_generateSubscriptionEvents();
		} // test case 156 reactive order*/
		
        $flat = Mage::getModel('recurringandrentalpayments/subscription_flat')->load($this->getId(),'subscription_id')->setSubscription($this)->save();
        return parent::_afterSave();
    }

    /**
     * Returns Term for subscription
     * @return Indies_Recurringandrentalpayments_Model_Term
     */
    public function getTerm()
    {
		if (!$this->getData('terms')) {
            $this->setTerms(Mage::getModel('recurringandrentalpayments/terms')->load($this->getTermType()));
        }
        return $this->getData('terms');
    }

    /**
     * Returns affected subscription items
     * @return Indies_Recurringandrentalpayments_Model_Mysql4_Subscription_Item_Collection
     */
    public function getItems()
    {
        if (!$this->getData('items')) {
            $this->setItems(Mage::getModel('recurringandrentalpayments/subscription_item')->getCollection()->addSubscriptionFilter($this));
        }
        return $this->getData('items');
    }

    /**
     * Initiates subscription items from order items
     * @param object $OrderItems
     * @return Indies_Recurringandrentalpayments_Model_Subscription
     */
    public function initFromOrderItems($OrderItems, Mage_Sales_Model_Order $Order)
    {		
		$this->_virtualItems = array();
        foreach ($OrderItems as $OrderItem) {
		$this->_virtualItems[] = Mage::getModel('recurringandrentalpayments/subscription_item')
                    ->setPrimaryOrderId($Order->getId())
                    ->setPrimaryOrderItemId($OrderItem->getId());
					
        }
        return $this;
    }

    /**
     * Returns primary order
     * @return Mage_Sales_Model_Order
     */
    public function getOrder()
    {
        if (!$this->getData('order')) {
            foreach ($this->getItems() as $Item) {
                $this->setOrder(Mage::getModel('sales/order')->load($Item->getPrimaryOrderId()));
                break;
            }

        }
        return $this->getData('order');
    }

    /**
     * Returns last created order
     * @return Mage_Sales_Model_Order
     */
    public function getLastOrder()
    {
        if (!$this->getData('last_order')) {
            $coll = Mage::getModel('recurringandrentalpayments/sequence')
                    ->getCollection()
                    ->addSubscriptionFilter($this)
                    ->addStatusFilter(Indies_Recurringandrentalpayments_Model_Sequence::STATUS_PAYED)
                    ->setOrder('date', Varien_Data_Collection::SORT_ORDER_ASC);

            foreach ($coll as $SequenceItem) {
                if (!$SequenceItem->getOrderId()) {
                    throw new Indies_Recurringandrentalpayments_Exception("Subscription record marked as paid but no order found: #{$SequenceItem->getId()}, suscription #{$SequenceItem->getSubscriptionId()}");
                }
                $orderId = $SequenceItem->getOrderId();
            }
            if (isset($orderId)) {
                $order = Mage::getModel('sales/order')->load($orderId);
            } else {
                $order = $this->getOrder(); // Primary order
            }
            $this->setData('last_order', $order);
        }
        return $this->getData('last_order');
    }

    /**
     * Returns primary order
     * @return Mage_Sales_Model_Quote
     */
    public function getQuote()
    {
		if (!$this->getData('quote')) {
            $this->setQuote(Mage::getModel('sales/quote')->setStoreId($this->getStoreId())->load($this->getPrimaryQuoteId()));
        }
        return $this->getData('quote');
    }

    /**
     * Creates new order for subscription
     * @return Mage_Sales_Model_Order
     */
    public function createOrder()
    {
		foreach ($this->getItems() as $Item) {
            $order = Mage::getModel('sales/order')->load($Item->getPrimaryOrderId());
            break;
        }
        $items = $this->getItems()->getOrderItems();
        $order->setReordered(true);
        $quote = $this->getQuote()->setUpdatedAt(now());
        $quoteCurrency = Mage::getModel('directory/currency')->load($quote->getQuoteCurrencyCode());
        $quote->setForcedCurrency($quoteCurrency);
        $quote->save();
        $no = Mage::getSingleton('recurringandrentalpayments/order_create');
        $no
                ->reset()
                ->setRecollect(1)
                ->setPrimaryQuote($quote);
        $no->getSession()->setUseOldShippingMethod(true);
        $no->setSendConfirmation(true);
        $no->setData('account', $this->getCustomer()->getData());
        foreach ($this->getItems() as $Item) {
            $arr[] = $Item->getPrimaryOrderItemId();
        }
        $no->setItemIdFilter($arr);
 		try
		{
			$no->initFromOrder($order ,$this->getTermType(),$this->getId());
		}
		catch (Exception $e)
		{
			$this->setResponsemessage($e->getMessage());
			Mage::throwException($e->getMessage());
		}		
        //fix for guest subscription
		$customer = $this->getGuestByEmail($order->getCustomerEmail());
	    if (!$order->getCustomerId()) {
            if (!$customer->getId()) {
                $no->getQuote()->getCustomer()
                                     ->setEmail($order->getCustomerEmail())
                                     ->setFirstname($order->getCustomerFirstname())
                                     ->setLastname($order->getCustomerLastname());
            }
            elseif (!$order->getCustomerId())
                $no->getQuote()->setCustomer($customer);
        }
        else
            $no->getQuote()->setCustomer($customer);

        $discount_amount = 0 ;
		$amount = 0 ;
		$data = Mage::getModel('recurringandrentalpayments/subscription')->load($this->getId());
		$amount = $data->getDiscountAmount();
		$apply_discount_on = $data->getApplyDiscountOn();

		if($apply_discount_on != 2)
		{
			if(strpos($amount,'%') === false)
			{
				$discount_amount = 	$amount * count($items) ;
			}
			else
			{
				$discount_amount = 0 ;
				foreach($this->getItems()->getOrderItems() as $item)
				{
					$discount_amount = $discount_amount  + ($item->getPrice() * $amount)/100 ;
				}
			}
		}
		// Apply quote changes to order
		
		/* Start : 2015-05-06 : This will add discount amount to order while capturing sequecne  */
		if ($discount_amount > 0)
		{
			$quote = $no->getQuote();
			foreach ($quote->getAllAddresses() as $address) 
			{
	  			$address->collectTotals();
				$quote->setSubtotal((float) $quote->getSubtotal() + $address->getSubtotal());
				$quote->setBaseSubtotal((float) $quote->getBaseSubtotal() + $address->getBaseSubtotal());

				$quote->setSubtotalWithDiscount(
					(float) $quote->getSubtotalWithDiscount() + $address->getSubtotalWithDiscount()
				);
				$quote->setBaseSubtotalWithDiscount(
					(float) $quote->getBaseSubtotalWithDiscount() + $address->getBaseSubtotalWithDiscount()
				);

				$quote->setGrandTotal((float) $quote->getGrandTotal() + $address->getGrandTotal());
				$quote->setBaseGrandTotal((float) $quote->getBaseGrandTotal() + $address->getBaseGrandTotal());
	
				$quote ->save(); 
	
				$quote->setGrandTotal($quote->getSubtotal() - $discount_amount + $order->getDiscountAmount())
				->setBaseGrandTotal($quote->getBaseSubtotal() - $discount_amount + $order->getDiscountAmount())
				->setSubtotalWithDiscount($quote->getBaseSubtotal())
				->setBaseSubtotalWithDiscount($quote->getBaseSubtotal())
				->save(); 
						
				if($order->getDiscountAmount()!=0)
				{	
					 $address->setDiscountAmount($order->getDiscountAmount());
					 $address->setBaseDiscountAmount($order->getDiscountAmount());
				} 
				$address->setGrandTotal((float) $address->getSubtotal() + $address->getTaxAmount() + $address->getShippingAmount()  - $discount_amount + $order->getDiscountAmount());
				$address->setBaseSubtotalWithDiscount((float) $address->getBaseSubtotalWithDiscount() + $order->getDiscountAmount());
				$address->setBaseGrandTotal((float) $address->getBaseSubtotal() + $address->getBaseTaxAmount() + $address->getBaseShippingAmount() - $discount_amount + $order->getDiscountAmount());

				$address->setRecurringDiscountAmount(-($discount_amount));
				$address->setDiscountDescription('Discount');
				$address->setBaseRecurringDiscountAmount(-($discount_amount));

				$address->save();  
		    } //end: foreach
		}
		try
		{
			$or = $no->createOrder();
			$or->save();
			$this->setResponsemessage('Success');
			if(Mage::getStoreConfig(Indies_Recurringandrentalpayments_Helper_Config::XML_PATH_NEXT_PAYMNET_CONFORMATION, Mage::app()->getStore()) == '1')
			{
				$alert = Mage::getModel('recurringandrentalpayments/alert_event');
				$alert->send($this,
							Mage::getStoreConfig(Indies_Recurringandrentalpayments_Helper_Config::XML_PATH_NEXT_PAYMNET_CONFORMATION_TEMPLATE),
							 0, 
							 Mage::getStoreConfig(Indies_Recurringandrentalpayments_Helper_Config::XML_PATH_NEXT_PAYMNET_CONFORMATION_SENDER));	
			}	
		}
		catch (Exception $e)
		{
			$or = NULL;
			if (!$this->getResponsemessage())
			$this->setResponsemessage($e->getMessage());
		}
		$no->getQuote()->removeAllItems();

        //clean base from fake guest records
        if (!$order->getCustomerId() && !$customer->getId()) {
            Mage::register('isSecureArea', true);
            $this->deleteFakeGuestsByEmail($order->getCustomerEmail());
        }
		
		
        return $or;
    }

    /**
     *
     * Get customer by email
     * @param string $email
     *
     * native magento method loadByEmail don't work with customer which webside id = 0
     */

    public function getGuestByEmail($email)
    {
		$resource = Mage::getSingleton('core/resource');
        $db = $resource->getConnection('core_read');
        $select = $db->select()->from($resource->getTableName('customer/entity'), array('entity_id'))->where('email = ?', $email);
        $id = $db->fetchOne($select);
        return Mage::getModel('customer/customer')->load($id);
    }

    public function deleteFakeGuestsByEmail($email)
    {
        $resource = Mage::getSingleton('core/resource');
        $db = $resource->getConnection('core_read');
        $select = $db->select()
                ->from($resource->getTableName('customer/entity'), array('entity_id'))
                ->where('email = ?', $email);
        $id = $db->fetchOne($select);
        if ($id)
            Mage::getModel('customer/customer')->load($id)->delete();
			
    }

    /**
     * Processes payment.
     * @param object $Order
     * @return Indies_Recurringandrentalpayments_Model_Subscription
     */
    public function processPayment($Order)
    {
        try {
            $pm_code = $this->getOrder()->getPayment()->getMethod();
            $PaymentInstance = $this->_getMethodInstance($pm_code);
            $PaymentInstance
                    ->setSubscription($this)
                    ->processOrder($this->getOrder(), $Order);
        } catch (Exception $e) 
		{
            throw new Indies_Recurringandrentalpayments_Exception("Payment message: #{$this->getId()}: {$e->getMessage()}");
        }
        return $this;
    }

    /**
     * Creates order and runs payment for it
     * @return bool|object
     */
    protected function _iterate()
    {
	try {
            Mage::unregister(self::ITERATE_STATUS_REGISTRY_NAME);
            Mage::register(self::ITERATE_STATUS_REGISTRY_NAME, self::ITERATE_STATUS_RUNNING);
	   		$newOrder = $this->createOrder();
         
            if ($this->getOrder()->getPayment()->getMethod() == 'epay_standard' ) $this->processPayment($newOrder);
            $this->setData('last_order', $newOrder);
            $this->setStatus(self::STATUS_ENABLED)->save();
          
            Mage::unregister(self::ITERATE_STATUS_REGISTRY_NAME);
            Mage::register(self::ITERATE_STATUS_REGISTRY_NAME, self::ITERATE_STATUS_FINISHED);
            return $newOrder; 
        } catch (Mage_Core_Exception $e) {
            // Something goes wrong. Suspend subscription.
            Mage::unregister(self::ITERATE_STATUS_REGISTRY_NAME);
            Mage::register(self::ITERATE_STATUS_REGISTRY_NAME, self::ITERATE_STATUS_FINISHED);
            return false;
        }
    }

    /**
     * Processes subscription by sequence
     * @param object $Item
     * @return bool
     */
    public function payBySequence($Item)
    {
	    self::$_subscription = $this;
        if (!$this->_getMethodInstance()->isValidForTransaction($Item)) {
            return false;
        }
        Mage::getSingleton('recurringandrentalpayments/subscription')->setId($this->getId());
        $Order = $this->_iterate();
		if ($Order instanceof Mage_Sales_Model_Order)
		{
			$Item->setTransactionStatus($this->getResponsemessage());
            switch ($Order->getStatus())
            {	
                case Mage_Sales_Model_Order::STATE_COMPLETE:
                    $Item->setStatus(Indies_Recurringandrentalpayments_Model_Sequence::STATUS_PAYED);
                    break;
                case Mage_Sales_Model_Order::STATE_CANCELED:
                    $Item->setStatus(Indies_Recurringandrentalpayments_Model_Sequence::STATUS_FAILED);
                    break;
                default:
                    $Item->setStatus(Indies_Recurringandrentalpayments_Model_Sequence::STATUS_PENDING_PAYMENT);
            }

            if (Mage::helper('recurringandrentalpayments')->isOrderStatusValidForActivation($Order)) 
			{
               $Item->setStatus(Indies_Recurringandrentalpayments_Model_Sequence::STATUS_PAYED);
            } 
			else
            {
                $setStatusSuspended = 1;
            }
            $date = Mage::app()->getLocale()->date(new Zend_Date);
            $Item->setOrderId($Order->getId())->save();
        } 
		else
        {
			$Item->setTransactionStatus($this->getResponsemessage());
            $Item->setStatus(Indies_Recurringandrentalpayments_Model_Sequence::STATUS_FAILED);
			$Item->save();
			$setStatusSuspended = 1;
        }


        if (isset($setStatusSuspended) && $setStatusSuspended) {
            $this->setStatus(self::STATUS_SUSPENDED)->setFlagNoSequenceUpdate(1)->save()->setFlagNoSequenceUpdate(0);
        }
		
		$pDate = new Zend_Date($Item->getDate(), self::DB_DATE_FORMAT);
		
		$nextPaymentDate = $this->getNextSubscriptionEventDate($pDate);
		
		$paymentOffset = $this->getTerm()->getPaymentBeforeDays();
		if($paymentOffset > 0)
			$nextPaymentDate->addDayOfYear(0 - floatval($paymentOffset));
		
		$collection = Mage::getModel('recurringandrentalpayments/subscription_flat')->load($this->getId(),'subscription_id');
		$collection->setFlatNextPaymentDate($nextPaymentDate)->save();
        return true;
      }

    /**
     * Processes payment for date
     * @param object $date
     * @return Indies_Recurringandrentalpayments_Model_Subscription
     */
    public function payForDate($date)
    {
		$this->updateSequences();
        $date = Mage::app()->getLocale()->date($date);

        $sequenceItems = Mage::getModel('recurringandrentalpayments/sequence')->getCollection()
                ->addSubscriptionFilter($this)
                ->prepareForPayment()
                ->addDateFilter($date);

        foreach ($sequenceItems as $Item)
        {
            if (!$this->_getMethodInstance()->isValidForTransaction($Item)) {
                continue;
            }
            $this->payBySequence($Item);
            break;
        }
        return $this;
    }
	/**
 * Processes payment for old failed/pending seq.
 */
	
   public function paySeq($seq_id)
   {
	   $this->updateSequences();
	   $Item = Mage::getModel('recurringandrentalpayments/sequence')->load($seq_id);
	   if (!$this->_getMethodInstance()->isValidForTransaction($Item))
	   {
			return;
       }
	   else
	   {
        	$this->payBySequence($Item);
	   }
	    return $this ;
   }

    public function updateSequences()
    {
        //checks if this is a last item of the infinite subscription
	    if ($this->isInfinite()) {
            $coll = Mage::getModel('recurringandrentalpayments/sequence')->getCollection()
                    ->addSubscriptionFilter($this)
                    ->addStatusFilter(Indies_Recurringandrentalpayments_Model_Sequence::STATUS_PENDING);
            if ($coll->count() == 1) //this is a last pending sequence, we need a new one
            {
				$_seq = $coll->getFirstItem();
                $_aDate = new Zend_Date($_seq->getDate(), Indies_Recurringandrentalpayments_Model_Subscription::DB_DATE_FORMAT);
                $nextDate = $this->getNextSubscriptionEventDate($_aDate);
               
			    $newSeq = Mage::getModel('recurringandrentalpayments/sequence')
                        ->setSubscriptionId($this->getId())
                        ->setstatus(Indies_Recurringandrentalpayments_Model_Sequence::STATUS_PENDING)
                        ->setDate($nextDate->toString(Indies_Recurringandrentalpayments_Model_Subscription::DB_DATE_FORMAT))
                        ->save();
            }
        }
    }

    /**
     * Returns payment model instance by code
     * @param string $method
     * @throws Mage_Core_Exception
     * @return object
     */
    protected function _getMethodInstance($method = null)
    {
        if (!$method && $this->getOrder()) {
            try {
                $method = $this->getOrder()->getPayment()->getMethod();
            } catch (Exception $e) {
            }
        }
        if ($model = Mage::getModel('recurringandrentalpayments/payment_method_' . $method)) {
            return $model->setSubscription($this);
        } else {
            throw new Indies_Recurringandrentalpayments_Exception(Mage::helper('recurringandrentalpayments')->__("Can't find implementation of payment method $method"));
        }
    }

    /**
     * Returns payment model instance by code
     * @param string $method
     * @throws Mage_Core_Exception
     * @return object
     */
    public function getMethodInstance($method)
    {
        return $this->_getMethodInstance($method);
    }

    /**
     * Check if payment method exists
     * @param string $method
     * @return bool
     */
    public function hasMethodInstance($method)
    {
        try
        {
		@$methodAvailable = Mage::getModel('recurringandrentalpayments/payment_method_' . $method);
        }
        catch (Exception $ex)
        {
            return false;
        }

        return $methodAvailable;
    }

    /**
     * Determines wether subscription is set to canceled
     * @return bool
     */
    public function getIsCancelling()
    {
        return (($this->_origData['status'] != $this->_data['status']) && ($this->_data['status'] == self::STATUS_CANCELED));
    }

    /**
     * Determines wether subscription is set to expired
     * @return bool
     */
    public function getIsExpiring()
    {
        return (($this->_origData['status'] != $this->_data['status']) && ($this->_data['status'] == self::STATUS_EXPIRED));
    }

    /**
     * Determines wether subscription is set to not active
     * @return bool
     */
    public function getIsStopping()
    {
		return (($this->_origData['status'] != $this->_data['status']) && ($this->_data['status'] != self::STATUS_ENABLED));
    }

    /**
     * Determines wether subscription is set to suspended
     * @return bool
     */
    public function getIsSuspending()
    {
        return (($this->_origData['status'] != $this->_data['status']) && ($this->_data['status'] == self::STATUS_SUSPENDED || $this->_data['status'] == self::STATUS_SUSPENDED_BY_CUSTOMER));
    }

    /**
     * Determines wether subscription is set back to active
     * @return bool
     */
    public function getIsReactivated()
    {
       return (($this->_origData['status'] != $this->_data['status']) && ($this->_data['status'] == self::STATUS_ENABLED));
    }

    /**
     * Cancels current subscription
     * @return Indies_Recurringandrentalpayments_Model_Subscription
     */
    public function cancel()
    {
        // Set canceled status
        return $this->setStatus(self::STATUS_CANCELED);
    }


    /**
     * Prepares for save
     * @return
     */
    protected function _beforeSave()
    {
		
		if (!$this->getId()) {
            $this->getQuote()->setUpdatedAt(now())->save();
            $this->setIsNew(true);
        }
        if (is_null($this->getStoreId())) {
            $storeId = (Mage::getSingleton('adminhtml/session_quote')->getStoreId())
                    ? Mage::getSingleton('adminhtml/session_quote')->getStoreId() : Mage::app()->getStore()->getId();
            $this->setStoreId($storeId);
        }
		
        if ($this->getIsCancelling() && !$this->getIsNew()) {
            Mage::dispatchEvent('recurringandrentalpayments_subscription_cancel_before', array('subscription' => $this));
            // Delete payment sequence
            Mage::getResourceModel('recurringandrentalpayments/sequence')->deleteBySubscriptionId($this->getId());
            // Throw payment onCancel
            $this->_getMethodInstance()->onSubscriptionCancel($this);
        }

        if ($this->getIsSuspending() && !$this->getIsNew()) {
            Mage::dispatchEvent('recurringandrentalpayments_subscription_suspend_before', array('subscription' => $this));
            $this->_getMethodInstance()->onSubscriptionSuspend($this);
        }
		
        if ($this->getIsReactivated() && !$this->getIsNew()) {
            Mage::dispatchEvent('recurringandrentalpayments_subscription_reactivate_before', array('subscription' => $this));
            $this->_getMethodInstance()->onSubscriptionReactivate($this);
        }
        if ($this->getIsExpiring() && !$this->getIsNew()) {
            Mage::dispatchEvent('recurringandrentalpayments_subscription_expire_before', array('subscription' => $this));
            $this->_getMethodInstance()->onSubscriptionSuspend($this);
        }

        return parent::_beforeSave();
    }


    public static function isIterating()
    {
        return Mage::registry(self::ITERATE_STATUS_REGISTRY_NAME) == self::ITERATE_STATUS_RUNNING;
    }

    /**
     * Returns subscription status for order
     * @param Mage_Sales_Model_Order $Order
     * @return Indies_Recurringandrentalpayments_Model_Subscription
     */
    public function getStatusByOrder(Mage_Sales_Model_Order $Order)
    {

    }

    /**
     * Returns customer URL
     * @return string
     */
    public function getCustomerUrl()
    {
        return Mage::getUrl('recurringandrentalpayments/customer/view', array('id' => $this->getId()));
    }
	
	public function getCustomerGridUrl()
	{
		return Mage::getUrl('recurringandrentalpayments/customer/');
	}
    /**
     * Returns admin URL
     * @return string
     */
    public function getAdminUrl()
    {
        return Mage::getSingleton('adminhtml/url')->getUrl('recurringandrentalpayments_admin/subscriptions/edit', array('id' => $this->getId()));
    }

    /**
     * Return current iterating subscription
     * @static
     * @return Indies_Recurringandrentalpayments_Model_Subscription
     */
    public static function getInstance()
    {
        return self::$_subscription;
    }

    public function issetSuspendedSubscription()
    {
        $collection = $this->getCollection();
        $collection->getSelect()->where('status = ?', self::STATUS_SUSPENDED);
        return (count($collection->getItems()) > 0);
    }
			public function getNextDateRemind()
	{
		$reminderBeforeDays = Mage::getStoreConfig(Indies_Recurringandrentalpayments_Helper_Config::XML_PATH_NEXT_PAYMNET_REMINDER_BEFORE_DAYS);
		$Ndate = Mage::getModel('core/date')->date('Y-m-d');
		$Ndate = strtotime($Ndate) + 86400*$reminderBeforeDays;		
		return date('Y-m-d', $Ndate);
	}
	
	public function nextdateonconform()
	{
		$id= $this->getId();
		$collection = Mage::getModel('recurringandrentalpayments/sequence')->getCollection()->addFieldToFilter('subscription_id', $id)->addFieldToFilter('status','pending')->getFirstItem();
		return $collection->getDate();
	}
	public function getloginUrl()
	{
		
		return Mage::getUrl('customer/account/login');
}
	public function getExpDate()
	{
		$ExpreminderBeforeDays = Mage::getStoreConfig(Indies_Recurringandrentalpayments_Helper_Config::XML_PATH_EXPIRY_REMINDER_BEFORE_DAYS);
		$Ndate = Mage::getModel('core/date')->date('Y-m-d');
		$Ndate = strtotime($Ndate) + 86400*$ExpreminderBeforeDays;		
		return date('Y-m-d', $Ndate);		
	}
	public function getpaymentmethod()
	{
		return $this->getOrder()->getPayment()->getMethodInstance()->getTitle();
		}
		
	public function getpaymentcurrency()
	{
		return Mage::app()->getStore()->getBaseCurrencyCode();
	}
	public function getlabel ()
	{
		$terms = $this->getTerms();
		return Mage::helper('recurringandrentalpayments')->__($terms->getLabel());	
	}
	public function nextdate()
	{	
			$id = $this->getId();
			$subscription = Mage::getModel('recurringandrentalpayments/subscription')->load($id);
			return $subscription->getData('flat_next_payment_date');			 
	}
	public function planname()
	{	

			$id= $this->getOrder()->getData('quote_id');
   			$termid = Mage::getModel('recurringandrentalpayments/subscription')->load($id,'primary_quote_id')->getTermType();
			$term=Mage::getModel('recurringandrentalpayments/terms')->load($termid)->getPlanId();
   			$plan = Mage::getModel('recurringandrentalpayments/plans')->load($term)->getData();
			return $plan['plan_name'];		
	}
		
	public function enddate()
	{
			$id= $this->getOrder()->getData('quote_id');
			$subscription = Mage::getModel('recurringandrentalpayments/subscription')->load($id,'primary_quote_id');
			$expirt_date = $subscription->getFlatDateExpire();
			if($subscription->getFlatDateExpire() == 0)
			{
				$expirt_date = '  -';
			}
			return $expirt_date;						
	}
	public function substart()
	{	
			$id = $this->getId();
			$subscription = Mage::getModel('recurringandrentalpayments/subscription')->load($id);
			return $subscription->getData('date_start');			 
	}
	public function parentorderid()
	{
			$orderid = Mage::getModel('recurringandrentalpayments/subscription_flat')->load($this->getId(),'subscription_id')->getParentOrderId();
			return $orderid;						
	}
	public function getOrderedItems()
	{
		$items = Mage::getModel('recurringandrentalpayments/subscription_item')->getCollection()
					->addFieldToFilter('subscription_id',$this->getId());
		$string = array();
		$products = '';
		foreach ($items as $item)
		{
			$data = Mage::getModel('sales/order_item')->load($item->getPrimaryOrderItemId());
			$prodOptions = $data->getProductOptions();
			
			$sub_type = $prodOptions['info_buyRequest']['indies_recurringandrentalpayments_subscription_type'];
			if(isset($sub_type) && $sub_type > 0)
			{
					$string[] = $data->getName();
			}
		}
		if (count($string)== 1)
		{
			 $products = implode('',$string);	
		}
		else
		{
			$products = implode(',', $string);
		}
		return $products;		
	}
	
	public function getSubscriptionStatusLabel()
    {
        return Mage::getModel('recurringandrentalpayments/source_subscription_status')->getLabel($this->getStatus());
    }
	
	public function getOrderItemsPrice()
	{
		$items = Mage::getModel('recurringandrentalpayments/subscription_item')->getCollection()
					->addFieldToFilter('subscription_id',$this->getId());
		$string = array();
		$price = '';
		foreach ($items as $item)
		{
			$data = Mage::getModel('sales/order_item')->load($item->getPrimaryOrderItemId());
			$prodOptions = $data->getProductOptions();
			$sub_type = $prodOptions['info_buyRequest']['indies_recurringandrentalpayments_subscription_type'];
			if(isset($sub_type) && $sub_type > 0)
			{
					$string[] = $this->getCurrencySymbol().number_format($data->getPrice(),2);
			}
		}
		if (count($string)== 1)
		{
			 $price = implode('',$string);	
		}
		else
		{
			$price = implode(',', $string);
		}
		return $price;	
	}
	
	public function getCurrencySymbol($currency_code=NULL)
	{
		if($currency_code!=NULL)
		{
			$currency_symbol = Mage::app()->getLocale()->currency($currency_code)->getSymbol();
		}
		else
		{
			$currency_symbol = Mage::app()->getLocale()->currency(Mage::app()->getStore()->getCurrentCurrencyCode())->getSymbol();
		}
		if($currency_symbol==NULL)
			$currency_symbol = Mage::app()->getStore()->getCurrentCurrencyCode();
		return $currency_symbol;
	}
}
