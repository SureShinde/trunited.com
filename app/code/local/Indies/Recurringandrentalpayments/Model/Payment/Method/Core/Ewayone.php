<?php
class Indies_Recurringandrentalpayments_Model_Payment_Method_Core_Ewayone extends Eway_Rapid31_Model_Method_Ewayone
{
    protected $_code  = 'ewayrapid_ewayone';

    protected $_formBlockType = 'ewayrapid/form_direct_ewayone';
    protected $_infoBlockType = 'ewayrapid/info_direct_ewayone';
    protected $_canCapturePartial           = true;
    protected $_billing                     = null;

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
    /**
     * Authorize & Capture a payment
     *
     * @param Varien_Object $payment
     * @param float $amount
     *
     * @return Mage_Payment_Model_Abstract
     */
    public function capture(Varien_Object $payment, $amount)
    {
         if (Indies_Recurringandrentalpayments_Model_Subscription::isIterating())
		 {
            $Subscription = Indies_Recurringandrentalpayments_Model_Subscription::getInstance()->processPayment($payment->getOrder());
			return $this;
         }
        return parent::capture($payment, $amount);
    }

    /**
     * Authorize a payment
     *
     * @param Varien_Object $payment
     * @param float $amount
     *
     * @return Mage_Payment_Model_Abstract
     */
    public function authorize(Varien_Object $payment, $amount)
    {
        if (Indies_Recurringandrentalpayments_Model_Subscription::isIterating())
		{
            $Subscription = Indies_Recurringandrentalpayments_Model_Subscription::getInstance()->processPayment($payment->getOrder());
            return $this;
        }
        return parent::authorize($payment, $amount);
    }
}