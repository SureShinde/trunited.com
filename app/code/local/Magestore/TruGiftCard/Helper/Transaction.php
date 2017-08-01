<?php

class Magestore_TruGiftCard_Helper_Transaction extends Mage_Core_Helper_Abstract
{
    const XML_PATH_EMAIL_ENABLE = 'trugiftcard/email/enable';
    const XML_PATH_EMAIL_SENDER = 'trugiftcard/email/sender';
    const XML_PATH_EMAIL_SHARE_EMAIL_CUSTOMER = 'trugiftcard/email/share_email_customer';
    const XML_PATH_EMAIL_SHARE_EMAIL_NON_CUSTOMER = 'trugiftcard/email/share_email_non_customer';
    const XML_PATH_EMAIL_SHARE_EMAIL_EXPIRY_DATE = 'trugiftcard/email/share_email_expiry_date';

    public function createTransaction($account, $data, $type, $status)
    {
        $result = null;
        try {
            if (!$account->getId())
                throw new Exception(
                    Mage::helper('trugiftcard')->__('Customer doesn\'t exist')
                );

            $customer = Mage::getModel('customer/customer')->load($account->getCustomerId());

            $transaction = Mage::getModel('trugiftcard/transaction');
            $_data = array();
            $_data['trugiftcard_id'] = $account->getId();
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
            $_data['current_credit'] = $account->getTrugiftcardCredit();
            $_data['changed_credit'] = isset($data['credit']) ? $data['credit'] : '';
            $_data['receiver_email'] = isset($data['receiver_email']) ? $data['receiver_email'] : '';
            $_data['receiver_customer_id'] = isset($data['receiver_customer_id']) ? $data['receiver_customer_id'] : '';
            $_data['recipient_transaction_id'] = isset($data['recipient_transaction_id']) ? $data['recipient_transaction_id'] : '';

            $transaction->setData($_data);
            $transaction->save();

            $result = $transaction;

        } catch (Exception $ex) {
            Mage::getSingleton('adminhtml/session')->addError(
                Mage::helper('trugiftcard')->__($ex->getMessage())
            );
            $result = null;
        }

        return $result;
    }

