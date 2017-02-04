<?php

class Magestore_TruWallet_Helper_Account extends Mage_Core_Helper_Abstract
{
    public function updateCredit($customer_id, $credit)
    {
        $account = $this->loadByCustomerId($customer_id);
        if($account->getId())
        {
            $_current_credit = $account->getTruwalletCredit();
            $new_credit = $_current_credit + $credit;
            $account->setTruwalletCredit($new_credit > 0 ? $new_credit : 0);
            $account->save();

            return $account;
        } else
            return null;

    }

    public function loadByCustomerId($customer_id)
    {
        $account = Mage::getModel('truwallet/customer')->load($customer_id, 'customer_id');
        if($account->getId())
            return $account;
        else{
            $new_account = $this->createTruWalletCustomer($customer_id);
            if($new_account != null)
                return $new_account;
            else
                return null;
        }
    }

    public function createTruWalletCustomer($customer_id)
    {
        $model = Mage::getModel('truwallet/customer');
        $model->setData('customer_id', $customer_id);
        $model->setData('truwallet_credit', 0);
        $model->setData('created_time', now());
        $model->setData('updated_time', now());
        try{
            $model->save();
        } catch (Exception $ex){
            return null;
        }

        return $model;
    }

    public function getCustomerId()
    {
        if(Mage::getSingleton('customer/session')->isLoggedIn()) {
            return Mage::getSingleton('customer/session')->getCustomer()->getId();
        } else {
            return null;
        }
    }

    public function getTruWalletCredit()
    {
        $customer_id = $this->getCustomerId();
        if($customer_id != null)
        {
            $account = $this->loadByCustomerId($customer_id);

            if($account != null)
            {
                return Mage::helper('core')->currency(
                    $account->getTruwalletCredit(),
                    true,
                    false
                );
            } else {
                return null;
            }
        } else {
            return null;
        }
    }
	
}