<?php

/**
 * Class Gene_Braintree_Model_Paymentmethod_Paypal
 *
 * @author Dave Macaulay <dave@gene.co.uk>
 */
class Indies_Recurringandrentalpayments_Model_Payment_Method_Gene_Braintree_Paypal extends Gene_Braintree_Model_Paymentmethod_Paypal
{
    /**
     * Validate payment method information object
     *
     * @param   Mage_Payment_Model_Info $info
     * @return  Mage_Payment_Model_Abstract
     */
    public function processOrder(Mage_Sales_Model_Order $PrimaryOrder, Mage_Sales_Model_Order $Order = null)
    {		
		$getSession = Mage::getSingleton('core/session')->getapplyDiscountAmount(); 
		$amount  = $Order->getGrandTotal();
		$orderIncrementId = $Order->getIncrementId();
		
		if ($amount == 0)
		{
			return $this;
		}
		
		if (isset($getSession))
		{
			$amount = 	$amount - $getSession ;
		}
		$Subscription = $this->getSubscription();
		Mage::getSingleton('gene_braintree/wrapper_braintree')->init();
		$result = Braintree_Transaction::sale(array(
			'paymentMethodToken' => $Subscription->getPaymentToken(),
			'amount' => $amount
			));
		
		// If the sale has failed
       		if ($result->success != true) 
			{
				 // Return a different message for declined cards
				if(isset($result->transaction->status))
				{
					// Return a custom response for processor declined messages
					if($result->transaction->status == Braintree_Transaction::PROCESSOR_DECLINED) 
					{
						$Subscription->setResponsemessage('Your transaction has been declined, please try another payment method or contacting your issuing bank.');
						Mage::throwException($Subscription->getResponsemessage());
					}
					else if($result->transaction->status == Braintree_Transaction::GATEWAY_REJECTED
						&& isset($result->transaction->gatewayRejectionReason)
						&& $result->transaction->gatewayRejectionReason == Braintree_Transaction::THREE_D_SECURE)
					{
						// An event for when 3D secure fails
						$Subscription->setResponsemessage('Your card has failed 3D secure validation, please try again or consider using an alternate payment method. Transaction failed with 3D secure');
						Mage::throwException($Subscription->getResponsemessage());
					}
					else
					{
						$Subscription->setResponsemessage($result->transaction->status);
						Mage::throwException($Subscription->getResponsemessage());
					}
				}
					$Subscription->setResponsemessage($result->message);
					Mage::throwException($Subscription->getResponsemessage());
			}
			else
			{
				$Subscription->setResponsemessage('Success');
				$Subscription->setResult($result);
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
		$payment_method_token = $Order->getPayment()->getAdditionalInformation('token');
		$Subscription->setPaymentToken($payment_method_token)->save();	
	}
	
	/**
     * Checks if payment method can perform transaction now
     * @return bool
     */
    public function isValidForTransaction(Indies_Recurringandrentalpayments_Model_Sequence $Sequence)
    {
		 $subscription = Mage::getModel('recurringandrentalpayments/subscription')->load($Sequence->getSubscriptionId());
		 if ($subscription->getPaymentToken() != '')
		 {
			 return true;
		 }
		 else
		 {
			$Sequence->setTransactionStatus('Card payment has failed, missing token')->save();
		 	return false;
		 }
    }
}