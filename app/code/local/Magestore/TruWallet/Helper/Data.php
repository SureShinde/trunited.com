<?php

class Magestore_TruWallet_Helper_Data extends Mage_Core_Helper_Abstract
{
    public function getMyTruWalletLabel()
    {
        $image = '<img src="'.Mage::getDesign()->getSkinUrl('images/truwallet/point.png').'" />';
        return $this->__('My truWallet') . ' ' . $image;
    }

    public function getShareTruWalletLabel()
    {
        $image = '<img src="'.Mage::getDesign()->getSkinUrl('images/truwallet/point.png').'" />';
        return $this->__('Share truWallet Money') . ' ' . $image;
    }


    public function synchronizeCredit()
    {
        $reward_customers = Mage::getModel('rewardpoints/customer')->getCollection()
            ->setOrder('reward_id','desc')
        ;

        if(count($reward_customers) > 0)
        {
            $transactionSave = Mage::getModel('core/resource_transaction');
            $connection = Mage::getSingleton('core/resource')->getConnection('core_write');
            try {
                $connection->beginTransaction();
                foreach ($reward_customers as $reward)
                {
                    $truWallet_customer = Mage::getModel('truwallet/customer');
                    $truWallet_customer->setData('customer_id', $reward->getCustomerId());
                    $truWallet_customer->setData('truwallet_credit', $reward->getProductCredit());
                    $truWallet_customer->setData('created_time', now());
                    $truWallet_customer->setData('updated_time', now());
                    $transactionSave->addObject($truWallet_customer);
                }

                $transactionSave->save();
                $connection->commit();
                echo 'success';
            } catch (Exception $e) {
                $connection->rollback();
                zend_debug::dump($e->getMessage());
            }
        } else {
            echo 'Empty';
        }

    }

    public function synchronizeTransaction()
    {
        $reward_transactions = Mage::getModel('rewardpoints/transaction')->getCollection()
            ->addFieldToFilter('action_type',array('in'=>array(0,3,4,5)))
            ->setOrder('transaction_id','desc')
        ;

        if(count($reward_transactions) > 0)
        {
            $transactionSave = Mage::getModel('core/resource_transaction');
            $connection = Mage::getSingleton('core/resource')->getConnection('core_write');
            try {
                $connection->beginTransaction();
                foreach ($reward_transactions as $transaction)
                {
                    $truwallet_account = Mage::helper('truwallet/account')->loadByCustomerId($transaction->getCustomerId());
                    if($transaction->getCustomerId() != null && $truwallet_account != null)
                    {
                        $truWallet_transaction = Mage::getModel('truwallet/transaction');
                        $_data = array();
                        $_data['truwallet_id'] = $truwallet_account->getId();
                        $_data['customer_id'] = $transaction->getCustomerId();
                        $_data['customer_email'] = $transaction->getCustomerEmail();
                        $_data['title'] = $transaction->getTitle();
                        $_data['store_id'] = $transaction->getStoreId();

                        switch($transaction->getStatus())
                        {
                            case 3:
                                $_data['status'] = Magestore_TruWallet_Model_Status::STATUS_TRANSACTION_COMPLETED;
                                break;

                            case 4:
                                $_data['status'] = Magestore_TruWallet_Model_Status::STATUS_TRANSACTION_CANCELLED;
                                break;

                            case 1:
                                $_data['status'] = Magestore_TruWallet_Model_Status::STATUS_TRANSACTION_PENDING;
                                break;

                            case 2:
                                $_data['status'] = Magestore_TruWallet_Model_Status::STATUS_TRANSACTION_ON_HOLD;
                                break;

                            case 5:
                                $_data['status'] = Magestore_TruWallet_Model_Status::STATUS_TRANSACTION_EXPIRED;
                                break;
                        }

                        switch($transaction->getActionType())
                        {
                            case 0:
                                $_data['action_type'] = Magestore_TruWallet_Model_Type::TYPE_TRANSACTION_BY_ADMIN;
                                break;

                            case 3:
                                $_data['action_type'] = Magestore_TruWallet_Model_Type::TYPE_TRANSACTION_TRANSFER;
                                break;

                            case 4:
                                $_data['action_type'] = Magestore_TruWallet_Model_Type::TYPE_TRANSACTION_SHARING;
                                break;

                            case 5:
                                $_data['action_type'] = Magestore_TruWallet_Model_Type::TYPE_TRANSACTION_RECEIVE_FROM_SHARING;
                                break;
                        }

                        $_data['created_time'] = $transaction->getCreatedTime();
                        $_data['updated_time'] = $transaction->getUpdatedTime();
                        $_data['expiration_date'] = $transaction->getExpirationDate();
                        $_data['order_id'] = $transaction->getOrderId() == 0 ? null : $transaction->getOrderId();
                        $_data['current_credit'] = $truwallet_account->getTruwalletCredit();
                        $_data['changed_credit'] = $transaction->getProductCredit();
                        $_data['receiver_email'] = $transaction->getReceiverEmail();
                        $_data['receiver_customer_id'] = $transaction->getReceiverCustomerId();

                        $truWallet_transaction->setData($_data);
                        $transactionSave->addObject($truWallet_transaction);
                    }
                }

                $transactionSave->save();
                $connection->commit();
                echo 'success';
            } catch (Exception $e) {
                $connection->rollback();
                zend_debug::dump($e->getMessage());
            }
        } else {
            echo 'Empty';
        }

    }
	
}