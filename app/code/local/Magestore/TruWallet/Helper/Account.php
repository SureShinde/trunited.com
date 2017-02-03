<?php

class Magestore_TruWallet_Helper_Account extends Mage_Core_Helper_Abstract
{
    public function updateCredit($customer_id, $credit)
    {
        $account = $this->loadByCustomerId($customer_id);
        if($account->getId())
        {
            $_current_credit = $account->getTruwalletCredit();
            $account->setTruwalletCredit($_current_credit+$credit);
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
	
}