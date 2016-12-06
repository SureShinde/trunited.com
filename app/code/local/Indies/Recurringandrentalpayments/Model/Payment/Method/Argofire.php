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

class Indies_Recurringandrentalpayments_Model_Payment_Method_Argofire extends Indies_Recurringandrentalpayments_Model_Payment_Method_Abstract
{

    /** Web service model */
    const WEB_SERVICE_MODEL = 'recurringandrentalpayments/web_service_client_argofire';
    /** New order status */
    const XML_PATH_ARGOFIRE_ORDER_STATUS = 'payment/argofire/order_status';

    public function __construct()
    {
        $this->_initWebService();
    }

    /**
     * This function is run when subscription is created and new order creates
     * @param Indies_Recurringandrentalpayments_Model_Subscription $Subscription
     * @param Mage_Sales_Model_Order     $Order
     * @param Mage_Sales_Model_Quote     $Quote
     * @return Indies_Recurringandrentalpayments_Model_Payment_Method_Argofire
     */
    public function onSubscriptionCreate(Indies_Recurringandrentalpayments_Model_Subscription $Subscription, Mage_Sales_Model_Order $Order, Mage_Sales_Model_Quote $Quote)
    {
        $this->createSubscription($Subscription, $Order, $Quote);
        return $this;
    }

    /**
     * Creates subscription at paypal
     * @param Indies_Recurringandrentalpayments_Model_Subscription $Subscription
     * @return
     */
    public function createSubscription(Indies_Recurringandrentalpayments_Model_Subscription $Subscription, $Order, $Quote)
    {

        // 1. Create subscription profile with 0 period
        // 2. Save CCInfoKey and CustomerInfoKey

        $Payment = $Quote->getPayment();

        // Credit Card number
        $ccNumber = Mage::getSingleton('customer/session')->getRecurringandrentalpaymentsCcNumber();

        // CVV
        //$ccCode = Mage::getSingleton('customer/session')->getRecurringandrentalpaymentsCcCid();

        // Glue month and year
        $expirationDate = sprintf("%02s", $Payment->getMethodInstance()->getInfoInstance()->getCcExpMonth()) . $Payment->getMethodInstance()->getInfoInstance()->getCcExpYear();
        // Customer name
        $customerName = $Quote->getBillingAddress()->getFirstname() . " " . $Quote->getBillingAddress()->getLastname();

        // Calculate start date
        $Date = $Subscription->getNextSubscriptionEventDate(new Zend_Date($Subscription->getDateStart()));
        $Date->addDayOfYear(0 - $Subscription->getPeriod()->getPaymentOffset());
        $date_start = $this->getWebService()->formatDate($Date);
        // Total
        $total = floatval($Subscription->getLastOrder()->getGrandTotal());


        $this->getWebService()
                ->getRequest()
                ->reset()
                ->setData(
            array(
                 'CcAccountNum' => $ccNumber,
                 'CcExpDate' => $expirationDate,
                 'FirstName' => $Quote->getBillingAddress()->getFirstname(),
                 'CustomerName' => $customerName,
                 'CustomerID' => $Quote->getCustomerEmail(),
                 'LastName' => $Quote->getBillingAddress()->getLastname(),
                 'BillingInterval' => "0",
                 'BillingPeriod' => "DAY",
                 'StartDate' => $date_start,
                 'EndDate' => $date_start,
                 'BillAmt' => $total,
                 'TotalAmt' => $total,
                 'ContractName' => Mage::helper('recurringandrentalpayments')->__("Subscription #%s", $Subscription->getId()),
                 'ContractID' => $Subscription->getId()
            )
        );


        try {
            $result = $this->getWebService()->addRecurringCreditCard();
            $CCInfoKey = $result->CcInfoKey;
            $CustomerKey = $result->CustomerKey;

            /**
             * @todo Add and save ContractKey
             */

            $Subscription
                    ->setRealId($CustomerKey)
                    ->setRealPaymentId($CCInfoKey)
                    ->save();

        } catch (Exception $E) {
            throw new Indies_Recurringandrentalpayments_Exception("Argofire recurrent profile creation failed for subscription #{$Subscription->getId()} "
                                        . "and order #{$Order->getId()}",
                print_r($result, 1)
            );
        }
    }

    /**
     * Processes payment for specified order
     * @param Mage_Sales_Model_Order $Order
     * @return
     */
    public function processOrder(Mage_Sales_Model_Order $PrimaryOrder, Mage_Sales_Model_Order $Order = null)
    {

        $amt = $Order->getGrandTotal();
        $inv = $Order->getIncrementId();

        $this->getWebService()
                ->getRequest()
                ->reset()
                ->setData(array(
                               'CcInfoKey' => $this->getSubscription()->getRealPaymentId(),
                               'Amount' => $amt,
                               'InvNum' => $inv,
                               'ExtData' => ''
                          ));

        $result = $this->getWebService()->processCreditCard();

    }


    protected function _convertPeriod($interval)
    {
        return strtoupper($interval);
    }


    /**
     * Initializes web service instance
     * @return Indies_Recurringandrentalpayments_Model_Payment_Method_Argofire
     */
    protected function _initWebService()
    {
        $service = Mage::getModel(self::WEB_SERVICE_MODEL);
        $this->setWebService($service);
        return $this;
    }

}
