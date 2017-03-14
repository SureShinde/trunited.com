<?php
class Magestore_TruWallet_Model_System_Config_Source_Payment
{
    public function toOptionArray()
    {
        return Mage::helper('truwallet/payment')->getActivPaymentMethods();
    }

}