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

class Indies_Recurringandrentalpayments_Model_Payment_Method_Verisign extends Mage_Paypal_Model_Payflowpro
{
	const XML_PATH_PP_NEW_ORDER_STATUS = 'payment/paypal_direct/order_status';
	const WEB_SERVICE_MODEL = 'recurringandrentalpayments/web_service_Verisign_client';
	const DATE_FORMAT = 'yyyy-MM-dd';
	
	const SUBSC_STATUS_ACTIVE = "ACTIVE";
    const SUBSC_STATUS_VENDOR_INACTIVE = "VENDOR INACTIVE";
    const SUBSC_STATUS_DEACTIVATED_BY_MERCHANT = "DEACTIVATED BY MERCHANT";
    const SUBSC_STATUS_TOO_MANY_FAILURES = "TOO MANY FAILURES";
    const SUBSC_STATUS_EXPIRED = "EXPIRED";

	public function __construct()
    {
        $this->_initWebService();
    }
	protected function _initWebService()
    {
        $service = Mage::getModel(self::WEB_SERVICE_MODEL);      
        $this->setWebService($service);
        return $this;
    }
	
	/**
     * Retrieves info about subscription
     * @param Indies_Recurringandrentalpayments_Model_Subscription $Subscription
     * @return array
     */
    public function getSubscriptionDetails(Indies_Recurringandrentalpayments_Model_Subscription $Subscription,$order)
    {
        if (!$this->getData('subscription_details')) {
			$getSession = Mage::getSingleton('core/session')->getapplyDiscountAmount(); 
			$amount  = $order->getGrandTotal();
			$orderIncrementId = $order->getIncrementId();
			if (isset($getSession))
			{
				$amount = 	$amount - $getSession ;
			}
			$action = Mage::getStoreconfig('payment/verisign/payment_action');
			if($action == 'Authorization')
				$action = self::TRXTYPE_AUTH_ONLY;
			else
				$action = self::TRXTYPE_SALE;
	
			$result = array();
			$request = $this->getWebService()->buildBasicRequest();
			$request->setTrxtype($action);
			$request->setTender(self::TENDER_CC);
			$request->setOrigid($Subscription->getPayflowPnrefId());
			$request->setAmt($amount);
			$request->setComment1($orderIncrementId);
			$request->setCustref($order->getCustomerId());
			$request->setPonum($order->getId());
			$response = $this->getWebService()->postRequest($request);
			$result = $response->getData();
            $this->setData('subscription_details', $result);
        }
        return $this->getData('subscription_details');
    }

    /**
     * This function is run when subscription is created and new order creates
     * @param Indies_Recurringandrentalpayments_Model_Subscription $Subscription
     * @param Mage_Sales_Model_Order     $Order
     * @param Mage_Sales_Model_Quote     $Quote
     * @return Indies_Recurringandrentalpayments_Model_Payment_Method_Paypal_Direct
     */
    public function onSubscriptionCreate(Indies_Recurringandrentalpayments_Model_Subscription $Subscription, Mage_Sales_Model_Order $Order, Mage_Sales_Model_Quote $Quote)
    {
        $this->createSubscription($Subscription, $Order, $Quote);
        return $this;
    }

    /**
     * Cancels subscription at paypal
     * @param Indies_Recurringandrentalpayments_Model_Subscription $Subscription
     * @return Indies_Recurringandrentalpayments_Model_Payment_Method_Paypal_Direct
     */
    public function onSubscriptionCancel(Indies_Recurringandrentalpayments_Model_Subscription $Subscription)
    {
         return $this;
    }

    /**
     * Cancels subscription at paypal
     * @param Indies_Recurringandrentalpayments_Model_Subscription $Subscription
     * @return Indies_Recurringandrentalpayments_Model_Payment_Method_Paypal_Direct
     */
    public function onSubscriptionSuspend(Indies_Recurringandrentalpayments_Model_Subscription $Subscription)
    {
         return $this;
    }