    /**
     * @param $recipient
     * @param $data
     * @param $type
     * @param $status
     * @return false|Mage_Core_Model_Abstract|null
     */
    public function createNonTransaction($recipient, $data, $type, $status)
    {
        $result = null;
        try {

            $transaction = Mage::getModel('trugiftcard/transaction');
            $_data = array();
            $_data['trugiftcard_id'] = '';
            $_data['customer_id'] = '';
            $_data['customer_email'] = isset($data['customer_email']) ? $data['customer_email'] : '';
            $_data['title'] = isset($data['title']) ? $data['title'] : '';
            $_data['action_type'] = $type;
            $_data['store_id'] = Mage::app()->getStore()->getId();
            $_data['status'] = $status;
            $_data['created_time'] = now();
            $_data['updated_time'] = now();
            $_data['expiration_date'] = isset($data['expiration_date']) ? $data['expiration_date'] : '';
            $_data['order_id'] = isset($data['order_id']) ? $data['order_id'] : '';
            $_data['current_credit'] = 0;
            $_data['changed_credit'] = isset($data['credit']) ? $data['credit'] : '';
            $_data['receiver_email'] = $recipient->getEmail();
            $_data['receiver_customer_id'] = $recipient->getId();

            $transaction->setData($_data);
            $transaction->save();

            $result = $transaction;

        } catch (Exception $ex) {
            Mage::getSingleton('adminhtml/session')->addError(
                Mage::helper('trugiftcard')->__($ex->getMessage())
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
     * @param $expiration_date
     * @return $this
     */
    public function sendEmailWhenSharingTruGiftCard($sender_id, $amount, $customer_exist, $receiver_email, $message, $status, $expiration_date)
    {
        $store = Mage::app()->getStore();
        if (!Mage::getStoreConfigFlag(self::XML_PATH_EMAIL_ENABLE, $store->getId())) {
            return $this;
        }

        $translate = Mage::getSingleton('core/translate');
        $translate->setTranslateInline(false);
        $sender = Mage::getModel('customer/customer')->load($sender_id);

        $name = Mage::helper('trugiftcard')->__('There');
        $current_credit = 0;
        $link = '';
        if ($customer_exist) {
            $email_path = Mage::getStoreConfig(self::XML_PATH_EMAIL_SHARE_EMAIL_CUSTOMER, $store);
            $receiver = Mage::getModel("customer/customer");
            $receiver->setWebsiteId($store->getWebsiteId());
            $receiver->loadByEmail($receiver_email);
            if ($receiver->getId()) {
                $name = $receiver->getName();
                $truGiftCardAccount = Mage::helper('trugiftcard/account')->loadByCustomerId($receiver->getId());
                if ($truGiftCardAccount->getId())
                    $current_credit = $truGiftCardAccount->getTrugiftcardCredit();
            }

        } else {
            $email_path = Mage::getStoreConfig(self::XML_PATH_EMAIL_SHARE_EMAIL_NON_CUSTOMER, $store);
            $link = Mage::getUrl('trugiftcard/transaction/register', array('email' => $sender->getEmail()));
        }

        $types = Magestore_TruGiftCard_Model_Type::getOptionArray();
        $data = array(
            'store' => $store,
            'customer_name' => $name,
            'amount' => Mage::helper('core')->currency(abs($amount), true, false),
            'sender_email' => $sender->getEmail(),
            'title' => $types[Magestore_TruGiftCard_Model_Type::TYPE_TRANSACTION_RECEIVE_FROM_SHARING],
            'point_balance' => Mage::helper('core')->currency(abs($current_credit), true, false),
            'status' => $this->getStatusLabel($status),
            'register_link' => $link,
            'message' => $message,
            'expiration_date' => date('m/d/Y h:ia', strtotime($expiration_date)),
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
    public function getStatusLabel($status)
    {
        $statusHash = Magestore_TruGiftCard_Model_Status::getTransactionOptionArray();
        if (isset($statusHash[$status])) {
            return $statusHash[$status];
        }
        return '';
    }

    public function getReceiveCreditTransaction($email)
    {
        $collection = Mage::getModel('trugiftcard/transaction')->getCollection()
            ->addFieldToFilter('receiver_email', $email)
            ->addFieldToFilter('action_type', Magestore_TruGiftCard_Model_Type::TYPE_TRANSACTION_SHARING)
            ->addFieldToFilter('status', Magestore_TruGiftCard_Model_Status::STATUS_TRANSACTION_PENDING)
            ->setOrder('transaction_id', 'desc');

        if (sizeof($collection) > 0)
            return $collection;
        else
            return null;
    }

    public function checkCreditFromSharing($customer)
    {
        $receiver = Mage::helper('trugiftcard/account')->loadByCustomerId($customer->getId());
        if ($receiver != null && $receiver->getId()) {

            $collection_recipient = $this->getCollectionByEmail($customer->getEmail(), Magestore_TruGiftCard_Model_Type::TYPE_TRANSACTION_RECEIVE_FROM_SHARING);
            if ($collection_recipient != null) {
                $transactionSave = Mage::getModel('core/resource_transaction');
                $new_credit = 0;
                foreach ($collection_recipient as $recipient) {
                    $recipient->setData('trugiftcard_id', $receiver->getId());
                    $recipient->setData('customer_id', $customer->getId());
                    $recipient->setData('updated_time', now());
                    $new_credit += $recipient->getChangedCredit();
                    $transactionSave->addObject($recipient);
                }
                Mage::helper('trugiftcard/account')->updateCredit($customer->getId(), $new_credit);
                $transactionSave->save();
            }

            $collection_sender = $this->getCollectionByEmail($customer->getEmail(), Magestore_TruGiftCard_Model_Type::TYPE_TRANSACTION_SHARING);
            if ($collection_sender != null) {
                $transactionSave = Mage::getModel('core/resource_transaction');
                foreach ($collection_sender as $sender) {
                    $sender->setData('receiver_customer_id', $customer->getId());
                    $sender->setData('updated_time', now());
                    $transactionSave->addObject($sender);
                }
                $transactionSave->save();
            }
        }
    }

    public function getCollectionByEmail($email, $type)
    {
        $collection = Mage::getModel('trugiftcard/transaction')->getCollection()
            ->addFieldToFilter('action_type', $type)
            ->addFieldToFilter('status', Magestore_TruGiftCard_Model_Status::STATUS_TRANSACTION_PENDING)
            ->setOrder('transaction_id', 'desc');

        if ($type == Magestore_TruGiftCard_Model_Type::TYPE_TRANSACTION_RECEIVE_FROM_SHARING)
            $collection->addFieldToFilter('customer_email', array('like' => '%' . $email . '%'));
        else if ($type == Magestore_TruGiftCard_Model_Type::TYPE_TRANSACTION_SHARING)
            $collection->addFieldToFilter('receiver_email', array('like' => '%' . $email . '%'));

        if (sizeof($collection) > 0)
            return $collection;
        else
            return null;
    }

    public function checkExpiryDateTransaction()
    {
        $collection = Mage::getModel('trugiftcard/transaction')->getCollection()
            ->addFieldToFilter('action_type', Magestore_TruGiftCard_Model_Type::TYPE_TRANSACTION_SHARING)
            ->addFieldToFilter('status', Magestore_TruGiftCard_Model_Status::STATUS_TRANSACTION_PENDING)
            ->addFieldToFilter('expiration_date', array('notnull' => true))
            ->setOrder('transaction_id', 'desc')
        ;

        Mage::log('Check Expiry Date - ' . date('d-m-Y H:i:s', time()) . ' - Quantity: ' . sizeof($collection), null, 'truGiftCardExpirationDate.log');
        if (sizeof($collection) > 0) {
            foreach ($collection as $transaction) {
                $expiration_date = strtotime($transaction->getExpirationDate());
                $compare_time = $this->compareExpireDate($expiration_date, time());

                if ($compare_time) {
                    /* user still dont register an new account */
                    if ($transaction->getReceiverCustomerId() == 0) {
                        $this->updateTransaction(
                            $transaction,
                            Magestore_TruGiftCard_Model_Status::STATUS_TRANSACTION_CANCELLED,
                            abs($transaction->getChangedCredit())
                        );

                        $rewardAccount = Mage::helper('trugiftcard/account')->loadByCustomerId($transaction->getCustomerId());
                        $rewardAccount->setTrugiftcardCredit($rewardAccount->getTrugiftcardCredit() + abs($transaction->getChangedCredit()));
                        $rewardAccount->save();
                    } /* User created an new account and check the amount of truGiftCard in order to return back */
                    else {
                        $orders = $this->getCollectionOrderByCustomer(
                            $transaction->getReceiverCustomerId(),
                            $expiration_date,
                            $transaction
                        );

                        $truGiftCard_used = 0;
                        $order_filter_ids = array();
                        if ($orders != null && sizeof($orders) > 0) {
                            foreach ($orders as $order) {
                                $truGiftCard_used += $order->getTrugiftcardDiscount();
                                $order_filter_ids[] = $order->getEntityId();
                            }
                        }

                        if ($truGiftCard_used >= abs($transaction->getChangedCredit())) {
                            $this->updateTransaction(
                                $transaction,
                                Magestore_TruGiftCard_Model_Status::STATUS_TRANSACTION_COMPLETED,
                                $order_filter_ids
                            );
                        } else {
                            $return_points = abs($transaction->getChangedCredit()) - $truGiftCard_used;
                            $rewardAccount = Mage::helper('trugiftcard/account')->loadByCustomerId($transaction->getCustomerId());
                            $rewardAccount->setTrugiftcardCredit($rewardAccount->getTrugiftcardCredit() + abs($return_points));
                            $rewardAccount->save();

                            $receiveAccount = Mage::helper('trugiftcard/account')->loadByCustomerId($transaction->getReceiverCustomerId());
                            $receiveAccount->setTrugiftcardCredit($receiveAccount->getTrugiftcardCredit() - abs($return_points));
                            $receiveAccount->save();

                            $this->updateTransaction(
                                $transaction,
                                Magestore_TruGiftCard_Model_Status::STATUS_TRANSACTION_COMPLETED,
                                $order_filter_ids,
                                $return_points
                            );
                        }
                    }
                }
            }
        }
    }

    public function updateTransaction($transaction, $type, $order_filter_ids, $point_back = 0)
    {
        $transaction->setUpdatedTime(now());
        $transaction->setStatus($type);
        $transaction->setPointBack($point_back);
        $transaction->setOrderFilterIds(json_encode($order_filter_ids));
        $transaction->save();

        $receive_transaction = Mage::getModel('trugiftcard/transaction')->load($transaction->getRecipientTransactionId());
        if ($receive_transaction != null && $receive_transaction->getId()) {
            $receive_transaction->setUpdatedTime(now());
            $receive_transaction->setStatus($type);
            $receive_transaction->setPointBack($point_back);
            $receive_transaction->setOrderFilterIds(json_encode($order_filter_ids));
            $receive_transaction->save();
        }
    }

    public function getCollectionOrderByCustomer($customer_id, $expiration_date, $transaction)
    {
        $orders = Mage::getModel('sales/order')->getCollection()
            ->addFieldToFilter('customer_id', $customer_id)
            ->addFieldToFilter('state', array('in' => Mage::getSingleton('sales/order_config')->getVisibleOnFrontStates()))
            ->addFieldToFilter('status', array('in' => array(
                Mage_Sales_Model_Order::STATE_COMPLETE,
                Mage_Sales_Model_Order::STATE_PROCESSING
            )))
            ->addFieldToFilter('created_at', array('from' => date('Y-m-d 00:00:00', $expiration_date), 'to' => date('Y-m-d 23:59:59', strtotime(now()))))
            ->setOrder('created_at', 'desc');

        $order_filter_ids = $this->getOrderFilterIdsFromTransaction($transaction);
        if(is_array($order_filter_ids) && sizeof($order_filter_ids) > 0)
            $orders->addFieldToFilter('entity_id', array('nin' => $order_filter_ids));

        if ($orders != null && sizeof($orders) > 0)
            return $orders;
        else
            return null;
    }

    public function getOrderFilterIdsFromTransaction($transaction)
    {
        $collection = Mage::getModel('trugiftcard/transaction')->getCollection()
            ->addFieldToFilter('customer_id', $transaction->getReceiverCustomerId())
            ->addFieldToFilter('action_type', Magestore_TruGiftCard_Model_Type::TYPE_TRANSACTION_RECEIVE_FROM_SHARING)
            ;
        $result = array();
        if(sizeof($collection) > 0)
        {
            foreach ($collection as $tran) {
                if($tran->getOrderFilterIds() != null)
                {
                    $result = array_merge($result, json_decode($tran->getOrderFilterIds(), true));
                }
            }
        }

        return array_unique($result);
    }

    public function compareExpireDate($start_time, $end_time)
    {
        $sub = $end_time - $start_time;

        if ($sub < 0)
            return false;

        $diff = abs($sub);

        $years = floor($diff / (365 * 60 * 60 * 24));
        $months = floor(($diff - $years * 365 * 60 * 60 * 24) / (30 * 60 * 60 * 24));
        $days = floor(($diff - $years * 365 * 60 * 60 * 24 - $months * 30 * 60 * 60 * 24) / (60 * 60 * 24));
        $hours = floor(($diff - $years * 365 * 60 * 60 * 24 - $months * 30 * 60 * 60 * 24 - $days * 60 * 60 * 24) / (60 * 60));
        $minutes = floor(($diff - $years * 365 * 60 * 60 * 24 - $months * 30 * 60 * 60 * 24 - $days * 60 * 60 * 24 - $hours * 60 * 60) / 60);
        $seconds = floor(($diff - $years * 365 * 60 * 60 * 24 - $months * 30 * 60 * 60 * 24 - $days * 60 * 60 * 24 - $hours * 60 * 60 - $minutes * 60));

        if ($years > 0 || $months > 0) {
            return false;
        } else {
            if($days > 0)
                return false;

            if($hours > 0)
                return false;

            return true;
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

        $email_path = Mage::getStoreConfig(Magestore_TruGiftCard_Model_Transaction::XML_PATH_EMAIL_SHARE_EMAIL_EXPIRY_DATE, $store);

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
                Mage::getStoreConfig(Magestore_TruGiftCard_Model_Transaction::XML_PATH_EMAIL_SENDER, $store),
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
                $type_data = Magestore_TruGiftCard_Model_Type::getDataType();
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
                            $account = Mage::helper('trugiftcard/account')->loadByCustomerId($customer->getId());
                            if ($account != null && $account->getId()) {
                                $_truGiftCard_before = $account->getTrugiftcardCredit();
                                $_truGiftCard_new = $_truGiftCard_before + $amount;
                                $account->setTrugiftcardCredit($_truGiftCard_new)
                                    ->setUpdatedTime(now())
                                    ->save();
                                $obj = Mage::getModel('trugiftcard/transaction');
                                $_data = array(
                                    'trugiftcard_id' => $account->getId(),
                                    'customer_id' => $customer->getId(),
                                    'customer_email' => $customer->getEmail(),
                                    'title' => isset($csv[4]) ? $csv[4] : '',
                                    'action_type' => isset($csv[3]) ? $csv[3] : '',
                                    'store_id' => Mage::app()->getStore()->getId(),
                                    'created_time' => now(),
                                    'updated_time' => now(),
                                    'current_credit' => $_truGiftCard_new,
                                    'changed_credit' => $amount,
                                    'status' => Magestore_TruGiftCard_Model_Status::STATUS_TRANSACTION_COMPLETED,
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
        $collection = Mage::getModel('trugiftcard/transaction')->getCollection()
            ->addFieldToFilter('customer_id', $customer_id)
            ->addFieldToFilter('order_id', $order_id)
            ->addFieldToFilter('action_type', Magestore_TruGiftCard_Model_Type::TYPE_TRANSACTION_PURCHASE_GIFT_CARD)
            ->setOrder('transaction_id', 'desc')
            ->getFirstItem();

        if ($collection->getId())
            return true;
        else
            return false;
    }

    public function addTruGiftCardFromProduct($order)
    {
        $helper = Mage::helper('trugiftcard');
        if (!$helper->isEnableTruGiftCardProduct())
            return $this;

        $order_status_configure = $helper->getTruGiftCardOrderStatus();
        $product_configure = $helper->getTruGiftCardSku();
        $value_configure = $helper->getTruGiftCardValue();

        if ($order_status_configure == '')
            return $this;

        $customer = Mage::getModel('customer/customer')->load($order->getCustomerId());
        $flag = $this->checkAddedTransaction($order->getEntityId(), $customer->getId());

        $items = $order->getAllItems();
        $is_only_virtual = 0;
        foreach ($items as $item) {
            $product = Mage::getModel('catalog/product')->load($item->getProductId());
            if ($product->getTypeId() != 'virtual') {
                $is_only_virtual++;
            }
        }

        if ((strcasecmp($order->getStatus(), $order_status_configure) == 0 || (strcasecmp($order->getStatus(), 'complete') == 0 && $is_only_virtual == 0)) && !$flag) {
            $items = $order->getAllItems();
            try {
                foreach ($items as $orderItem) {
                    if (strcasecmp($orderItem->getSku(), $product_configure) == 0) {
                        $credit = $value_configure * (int)$orderItem->getQtyOrdered();
                        $receiverAccount = Mage::helper('trugiftcard/account')->updateCredit($customer->getId(), $credit);
                        $params = array(
                            'credit' => $credit,
                            'title' => Mage::helper('trugiftcard')->__('Purchased Trunited Gift Card on order #<a href="' . Mage::getUrl('sales/order/view', array('order_id' => $order->getEntityId())) . '">' . $order->getIncrementId() . '</a>'),
                        );
                        $this->createTransaction(
                            $receiverAccount,
                            $params,
                            Magestore_TruGiftCard_Model_Type::TYPE_TRANSACTION_PURCHASE_GIFT_CARD,  // type
                            Magestore_TruGiftCard_Model_Status::STATUS_TRANSACTION_COMPLETED
                        );
                    }
                }
            } catch (Exception $ex) {

            }
        }
    }

    public function addDaysToDate($date, $days, $operator = '+')
    {
        $date = strtotime($operator . " " . $days . " days", strtotime($date));
        return date("Y-m-d H:i:s", $date);
    }

}