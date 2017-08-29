<?php

class Magestore_TruGiftCard_Helper_Account extends Mage_Core_Helper_Abstract
{
    public function updateCredit($customer_id, $credit)
    {
        $account = $this->loadByCustomerId($customer_id);
        if($account != null && $account->getId())
        {
            $_current_credit = $account->getTrugiftcardCredit();
            $new_credit = $_current_credit + $credit;
            $account->setTrugiftcardCredit($new_credit > 0 ? $new_credit : 0);
            $account->save();
            return $account;
        } else
            return null;

    }

    public function loadByCustomerId($customer_id)
    {
        $account = Mage::getModel('trugiftcard/customer')->load($customer_id, 'customer_id');
        if($account->getId())
            return $account;
        else{
            $new_account = $this->createTruGiftCardCustomer($customer_id);
            if($new_account != null)
                return $new_account;
            else
                return null;
        }
    }

    public function createTruGiftCardCustomer($customer_id)
    {
        $model = Mage::getModel('trugiftcard/customer');
        $model->setData('customer_id', $customer_id);
        $model->setData('trugiftcard_credit', 0);
        $model->setData('created_time', now());
        $model->setData('updated_time', now());
        try{
            if($customer_id != 0)
                $model->save();
            else
                return null;
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

    public function getCurrentAccount()
    {
        $customer_id = $this->getCustomerId();
        if ($customer_id != null) {
            $account = $this->loadByCustomerId($customer_id);

            if ($account != null) {
                return $account;
            } else {
                return null;
            }
        } else {
            return null;
        }
    }

    public function getTruGiftCardCredit($is_format = true)
    {
        $account = $this->getCurrentAccount();
        if($account != null)
        {
            if ($is_format)
                return Mage::helper('core')->currency(
                    $account->getTrugiftcardCredit(),
                    true,
                    false
                );
            else
                return $account->getTrugiftcardCredit();
        } else {
            return null;
        }
    }
	
}