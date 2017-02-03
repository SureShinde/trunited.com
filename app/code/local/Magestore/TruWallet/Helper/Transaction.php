<?php

class Magestore_TruWallet_Helper_Transaction extends Mage_Core_Helper_Abstract
{
    public function createTransaction($account, $data, $type, $status)
    {
        try{
            if(!$account->getId())
                throw new Exception(
                    Mage::helper('truwallet')->__('Customer doesn\'t exist')
                );

            $customer = Mage::getModel('customer/customer')->load($account->getCustomerId());

            $transaction = Mage::getModel('truwallet/transaction');
            $_data = array();
            $_data['truwallet_id'] = $account->getId();
            $_data['customer_id'] = $customer->getId();
            $_data['customer_email'] = $customer->getEmail();
            $_data['title'] = isset($data['title']) ? $data['title'] : '';
            $_data['action_type'] = $type;
            $_data['store_id'] = Mage::app()->getStore()->getId();
            $_data['status'] = $status;
            $_data['created_time'] = now();
            $_data['updated_time'] = now();
            $_data['expiration_date'] = isset($data['expiration_date']) ? $data['expiration_date'] : '';
            $_data['order_id'] = isset($data['order_id']) ? $data['order_id'] : '';
            $_data['current_credit'] = $account->getTruwalletCredit();
            $_data['changed_credit'] = isset($data['credit']) ? $data['credit'] : '';

            $transaction->setData($_data);
            $transaction->save();

        } catch (Exception $ex){
            Mage::getSingleton('adminhtml/session')->addError(
                Mage::helper('truwallet')->__($ex->getMessage())
            );
        }
    }
	
}