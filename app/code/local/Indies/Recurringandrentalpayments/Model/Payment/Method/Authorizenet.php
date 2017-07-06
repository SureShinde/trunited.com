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

class  Indies_Recurringandrentalpayments_Model_Payment_Method_Authorizenet extends  Indies_Recurringandrentalpayments_Model_Payment_Method_Abstract
{

    const PAYMENT_METHOD_CODE = 'authorizenet';

    const XML_PATH_AUTHORIZENET_API_LOGIN_ID = 'payment/authorizenet/login';
    const XML_PATH_AUTHORIZENET_TEST_MODE = 'payment/authorizenet/test';
    const XML_PATH_AUTHORIZENET_DEBUG = 'payment/authorizenet/debug';
    const XML_PATH_AUTHORIZENET_TRANSACTION_KEY = 'payment/authorizenet/trans_key';
    const XML_PATH_AUTHORIZENET_PAYMENT_ACTION = 'payment/authorizenet/payment_action';
    const XML_PATH_AUTHORIZENET_ORDER_STATUS = 'payment/authorizenet/order_status';
    const XML_PATH_AUTHORIZENET_SOAP_TEST = 'payment/authorizenet/soap_test';

    const WEB_SERVICE_MODEL = ' recurringandrentalpayments/web_service_client_authorizenet';

    public function __construct()
    {
        $this->_initWebService();
    }

    /**
     * Initializes web service instance
     * @return Indies_Recurringandrentalpayments_Model_Payment_Method_Authorizenet
     */
    protected function _initWebService()
    {
		$service = Mage::getModel(self::WEB_SERVICE_MODEL);
        $this->setWebService($service);
        return $this;
    }

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
                            ->setBillingAddress($billingAddress);
        $service->updateBillingAddress();
        return $this;
    }

    public function createSubscription($Subscription, $Order, $Quote)
    {
	    $this->getWebService()
                ->setSubscriptionName(Mage::helper('recurringandrentalpayments')->__('Subscription #%s', $Subscription->getId()))
                ->setSubscription($Subscription)
                ->setPayment($Quote->getPayment());
        $CIMInfo = $this->getWebService()->createCIMAccount();

        $CIMId = $this->getWebService()->getCIMCustomerProfileId($CIMInfo);
        $CIMPaymentId = $this->getWebService()->getCIMCustomerPaymentProfileId($CIMInfo);
        $Subscription
                ->setRealId($CIMId)
                ->setRealPaymentId($CIMPaymentId)
                ->save();
        return $this;

    }

    /**
     * Processes payment for specified order
     * @param Mage_Sales_Model_Order $Order
     * @return
     */
    public function processOrder(Mage_Sales_Model_Order $PrimaryOrder, Mage_Sales_Model_Order $Order = null)
    {
        if ($Order->getBaseGrandTotal() > 0) {
            $result = $this->getWebService()
                    ->setSubscription($this->getSubscription())
                    ->setOrder($Order)
                    ->createTransaction();
			
			$this->getSubscription()->setTransactionstatus('Response from Authorize');
          
			$ccTransId = @$result->transactionId;
            $Order->getPayment()->setCcTransId($ccTransId);
        }
    }
}