    /**
     * Cancels subscription at paypal
     * @param Indies_Recurringandrentalpayments_Model_Subscription $Subscription
     * @return Indies_Recurringandrentalpayments_Model_Payment_Method_Paypal_Direct
     */
    public function onSubscriptionReactivate(Indies_Recurringandrentalpayments_Model_Subscription $Subscription)
    {
         return $this;
    }

    /**
     * Checks if payment method can perform transaction now
     * @return bool
     */
    public function isValidForTransaction(Indies_Recurringandrentalpayments_Model_Sequence $Sequence)
    {
		 return true;
    }

    /**
     * Processes payment for specified order
     * @param Mage_Sales_Model_Order $Order
     * @return
     */
    public function processOrder(Mage_Sales_Model_Order $PrimaryOrder, Mage_Sales_Model_Order $Order = null)
    {
		$Subscription = $this->getSubscription();

		$sequence = Mage::getModel('recurringandrentalpayments/sequence')->load($Subscription->getSequenceId());
		$response = $this->getSubscriptionDetails($Subscription,$Order);
		if(isset($response['result_code']))
		{
			$pnrefid = $response['pnref'];
			
			Zend_Date::setOptions(array('extend_month' => true)); // Fix Zend_Date::addMonth unexpected result
			$magento_date = (Mage::getModel('core/date')->date('Y-m-d'));
			$pnref_expireDate = date('Y-m-d', strtotime("+1 years", strtotime($magento_date)));

			if($response['result_code'] == self::RESPONSE_CODE_APPROVED)
			{
				$Subscription
				->setPayflowPnrefId($pnrefid)	
				->setPnrefExpiryDate($pnref_expireDate)
				->save();
				$Order->getPayment()->setTransactionId($pnrefid)->save();    // Save pnref id in sales_flat_order_payment
				$Subscription->setResponsemessage('Success');

			}
			else if($response['result_code'] == self::RESPONSE_CODE_FRAUDSERVICE_FILTER)
			{
				$Subscription
				->setPayflowPnrefId($pnrefid)	
				->setPnrefExpiryDate($pnref_expiry_date)
				->save();
				$Order->getPayment()->setTransactionId($pnrefid)->save();
				$Subscription->setResponsemessage('Success');
			}
			else if ($response['result_code'] == self::RESPONSE_CODE_VOID_ERROR)
			{
				$Subscription->setResponsemessage('Failed : '.Mage::helper('paypal')->__('You cannot void a verification transaction'));
				throw new Mage_Paypal_Exception(Mage::helper('paypal')->__('You cannot void a verification transaction'));
			}
			else if ($response['result_code'] != self::RESPONSE_CODE_APPROVED  && 
					 $response['result_code'] != self::RESPONSE_CODE_FRAUDSERVICE_FILTER)
			{
				$Subscription->setResponsemessage('Failed : '.$response['respmsg']);
				Mage::throwException($response['respmsg']);
			}
		}
		else
		{
			$Subscription->setResponsemessage('Failed : '.$response['respmsg']);
			Mage::throwException($response['respmsg']);
		}
    }

    /**
     * Save Pnref and Expiry date in database while Placing an order
     */
    public function createSubscription(Indies_Recurringandrentalpayments_Model_Subscription $Subscription, $Order, $Quote)
    {
		$pnrefid = $Order->getPayment()->getTransactionId();// Mage::getSingleton('core/session')->getPnrefId();
		Zend_Date::setOptions(array('extend_month' => true)); // Fix Zend_Date::addMonth unexpected result
		$magento_date = (Mage::getModel('core/date')->date('Y-m-d'));
		$pnref_expireDate = date('Y-m-d', strtotime("+1 years", strtotime($magento_date)));


 		$Subscription
        ->setPayflowPnrefId($pnrefid)	
		->setPnrefExpiryDate($pnref_expireDate)
		->save();
    }
   
    public function reactivateSubscription(Indies_Recurringandrentalpayments_Model_Subscription $Subscription)
    {
        return $this;
	}
   
}