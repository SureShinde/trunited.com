<?php

class Magestore_TruWallet_Helper_Transaction extends Mage_Core_Helper_Abstract
{
    const XML_PATH_EMAIL_ENABLE = 'truwallet/email/enable';
    const XML_PATH_EMAIL_SENDER = 'truwallet/email/sender';
    const XML_PATH_EMAIL_SHARE_EMAIL_CUSTOMER = 'truwallet/email/share_email_customer';
    const XML_PATH_EMAIL_SHARE_EMAIL_NON_CUSTOMER = 'truwallet/email/share_email_non_customer';
    const XML_PATH_EMAIL_SHARE_EMAIL_EXPIRY_DATE = 'truwallet/email/share_email_expiry_date';

    public function createTransaction($account, $data, $type, $status)
    {
        $result = null;
        try {
            if (!$account->getId())
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

            $result = $transaction;

        } catch (Exception $ex) {
            Mage::getSingleton('adminhtml/session')->addError(
                Mage::helper('truwallet')->__($ex->getMessage())
            );
            $result = null;
        }

        return $result;
    }

    /**
     * @param $sender_id
     * @param $amount
     * @param $customer_exist
     * @param $receiver_email
     * @param $message
     * @param $status
     * @return $this
     */
    public function sendEmailWhenSharingTruWallet($sender_id, $amount, $customer_exist, $receiver_email, $message, $status)
    {
        $store = Mage::app()->getStore();
        if (!Mage::getStoreConfigFlag(self::XML_PATH_EMAIL_ENABLE, $store->getId())) {
            return $this;
        }

        $translate = Mage::getSingleton('core/translate');
        $translate->setTranslateInline(false);
        $sender = Mage::getModel('customer/customer')->load($sender_id);

        $name = Mage::helper('truwallet')->__('There');
        $current_credit = 0;
        $link = '';
        if($customer_exist) {
            $email_path = Mage::getStoreConfig(self::XML_PATH_EMAIL_SHARE_EMAIL_CUSTOMER, $store);
            $receiver = Mage::getModel("customer/customer");
            $receiver->setWebsiteId($store->getWebsiteId());
            $receiver->loadByEmail($receiver_email);
            if($receiver->getId()){
                $name = $receiver->getName();
                $truWalletAccount = Mage::helper('truwallet/account')->loadByCustomerId($receiver->getId());
                if($truWalletAccount->getId())
                    $current_credit = $truWalletAccount->getTruwalletCredit();
            }

        } else {
            $email_path =  Mage::getStoreConfig(self::XML_PATH_EMAIL_SHARE_EMAIL_NON_CUSTOMER, $store);
            $link = Mage::getUrl('truwallet/transaction/register',array('email'=>$sender->getEmail()));
        }

        $types = Magestore_TruWallet_Model_Type::getOptionArray();
        $data = array(
            'store' => $store,
            'customer_name' => $name,
            'amount' => Mage::helper('core')->currency(abs($amount), true, false),
            'sender_email' => $sender->getEmail(),
            'title' => $types[Magestore_TruWallet_Model_Type::TYPE_TRANSACTION_RECEIVE_FROM_SHARING],
            'point_balance' => Mage::helper('core')->currency(abs($current_credit), true, false),
            'status' => $this->getStatusLabel($status),
            'register_link' => $link,
            'message' => $message,
        );

        /*if($_SERVER['REMOTE_ADDR'] == '101.99.23.40')
    	{
    		zend_debug::dump($sender->debug());
        	zend_debug::dump($amount);
        	zend_debug::dump($customer_exist);
        	zend_debug::dump($receiver_email);
        	zend_debug::dump($message);
        	zend_debug::dump($status);
        	zend_debug::dump($data);
        	zend_debug::dump($email_path);
        	exit;
    	}*/

        Mage::getModel('core/email_template')
            ->setDesignConfig(array(
                'area' => 'frontend',
                'store' => Mage::app()->getStore()->getId()
            ))->sendTransactional(
                $email_path,
                Mage::getStoreConfig(self::XML_PATH_EMAIL_SENDER, $store->getId()),
                $receiver_email,
                $name,
                $data
            );

        $translate->setTranslateInline(true);

        return $this;
    }

