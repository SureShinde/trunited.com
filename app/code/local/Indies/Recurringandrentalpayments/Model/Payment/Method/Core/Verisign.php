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
class Indies_Recurringandrentalpayments_Model_Payment_Method_Core_Verisign extends Mage_Paypal_Model_Payflowpro
{
	 /**
     * Validate payment method information object
     *
     * @param   Mage_Payment_Model_Info $info
     * @return  Mage_Payment_Model_Abstract
     */
	 
	 
    public function validate()
    {
        if (Indies_Recurringandrentalpayments_Model_Subscription::isIterating()) {
            return $this;
        } else {
            return parent::validate();
        }
    }

    public function capture(Varien_Object $payment, $amount)
    {
        if (Indies_Recurringandrentalpayments_Model_Subscription::isIterating()) {
            $Subscription = Indies_Recurringandrentalpayments_Model_Subscription::getInstance()->processPayment($payment->getOrder());
            return $this;
        }
        return parent::capture($payment, $amount);
    }

    public function authorize(Varien_Object $payment, $amount)
    {		
        if (Indies_Recurringandrentalpayments_Model_Subscription::isIterating()) {
            $Subscription = Indies_Recurringandrentalpayments_Model_Subscription::getInstance()->processPayment($payment->getOrder());
            return $this;
        }
        return parent::authorize($payment, $amount);
    }

}