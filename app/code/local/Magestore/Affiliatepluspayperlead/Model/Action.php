<?php

class Magestore_Affiliatepluspayperlead_Model_Action extends Varien_Object
{
    const ACCOUNT_SIGNUP	= 1;
    const CUSTOMER_SUBSCRIBE	= 2;

    static public function getOptionArray()
    {
        return array(
            self::ACCOUNT_SIGNUP    => Mage::helper('affiliateplus')->__('Sign up for an account'),
            self::CUSTOMER_SUBSCRIBE   => Mage::helper('affiliateplus')->__('Subscribe to newsletters')
        );
    }
}