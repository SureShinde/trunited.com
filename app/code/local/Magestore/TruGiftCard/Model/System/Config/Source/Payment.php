<?php
class Magestore_TruGiftCard_Model_System_Config_Source_Payment
{
    public function toOptionArray()
    {
        return Mage::helper('trugiftcard/payment')->getActivPaymentMethods();
    }

}