    /**
     * @param $status
     * @return string
     */
    public function getStatusLabel($status) {
        $statusHash = Magestore_TruWallet_Model_Status::getTransactionOptionArray();
        if (isset($statusHash[$status])) {
            return $statusHash[$status];
        }
        return '';
    }

    public function getReceiveCreditTransaction($email)
    {
        $collection = Mage::getModel('truwallet/transaction')->getCollection()
            ->addFieldToFilter('receiver_email', $email)
            ->addFieldToFilter('action_type', Magestore_TruWallet_Model_Type::TYPE_TRANSACTION_SHARING)
            ->addFieldToFilter('status', Magestore_TruWallet_Model_Status::STATUS_TRANSACTION_PENDING)
            ->setOrder('transaction_id', 'desc');

        if (sizeof($collection) > 0)
            return $collection;
        else
            return null;
    }

    public function checkCreditFromSharing($customer)
    {
        $receiver = Mage::helper('truwallet/account')->loadByCustomerId($customer->getId());
        if ($receiver != null) {
            $collection = $this->getReceiveCreditTransaction($customer->getEmail());
            if ($collection != null && sizeof($collection) > 0) {
                foreach ($collection as $transaction) {
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
            ->addFieldToFilter('action_type', Magestore_TruWallet_Model_Type::TYPE_TRANSACTION_SHARING)
            ->addFieldToFilter('status', Magestore_TruWallet_Model_Status::STATUS_TRANSACTION_PENDING)
            ->setOrder('transaction_id', 'desc')
        ;

        Mage::log('Check Expiry Date - '.date('d-m-Y H:i:s',time()).' - Quantity: '.sizeof($collection), null, 'expiryDate.log');
        if (sizeof($collection) > 0) {

            $timestamp = Mage::getModel('core/date')->timestamp(time());
            $expiry_date = Mage::getStoreConfig('truwallet/sharewallet/expire_date', Mage::app()->getStore()->getId());

            foreach ($collection as $transaction) {
                $updated_time = strtotime($transaction->getUpdatedTime());
                $compare_time = $this->compareTime($updated_time, $timestamp);
                if ($compare_time > $expiry_date) {
                    // zend_debug::dump($transaction->debug());
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
        $store = Mage::app()->getStore();
        $translate = Mage::getSingleton('core/translate');
        $translate->setTranslateInline(false);
        $customer = Mage::getModel('customer/customer')->load($transaction->getCustomerId());
        if (!$customer->getId())
            return $this;

        $email_path = Mage::getStoreConfig(Magestore_TruWallet_Model_Transaction::XML_PATH_EMAIL_SHARE_EMAIL_EXPIRY_DATE, $store);

        $data = array(
            'store' => $store,
            'customer_name' => $customer->getName(),
            'amount' => Mage::helper('core')->currency(abs($transaction->getChangedCredit()), true, false),
        );

        Mage::getModel('core/email_template')
            ->setDesignConfig(array(
                'area' => 'frontend',
                'store' => $store->getId(),
            ))->sendTransactional(
                $email_path,
                Mage::getStoreConfig(Magestore_TruWallet_Model_Transaction::XML_PATH_EMAIL_SENDER, $store),
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

    public function import()
    {
        $fileName = $_FILES['csv_store']['tmp_name'];
        $csvObject = new Varien_File_Csv();
        $csvData = $csvObject->getData($fileName);
        $import_count = 0;

        $transactionSave = Mage::getModel('core/resource_transaction');
        $connection = Mage::getSingleton('core/resource')->getConnection('core_write');

        try {
            $connection->beginTransaction();

            if (sizeof($csvData) > 0) {
                $current_symbol = Mage::app()->getLocale()->currency(Mage::app()->getStore()->getCurrentCurrencyCode())->getSymbol();
                $type_data = Magestore_TruWallet_Model_Type::getDataType();
                foreach ($csvData as $csv) {
                    $amount = str_replace($current_symbol, '', $csv[2]);
                    if (isset($csv[0]) && !filter_var($csv[0], FILTER_VALIDATE_INT) === false
                        && isset($csv[1]) && !filter_var($csv[1], FILTER_VALIDATE_EMAIL) === false
                        && isset($csv[3]) && !filter_var($csv[3], FILTER_VALIDATE_INT) === false
                        && isset($amount) && !filter_var($amount, FILTER_VALIDATE_FLOAT) === false
                        && in_array($csv[3], $type_data)
                    ) {
                        $customer = Mage::getModel('customer/customer')->load($csv[0]);

                        if ($customer->getId() && strcasecmp($customer->getEmail(), $csv[1]) == 0) {
                            $account = Mage::helper('truwallet/account')->loadByCustomerId($customer->getId());
                            if ($account != null && $account->getId()) {
                                $_truWallet_before = $account->getTruwalletCredit();
                                $_truWallet_new = $_truWallet_before + $amount;
                                $account->setTruwalletCredit($_truWallet_new)
                                    ->setUpdatedTime(now())
                                    ->save();
                                $obj = Mage::getModel('truwallet/transaction');
                                $_data = array(
                                    'truwallet_id' => $account->getId(),
                                    'customer_id' => $customer->getId(),
                                    'customer_email' => $customer->getEmail(),
                                    'title' => isset($csv[4]) ? $csv[4] : '',
                                    'action_type' => isset($csv[3]) ? $csv[3] : '',
                                    'store_id' => Mage::app()->getStore()->getId(),
                                    'created_time' => now(),
                                    'updated_time' => now(),
                                    'current_credit' => $_truWallet_new,
                                    'changed_credit' => $amount,
                                    'status' => Magestore_TruWallet_Model_Status::STATUS_TRANSACTION_COMPLETED,
                                );
                                $obj->setData($_data);
                                $transactionSave->addObject($obj);
                                $import_count++;
                            }
                        }
                    }
                }
            }

            $transactionSave->save();
            $connection->commit();
        } catch (Exception $e) {
            $connection->rollback();
            zend_debug::dump($e->getMessage());
            exit;
        }
        return $import_count;
    }

    public function checkAddedTransaction($order_id, $customer_id)
    {
        $collection = Mage::getModel('truwallet/transaction')->getCollection()
            ->addFieldToFilter('customer_id',$customer_id)
            ->addFieldToFilter('order_id',$order_id)
            ->addFieldToFilter('action_type',Magestore_TruWallet_Model_Type::TYPE_TRANSACTION_PURCHASE_GIFT_CARD)
            ->setOrder('transaction_id','desc')
            ->getFirstItem()
        ;

        if($collection->getId())
            return true;
        else
            return false;
    }

    public function addTruWalletFromProduct($order)
    {
        $helper = Mage::helper('truwallet');
        if(!$helper->isEnableTruWalletProduct())
            return $this;

        $order_status_configure = $helper->getTruWalletOrderStatus();
        $product_configure = $helper->getTruWalletSku();
        $value_configure = $helper->getTruWalletValue();

        if($order_status_configure == '')
            return $this;

        $customer = Mage::getModel('customer/customer')->load($order->getCustomerId());
        $flag = $this->checkAddedTransaction($order->getEntityId(), $customer->getId());

        $items = $order->getAllItems();
        $is_only_virtual = 0;
        foreach($items as $item)
        {
            $product = Mage::getModel('catalog/product')->load($item->getProductId());
            if($product->getTypeId() != 'virtual')
            {
                $is_only_virtual++;
            }
        }

        if((strcasecmp($order->getStatus(),$order_status_configure) == 0 || (strcasecmp($order->getStatus(),'complete') == 0 && $is_only_virtual == 0)) && !$flag){
            $items = $order->getAllItems();
            try{
                foreach($items as $orderItem) {
                    if(strcasecmp($orderItem->getSku(),$product_configure) == 0)
                    {
                        $credit = $value_configure * (int)$orderItem->getQtyOrdered();
                        $receiverAccount = Mage::helper('truwallet/account')->updateCredit($customer->getId(), $credit);
                        $params = array(
                            'credit' => $credit,
                            'title' => Mage::helper('truwallet')->__('Purchased truWallet Gift Card on order #<a href="'.Mage::getUrl('sales/order/view',array('order_id'=>$order->getEntityId())).'">'.$order->getIncrementId().'</a>'),
                        );
                        Mage::helper('truwallet/transaction')->createTransaction(
                            $receiverAccount,
                            $params,
                            Magestore_TruWallet_Model_Type::TYPE_TRANSACTION_PURCHASE_GIFT_CARD,  // type
                            Magestore_TruWallet_Model_Status::STATUS_TRANSACTION_COMPLETED
                        );
                    }
                }
            } catch (Exception $ex) {

            }
        }
    }

}