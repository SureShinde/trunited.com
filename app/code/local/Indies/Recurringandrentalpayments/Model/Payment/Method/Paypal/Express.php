<?php

class Indies_Recurringandrentalpayments_Model_Payment_Method_Paypal_Express extends Indies_Recurringandrentalpayments_Model_Payment_Method_Abstract
{
    const PAYMENT_METHOD_CODE = 'paypal_express';
     
	/**
     * This function is run when subscription is created and new order creates
     * @param Indies_Recurringandrentalpayments_Model_Subscription $Subscription
     * @param Mage_Sales_Model_Order     $Order
     * @param Mage_Sales_Model_Quote     $Quote
     * @return Indies_Recurringandrentalpayments_Model_Payment_Method_Abstract
     */

    public function onSubscriptionCreate( Indies_Recurringandrentalpayments_Model_Subscription $Subscription, Mage_Sales_Model_Order $Order, Mage_Sales_Model_Quote $Quote)
    {
        $this->createSubscription($Subscription, $Order, $Quote);
        return $this;
    }

    public function onBillingAddressChange( Indies_Recurringandrentalpayments_Model_Subscription $Subscription, $billingAddress)
    {
        $service = $this->getWebService()
                            ->setSubscription($Subscription)
                            ->setBillingAddress($billingAddress)
        ;
        $service->updateBillingAddress();
        return $this;
    } 
	/**
     * Processes payment for specified order
     * @param Mage_Sales_Model_Order $Order
     * @return
     */
    public function processOrder(Mage_Sales_Model_Order $PrimaryOrder, Mage_Sales_Model_Order $Order = null)
    {
 		$billingid = Mage::getSingleton('core/session')->getBillingAgreementId();
	
    }
    public function createSubscription($Subscription, $Order, $Quote)
    {
		$billingid = Mage::getSingleton('core/session')->getBillingAgreementId();
 		$Subscription
        ->setBillingAgreementId($billingid)	
		->save();
	}
}
