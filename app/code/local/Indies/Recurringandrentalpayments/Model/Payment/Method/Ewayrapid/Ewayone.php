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

class Indies_Recurringandrentalpayments_Model_Payment_Method_Ewayrapid_Ewayone extends Indies_Recurringandrentalpayments_Model_Payment_Method_Abstract//Gene_Braintree_Model_Paymentmethod_Creditcard
{
	 /**
     * Validate payment method information object
     *
     * @param   Mage_Payment_Model_Info $info
     * @return  Mage_Payment_Model_Abstract
     */
    public function processOrder(Mage_Sales_Model_Order $PrimaryOrder, Mage_Sales_Model_Order $Order = null)
    {		
		$ewayConfig = Mage::getSingleton('ewayrapid/config');
		$url = $ewayConfig->getRapidAPIUrl('Transaction');
		$getSession = Mage::getSingleton('core/session')->getapplyDiscountAmount(); 
		$amount = $Order->getGrandTotal() ;
		$Subscription = $this->getSubscription();

		if (isset($getSession))
		{
			$amount = 	$amount - $getSession ;
		}
		
		$auth = $ewayConfig->getBasicAuthenticationHeader();
		$params = array();
		$id = array();
		//$cust['Customer'] = $params;
		$id['TokenCustomerID'] = $Subscription->getPaymentToken();
		$params['Customer'] = $id;

		$payment = array();
		$TotalAmount = array ();
		$TotalAmount['TotalAmount'] = round($amount * 100) ;

		//$params['Customer'] = $id;
		$params['Payment'] = $TotalAmount;
		$params['TransactionType'] =  'MOTO';
		$params['Method'] = 'TokenPayment';
		
		$json = json_encode($params);
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json", "X-EWAY-APIVERSION: 40"));
        curl_setopt($ch, CURLOPT_USERPWD, $auth);
		curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
        
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

        $response = curl_exec($ch);
		$result = json_decode($response,true);
		
		$info = curl_getinfo($ch);
		$http_code = intval(trim($info['http_code']));

		if ($http_code == 401 || $http_code == 404 || $http_code == 403)
		{
				$Subscription->setResponsemessage(Mage::helper('ewayrapid')->__("Please check the API Key and Password"));
				Mage::throwException($Subscription->getResponsemessage());
        } 
		elseif ($http_code != 200) 
		{
				$Subscription->setResponsemessage(Mage::helper('ewayrapid')->__("Error connecting to payment gateway, please try again"));
				Mage::throwException($Subscription->getResponsemessage());
        }
		else 
		{
			curl_close($ch);
			if(isset($result['Errors'] ))
			{
				$Subscription->setResponsemessage("Error Codes :".$result['Errors']);
				Mage::throwException($Subscription->getResponsemessage());
        	}
			else
			{
				$transid = $result['TransactionID'];
				$Order->getPayment()->setTransactionId($transid)->save(); 
				$Subscription->setResponsemessage('Success');
			}
    	 }
	 }
    /**
     * Returns service subscription service id for specified quote
     * @param mixed $quoteId
     * @return int
     */
    public function getSubscriptionId($OrderItem)
    {
        return 1;
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
 
  
	public function createSubscription($Subscription, $Order, $Quote)
    {
		$payment_method_token = Mage::getSingleton('core/session')->getcustToken();
		$Subscription->setPaymentToken($payment_method_token)->save();	
	}
	
	/**
     * Checks if payment method can perform transaction now
     * @return bool
     */
    public function isValidForTransaction(Indies_Recurringandrentalpayments_Model_Sequence $Sequence)
    {
		 $subscription = Mage::getModel('recurringandrentalpayments/subscription')->load($Sequence->getSubscriptionId());
		  echo $Sequence->getSubscriptionId();
		 if ($subscription->getPaymentToken() != '')
		 {
			 return true;
		 }
		 else
		 {
			$Sequence->setTransactionStatus('Card payment has failed, missing token customer id')->save();
		 	return false;
		 }
    }
}