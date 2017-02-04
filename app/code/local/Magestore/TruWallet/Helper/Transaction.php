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
            $_data['receiver_email'] = isset($data['receiver_email']) ? $data['receiver_email'] : '';
            $_data['receiver_customer_id'] = isset($data['receiver_customer_id']) ? $data['receiver_customer_id'] : '';

            $transaction->setData($_data);
            $transaction->save();

        } catch (Exception $ex){
            Mage::getSingleton('adminhtml/session')->addError(
                Mage::helper('truwallet')->__($ex->getMessage())
            );
        }
    }

    public function getReceiveCreditTransaction($email)
    {
        $collection = Mage::getModel('truwallet/transaction')->getCollection()
            ->addFieldToFilter('receiver_email',$email)
            ->addFieldToFilter('action_type',Magestore_TruWallet_Model_Type::TYPE_TRANSACTION_SHARING)
            ->addFieldToFilter('status',Magestore_TruWallet_Model_Status::STATUS_TRANSACTION_PENDING)
            ->setOrder('transaction_id','desc')
        ;

        if(sizeof($collection) > 0)
            return $collection;
        else
            return null;
    }

    public function checkCreditFromSharing($customer)
    {
        $receiver = Mage::helper('truwallet/account')->loadByCustomerId($customer->getId());
        if($receiver != null)
        {
            $collection = $this->getReceiveCreditTransaction($customer->getEmail());
            if($collection != null && sizeof($collection) > 0)
            {
                foreach ($collection as $transaction)
                {
                    $amount = abs($transaction->getChangedCredit());
                    $receiverAccount = Mage::helper('truwallet/account')->updateCredit($customer->getId(), $amount);
                    $params = array(
                        'credit' => $amount,
                        'title' => '',
                        'receiver_email' => $transaction->getCustomerEmail(),
                        'receiver_customer_id' => $transaction->getCustomerId(),
                    );
                    Mage::helper('truwallet/transaction')->createTransaction(
                        $receiverAccount,
                        $params,
                        Magestore_TruWallet_Model_Type::TYPE_TRANSACTION_RECEIVE_FROM_SHARING,  // type
                        Magestore_TruWallet_Model_Status::STATUS_TRANSACTION_COMPLETED
                    );

                    $transaction->setStatus(Magestore_TruWallet_Model_Status::STATUS_TRANSACTION_COMPLETED);
                    $transaction->setUpdatedTime(now());
                    $transaction->save();
                }
            }
        }
    }

    public function checkExpiryDateTransaction()
    {
        $collection = Mage::getModel('truwallet/transaction')->getCollection()
            ->addFieldToFilter('action_type',Magestore_TruWallet_Model_Type::TYPE_TRANSACTION_SHARING)
            ->addFieldToFilter('status',Magestore_TruWallet_Model_Status::STATUS_TRANSACTION_PENDING)
            ->setOrder('transaction_id','desc')
        ;

        if(sizeof($collection) > 0)
        {
            $timestamp = Mage::getModel('core/date')->timestamp(time());
            $expiry_date = Mage::getStoreConfig('truwallet/sharewallet/expire_date', Mage::app()->getStore()->getId());
            foreach($collection as $transaction)
            {
                $updated_time = strtotime($transaction->getUpdatedTime());
                $compare_time = $this->compareTime($updated_time, $timestamp);
                if($compare_time > $expiry_date)
                {
                    $transaction->setUpdatedTime(now());
                    $transaction->setStatus(Magestore_TruWallet_Model_Status::STATUS_TRANSACTION_CANCELLED);
                    $transaction->save();

                    $rewardAccount = Mage::helper('truwallet/account')->loadByCustomerId($transaction->getCustomerId());
                    $rewardAccount->setTruwalletCredit($rewardAccount->getTruwalletCredit() + abs($transaction->getChangedCredit()));
                    $rewardAccount->save();

                    $this->sendEmailExpiryDate($transaction);
                }
            }
        }
    }

    public function sendEmailExpiryDate($transaction)
    {
        if (!Mage::getStoreConfigFlag(Magestore_RewardPoints_Model_Transaction::XML_PATH_EMAIL_ENABLE, Mage::app()->getStore()->getId())) {
            return $this;
        }

        $store = Mage::app()->getStore($this->getStoreId());
        $translate = Mage::getSingleton('core/translate');
        $translate->setTranslateInline(false);
        $customer = Mage::getModel('customer/customer')->load($transaction->getCustomerId());
        if(!$customer->getId())
            return $this;

        $email_path =  Mage::getStoreConfig(Magestore_RewardPoints_Model_Transaction::XML_PATH_EMAIL_SHARE_EMAIL_EXPIRY_DATE, $store);


        $data = array(
            'store' => $store,
            'customer_name' => $customer->getName(),
            'amount' => Mage::helper('core')->currency(abs($transaction->getProductCredit()), true, false),
        );

        Mage::getModel('core/email_template')
            ->setDesignConfig(array(
                'area' => 'frontend',
                'store' => $store->getId()
            ))->sendTransactional(
                $email_path,
                Mage::getStoreConfig(self::XML_PATH_EMAIL_SENDER, $store),
                $customer->getEmail(),
                $customer->getName(),
                $data
            );

        $translate->setTranslateInline(true);
        return $this;
    }

    public function compareTime($start_time, $end_time)
    {
        $diff = abs($end_time - $start_time);

        $years = floor($diff / (365 * 60 * 60 * 24));
        $months = floor(($diff - $years * 365 * 60 * 60 * 24) / (30 * 60 * 60 * 24));
        $days = floor(($diff - $years * 365 * 60 * 60 * 24 - $months * 30 * 60 * 60 * 24) / (60 * 60 * 24));
        $hours = floor(($diff - $years * 365 * 60 * 60 * 24 - $months * 30 * 60 * 60 * 24 - $days * 60 * 60 * 24) / (60 * 60));
        $minutes = floor(($diff - $years * 365 * 60 * 60 * 24 - $months * 30 * 60 * 60 * 24 - $days * 60 * 60 * 24 - $hours * 60 * 60) / 60);
        $seconds = floor(($diff - $years * 365 * 60 * 60 * 24 - $months * 30 * 60 * 60 * 24 - $days * 60 * 60 * 24 - $hours * 60 * 60 - $minutes * 60));

        if ($years > 0 || $months > 0) {
            return false;
        } else {
            return $days;
        }
    }
	
}