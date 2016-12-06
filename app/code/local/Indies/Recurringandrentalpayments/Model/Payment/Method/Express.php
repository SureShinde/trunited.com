<?php
class Indies_Recurringandrentalpayments_Model_Payment_Method_Express extends Mage_Paypal_Model_Express
{
     /**
     * Website Payments Pro instance
     *
     * @var Mage_Paypal_Model_Pro
     */
    protected $_pro = null;

    /**
     * Place an order with authorization or capture action
     *
     * @param Mage_Sales_Model_Order_Payment $payment
     * @param float $amount
     * @return Mage_Paypal_Model_Express
     */
    protected function _placeOrder(Mage_Sales_Model_Order_Payment $payment, $amount)
    {
        $order = $payment->getOrder();
		$subscriptoin_billingagreement = Mage::getSingleton("core/session")->getBillingAgreementData();
		
		if($subscriptoin_billingagreement !='')
		{
			// prepare api call
			//Here come while capture amount of subscription
			$api = $this->_pro->getApi()
            ->setPayerId($payment->getAdditionalInformation(Mage_Paypal_Model_Express_Checkout::PAYMENT_INFO_TRANSPORT_PAYER_ID))
            ->setAmount($amount)
            ->setPaymentAction($this->_pro->getConfig()->paymentAction)
            ->setNotifyUrl(Mage::getUrl('paypal/ipn/'))
            ->setInvNum($order->getIncrementId())
            ->setCurrencyCode($order->getBaseCurrencyCode())
            ->setReferenceId($subscriptoin_billingagreement);
			$api->callDoReferenceTransaction();
		}
		else
		{
			$token = $payment->getAdditionalInformation(Mage_Paypal_Model_Express_Checkout::PAYMENT_INFO_TRANSPORT_TOKEN);
      		$api = $this->_pro->getApi()
            	->setToken($token)
           		->setPayerId($payment->getAdditionalInformation(Mage_Paypal_Model_Express_Checkout::PAYMENT_INFO_TRANSPORT_PAYER_ID))
            	->setAmount($amount)
            	->setPaymentAction($this->_pro->getConfig()->paymentAction)
            	->setNotifyUrl(Mage::getUrl('paypal/ipn/'))
            	->setInvNum($order->getIncrementId())
            	->setCurrencyCode($order->getBaseCurrencyCode())
            	->setPaypalCart(Mage::getModel('paypal/cart', array($order)))
            	->setIsLineItemsEnabled($this->_pro->getConfig()->lineItemsEnabled);

			// call api and get details from it
			$api->callDoExpressCheckoutPayment();	
		}
		Mage::getSingleton('core/session')->unsBillingAgreementData();
        $this->_importToPayment($api, $payment);
        return $this;
    }

 
}
