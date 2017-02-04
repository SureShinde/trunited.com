<?php

class Magestore_TruWallet_Model_Observer
{
    public function customerRegisterSuccess($observer)
    {
        $customer_reg = $observer->getCustomer();
        $customer = Mage::getModel('customer/customer')->load($customer_reg->getId());
        if (!$customer->getId())
            return $this;

        Mage::helper('truwallet/transaction')->checkCreditFromSharing($customer);

        return $this;
    }

    public function checkExpiryDate()
    {
        Mage::helper('truwallet/transaction')->checkExpiryDateTransaction();
    }
}
