<?php

class Magestore_TruGiftCard_Helper_Data extends Mage_Core_Helper_Abstract
{
    const XML_PATH_ENABLE = 'trugiftcard/general/enable';

    public function isEnable($store = null)
    {
        return Mage::getStoreConfigFlag(self::XML_PATH_ENABLE, $store);
    }

    public function isEnableModule(){
        return Mage::helper('core')->isModuleOutputEnabled('Magestore_TruGiftCard');
    }

    public function getMyTruGiftCardLabel()
    {
        $image = '<img src="'.Mage::getDesign()->getSkinUrl('images/trugiftcard/point.png').'" />';
        return $this->__('My Trunited Gift Card') . ' ' . $image;
    }

    public function getShareTruGiftCardLabel()
    {
        $image = '<img src="'.Mage::getDesign()->getSkinUrl('images/trugiftcard/point.png').'" />';
        return $this->__('Share Trunited Gift Card Money') . ' ' . $image;
    }

    public function formatTruwallet($credit)
    {
        return Mage::helper('core')->currency($credit, true, false);
    }

    public function getSpendConfig($code, $store = null)
    {
        return Mage::getStoreConfig('trugiftcard/spending/' . $code, $store);
    }

    public function getWarningMessage($store = null)
    {
        return Mage::getStoreConfig('trugiftcard/general/warning_message', $store);
    }

    public function isEnableTruGiftCardProduct($store = null)
    {
        return Mage::getStoreConfig('trugiftcard/product/enable', $store);
    }

    public function getTruGiftCardOrderStatus($store = null)
    {
        return Mage::getStoreConfig('trugiftcard/product/order_status', $store);
    }

    public function getTruGiftCardSku($store = null)
    {
        return Mage::getStoreConfig('trugiftcard/product/trugiftcard_sku', $store);
    }

    public function getTruGiftCardValue($store = null)
    {
        return Mage::getStoreConfig('trugiftcard/product/trugiftcard_value', $store);
    }

    public function getEnableChangeBalance($store = null)
    {
        return Mage::getStoreConfig('trugiftcard/general/enable_change_balance', $store);
    }

    public function getTruGiftCardPaymentEnable($store = null)
    {
        return Mage::getStoreConfig('trugiftcard/trugiftcard_payment/enable', $store);
    }

    public function getTruGiftCardPayment($store = null)
    {
        return Mage::getStoreConfig('trugiftcard/trugiftcard_payment/payment', $store);
    }

    public function getTruGiftCardOrderAmount($store = null)
    {
        return Mage::getStoreConfig('trugiftcard/trugiftcard_payment/order_amount', $store);
    }

    public function getTruGiftCardPaymentPoint($store = null)
    {
        return Mage::getStoreConfig('trugiftcard/trugiftcard_payment/reward_point', $store);
    }

    public function getEnableTransferBonus($store = null)
    {
        return Mage::getStoreConfig('trugiftcard/transfer/enable', $store);
    }

    public function getTransferBonus($store = null)
    {
        return Mage::getStoreConfig('trugiftcard/transfer/bonus', $store);
    }

    public function getMessageTransferBonus($store = null)
    {
        return Mage::getStoreConfig('trugiftcard/transfer/message', $store);
    }


    public function isShowWarningMessage()
    {
        if(Mage::helper('core')->isModuleOutputEnabled('Magestore_TruBox'))
        {
            $truBoxCollection = Mage::helper('trubox')->getCurrentTruBoxCollection();
            if(sizeof($truBoxCollection) <= 0)
                return false;

            $totalPrice = 0;
            foreach ($truBoxCollection as $item) {
                $product = Mage::getModel('catalog/product')->load($item->getProductId());
                $option_params = json_decode($item->getOptionParams(), true);
                $price_options = 0;

                if($product->getTypeId() != 'configurable')
                {
                    foreach ($product->getOptions() as $o)
                    {
                        $values = $o->getValues();
                        $_attribute_value = 0;

                        foreach($option_params as $k=>$v)
                        {
                            if($k == $o->getOptionId())
                            {
                                $_attribute_value = $v;
                                break;
                            }
                        }
                        if($_attribute_value > 0)
                        {
                            foreach ($values as $val) {
                                if(is_array($_attribute_value)){
                                    if(in_array($val->getOptionTypeId(), $_attribute_value)) {
                                        echo $val->getTitle().' ';
                                        $price_options += $val->getPrice();

                                    }
                                } else if($val->getOptionTypeId() == $_attribute_value){
                                    echo $val->getTitle().' ';
                                    $price_options += $val->getPrice();
                                }
                            }
                        }
                    }
                }

                $itemPrice = ($product->getFinalPrice() + $price_options) * $item->getQty();
                $totalPrice += $itemPrice;
            }

            if($totalPrice == 0)
                return false;

            $current_truGiftCard_balance = Mage::helper('trugiftcard/account')->getTruGiftCardCredit(false);
            if($current_truGiftCard_balance == null)
                return false;

            if($current_truGiftCard_balance < $totalPrice)
                return true;
            else
                return false;
        } else {
            return false;
        }


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
                    $truGiftCard_customer = Mage::getModel('trugiftcard/customer');
                    $truGiftCard_customer->setData('customer_id', $reward->getCustomerId());
                    $truGiftCard_customer->setData('trugiftcard_credit', $reward->getProductCredit());
                    $truGiftCard_customer->setData('created_time', now());
                    $truGiftCard_customer->setData('updated_time', now());
                    $transactionSave->addObject($truGiftCard_customer);
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
                    $trugiftcard_account = Mage::helper('trugiftcard/account')->loadByCustomerId($transaction->getCustomerId());
                    if($transaction->getCustomerId() != null && $trugiftcard_account != null)
                    {
                        $truGiftCard_transaction = Mage::getModel('trugiftcard/transaction');
                        $_data = array();
                        $_data['trugiftcard_id'] = $trugiftcard_account->getId();
                        $_data['customer_id'] = $transaction->getCustomerId();
                        $_data['customer_email'] = $transaction->getCustomerEmail();
                        $_data['title'] = $transaction->getTitle();
                        $_data['store_id'] = $transaction->getStoreId();

                        switch($transaction->getStatus())
                        {
                            case 3:
                                $_data['status'] = Magestore_TruGiftCard_Model_Status::STATUS_TRANSACTION_COMPLETED;
                                break;

                            case 4:
                                $_data['status'] = Magestore_TruGiftCard_Model_Status::STATUS_TRANSACTION_CANCELLED;
                                break;

                            case 1:
                                $_data['status'] = Magestore_TruGiftCard_Model_Status::STATUS_TRANSACTION_PENDING;
                                break;

                            case 2:
                                $_data['status'] = Magestore_TruGiftCard_Model_Status::STATUS_TRANSACTION_ON_HOLD;
                                break;

                            case 5:
                                $_data['status'] = Magestore_TruGiftCard_Model_Status::STATUS_TRANSACTION_EXPIRED;
                                break;
                        }

                        switch($transaction->getActionType())
                        {
                            case 0:
                                $_data['action_type'] = Magestore_TruGiftCard_Model_Type::TYPE_TRANSACTION_BY_ADMIN;
                                break;

                            case 3:
                                $_data['action_type'] = Magestore_TruGiftCard_Model_Type::TYPE_TRANSACTION_TRANSFER;
                                break;

                            case 4:
                                $_data['action_type'] = Magestore_TruGiftCard_Model_Type::TYPE_TRANSACTION_SHARING;
                                break;

                            case 5:
                                $_data['action_type'] = Magestore_TruGiftCard_Model_Type::TYPE_TRANSACTION_RECEIVE_FROM_SHARING;
                                break;
                        }

                        $_data['created_time'] = $transaction->getCreatedTime();
                        $_data['updated_time'] = $transaction->getUpdatedTime();
                        $_data['expiration_date'] = $transaction->getExpirationDate();
                        $_data['order_id'] = $transaction->getOrderId() == 0 ? null : $transaction->getOrderId();
                        $_data['current_credit'] = $trugiftcard_account->getTrugiftcardCredit();
                        $_data['changed_credit'] = $transaction->getProductCredit();
                        $_data['receiver_email'] = $transaction->getReceiverEmail();
                        $_data['receiver_customer_id'] = $transaction->getReceiverCustomerId();

                        $truGiftCard_transaction->setData($_data);
                        $transactionSave->addObject($truGiftCard_transaction);
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

    public function synchFeb()
    {
        $reward_transactions = Mage::getModel('rewardpoints/transaction')->getCollection()
            ->addFieldToFilter('action_type',Magestore_RewardPoints_Model_Transaction::ACTION_TYPE_SPEND)
            ->addFieldToFilter('action',array('like'=>'%spending_order%'))
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
                    $trugiftcard_account = Mage::helper('trugiftcard/account')->updateCredit($transaction->getCustomerId(), $transaction->getProductCredit());
                    if($transaction->getCustomerId() != null && $trugiftcard_account != null)
                    {
                        $truGiftCard_transaction = Mage::getModel('trugiftcard/transaction');
                        $_data = array();
                        $_data['trugiftcard_id'] = $trugiftcard_account->getId();
                        $_data['customer_id'] = $transaction->getCustomerId();
                        $_data['customer_email'] = $transaction->getCustomerEmail();
                        $_data['title'] = $transaction->getTitle();
                        $_data['store_id'] = $transaction->getStoreId();

                        switch($transaction->getStatus())
                        {
                            case 3:
                                $_data['status'] = Magestore_TruGiftCard_Model_Status::STATUS_TRANSACTION_COMPLETED;
                                break;

                            case 4:
                                $_data['status'] = Magestore_TruGiftCard_Model_Status::STATUS_TRANSACTION_CANCELLED;
                                break;

                            case 1:
                                $_data['status'] = Magestore_TruGiftCard_Model_Status::STATUS_TRANSACTION_PENDING;
                                break;

                            case 2:
                                $_data['status'] = Magestore_TruGiftCard_Model_Status::STATUS_TRANSACTION_ON_HOLD;
                                break;

                            case 5:
                                $_data['status'] = Magestore_TruGiftCard_Model_Status::STATUS_TRANSACTION_EXPIRED;
                                break;
                        }

                        $_data['action_type'] = Magestore_TruGiftCard_Model_Type::TYPE_TRANSACTION_CHECKOUT_ORDER;

                        $_data['created_time'] = $transaction->getCreatedTime();
                        $_data['updated_time'] = $transaction->getUpdatedTime();
                        $_data['expiration_date'] = $transaction->getExpirationDate();
                        $_data['order_id'] = $transaction->getOrderId() == 0 ? null : $transaction->getOrderId();
                        $_data['current_credit'] = $trugiftcard_account->getTrugiftcardCredit();
                        $_data['changed_credit'] = $transaction->getProductCredit();
                        $_data['receiver_email'] = $transaction->getReceiverEmail();
                        $_data['receiver_customer_id'] = $transaction->getReceiverCustomerId();

                        $truGiftCard_transaction->setData($_data);
                        $transactionSave->addObject($truGiftCard_transaction);
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

    public function synchGiftCard()
    {
        $reward_transactions = Mage::getModel('rewardpoints/transaction')->getCollection()
            ->addFieldToFilter('action_type', 8)
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
                    $trugiftcard_account = Mage::helper('trugiftcard/account')->loadByCustomerId($transaction->getCustomerId());
                    if($transaction->getCustomerId() != null && $trugiftcard_account != null)
                    {
                        $truGiftCard_transaction = Mage::getModel('trugiftcard/transaction');
                        $_data = array();
                        $_data['trugiftcard_id'] = $trugiftcard_account->getId();
                        $_data['customer_id'] = $transaction->getCustomerId();
                        $_data['customer_email'] = $transaction->getCustomerEmail();
                        $_data['title'] = $transaction->getTitle();
                        $_data['store_id'] = $transaction->getStoreId();

                        switch($transaction->getStatus())
                        {
                            case 3:
                                $_data['status'] = Magestore_TruGiftCard_Model_Status::STATUS_TRANSACTION_COMPLETED;
                                break;

                            case 4:
                                $_data['status'] = Magestore_TruGiftCard_Model_Status::STATUS_TRANSACTION_CANCELLED;
                                break;

                            case 1:
                                $_data['status'] = Magestore_TruGiftCard_Model_Status::STATUS_TRANSACTION_PENDING;
                                break;

                            case 2:
                                $_data['status'] = Magestore_TruGiftCard_Model_Status::STATUS_TRANSACTION_ON_HOLD;
                                break;

                            case 5:
                                $_data['status'] = Magestore_TruGiftCard_Model_Status::STATUS_TRANSACTION_EXPIRED;
                                break;
                        }

                        $_data['action_type'] = Magestore_TruGiftCard_Model_Type::TYPE_TRANSACTION_PURCHASE_GIFT_CARD;

                        $_data['created_time'] = $transaction->getCreatedTime();
                        $_data['updated_time'] = $transaction->getUpdatedTime();
                        $_data['expiration_date'] = $transaction->getExpirationDate();
                        $_data['order_id'] = $transaction->getOrderId() == 0 ? null : $transaction->getOrderId();
                        $_data['current_credit'] = $trugiftcard_account->getTrugiftcardCredit();
                        $_data['changed_credit'] = $transaction->getProductCredit();
                        $_data['receiver_email'] = $transaction->getReceiverEmail();
                        $_data['receiver_customer_id'] = $transaction->getReceiverCustomerId();

                        $truGiftCard_transaction->setData($_data);
                        $transactionSave->addObject($truGiftCard_transaction);
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

    public function synchTransfer()
    {
        $reward_transactions = Mage::getModel('rewardpoints/transaction')->getCollection()
            ->addFieldToFilter('action_type',3)
            ->addFieldToFilter('customer_id',array('gt' => 0))
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
                    $trugiftcard_account = Mage::helper('trugiftcard/account')->updateCredit($transaction->getCustomerId(), $transaction->getProductCredit());
                    if($transaction->getCustomerId() != null && $trugiftcard_account != null)
                    {
                        $truGiftCard_transaction = Mage::getModel('trugiftcard/transaction');
                        $_data = array();
                        $_data['trugiftcard_id'] = $trugiftcard_account->getId();
                        $_data['customer_id'] = $transaction->getCustomerId();
                        $_data['customer_email'] = $transaction->getCustomerEmail();
                        $_data['title'] = $transaction->getTitle();
                        $_data['store_id'] = $transaction->getStoreId();

                        switch($transaction->getStatus())
                        {
                            case 3:
                                $_data['status'] = Magestore_TruGiftCard_Model_Status::STATUS_TRANSACTION_COMPLETED;
                                break;

                            case 4:
                                $_data['status'] = Magestore_TruGiftCard_Model_Status::STATUS_TRANSACTION_CANCELLED;
                                break;

                            case 1:
                                $_data['status'] = Magestore_TruGiftCard_Model_Status::STATUS_TRANSACTION_PENDING;
                                break;

                            case 2:
                                $_data['status'] = Magestore_TruGiftCard_Model_Status::STATUS_TRANSACTION_ON_HOLD;
                                break;

                            case 5:
                                $_data['status'] = Magestore_TruGiftCard_Model_Status::STATUS_TRANSACTION_EXPIRED;
                                break;
                        }

                        $_data['action_type'] = Magestore_TruGiftCard_Model_Type::TYPE_TRANSACTION_TRANSFER;

                        $_data['created_time'] = $transaction->getCreatedTime();
                        $_data['updated_time'] = $transaction->getUpdatedTime();
                        $_data['expiration_date'] = $transaction->getExpirationDate();
                        $_data['order_id'] = $transaction->getOrderId() == 0 ? null : $transaction->getOrderId();
                        $_data['current_credit'] = $trugiftcard_account->getTrugiftcardCredit();
                        $_data['changed_credit'] = $transaction->getProductCredit();
                        $_data['receiver_email'] = $transaction->getReceiverEmail();
                        $_data['receiver_customer_id'] = $transaction->getReceiverCustomerId();

                        $truGiftCard_transaction->setData($_data);
                        $transactionSave->addObject($truGiftCard_transaction);
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

     public function synchPurchaseGiftCard()
    {
        $reward_transactions = Mage::getModel('rewardpoints/transaction')->getCollection()
            ->addFieldToFilter('action_type',8)
            ->addFieldToFilter('customer_id',array('gt' => 0))
            ->addFieldToFilter('transaction_id',array('in' => array(8902,9245,10648)))
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
                    $trugiftcard_account = Mage::helper('trugiftcard/account')->updateCredit($transaction->getCustomerId(), $transaction->getProductCredit());
                    if($transaction->getCustomerId() != null && $trugiftcard_account != null)
                    {
                        $truGiftCard_transaction = Mage::getModel('trugiftcard/transaction');
                        $_data = array();
                        $_data['trugiftcard_id'] = $trugiftcard_account->getId();
                        $_data['customer_id'] = $transaction->getCustomerId();
                        $_data['customer_email'] = $transaction->getCustomerEmail();
                        $_data['title'] = $this->__('Purchased truGiftCard Gift Card on order #<a href="'.Mage::getUrl('sales/order/view',array('order_id'=>$transaction->getOrderId())).'">'.$transaction->getOrderIncrementId().'</a>');
                        $_data['store_id'] = $transaction->getStoreId();

                        switch($transaction->getStatus())
                        {
                            case 3:
                                $_data['status'] = Magestore_TruGiftCard_Model_Status::STATUS_TRANSACTION_COMPLETED;
                                break;

                            case 4:
                                $_data['status'] = Magestore_TruGiftCard_Model_Status::STATUS_TRANSACTION_CANCELLED;
                                break;

                            case 1:
                                $_data['status'] = Magestore_TruGiftCard_Model_Status::STATUS_TRANSACTION_PENDING;
                                break;

                            case 2:
                                $_data['status'] = Magestore_TruGiftCard_Model_Status::STATUS_TRANSACTION_ON_HOLD;
                                break;

                            case 5:
                                $_data['status'] = Magestore_TruGiftCard_Model_Status::STATUS_TRANSACTION_EXPIRED;
                                break;
                        }

                        $_data['action_type'] = Magestore_TruGiftCard_Model_Type::TYPE_TRANSACTION_PURCHASE_GIFT_CARD;

                        $_data['created_time'] = $transaction->getCreatedTime();
                        $_data['updated_time'] = $transaction->getUpdatedTime();
                        $_data['expiration_date'] = $transaction->getExpirationDate();
                        $_data['order_id'] = $transaction->getOrderId() == 0 ? null : $transaction->getOrderId();
                        $_data['current_credit'] = $trugiftcard_account->getTrugiftcardCredit();
                        $_data['changed_credit'] = $transaction->getProductCredit();
                        $_data['receiver_email'] = $transaction->getReceiverEmail();
                        $_data['receiver_customer_id'] = $transaction->getReceiverCustomerId();

                        $truGiftCard_transaction->setData($_data);
                        $transactionSave->addObject($truGiftCard_transaction);
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

    public function synchOrder()
    {
        $orders = Mage::getModel('sales/order')->getCollection()
            ->addAttributeToFilter('created_at',array(
                'from' => '2017-02-09',
                'to'    => '2017-02-11'
            ))
            ->setOrder('entity_id','desc')
            ;

        $data_order = array();
        foreach($orders as $order)
        {
            $items = $order->getAllItems();
            foreach($items as $item)
            {
                if(strcasecmp($item->getSku(),'TWGIFTCARD') == 0)
                {
                    $data_order[] = array(
                        'order' => $order,
                        'qty' => $item->getQtyOrdered(),
                    );
                }
            }
        }
//        zend_debug::dump($data_order);
//        exit;

        if(sizeof($data_order) > 0)
        {
            $transactionSave = Mage::getModel('core/resource_transaction');
            $connection = Mage::getSingleton('core/resource')->getConnection('core_write');
            try {
                $connection->beginTransaction();
                foreach ($data_order as $order)
                {
                    $do = $order['order'];
                    $qty = $order['qty'];
                    $trugiftcard_account = Mage::helper('trugiftcard/account')->loadByCustomerId($do->getCustomerId());
                    $customer = Mage::getModel('customer/customer')->load($do->getCustomerId());
                    if($do->getCustomerId() != null && $trugiftcard_account != null)
                    {
                        $truGiftCard_transaction = Mage::getModel('trugiftcard/transaction');
                        $_data = array();
                        $_data['trugiftcard_id'] = $trugiftcard_account->getId();
                        $_data['customer_id'] = $do->getCustomerId();
                        $_data['customer_email'] = $customer->getEmail();
                        $_data['title'] = $this->__('Purchased truGiftCard Gift Card on order #<a href="'.Mage::getUrl('sales/order/view',array('order_id'=>$do->getEntityId())).'">'.$do->getIncrementId().'</a>');
                        $_data['store_id'] = $do->getStoreId();

                        $_data['status'] = Magestore_TruGiftCard_Model_Status::STATUS_TRANSACTION_COMPLETED;

                        $_data['action_type'] = Magestore_TruGiftCard_Model_Type::TYPE_TRANSACTION_PURCHASE_GIFT_CARD;

                        $_data['created_time'] = $do->getCreatedAt();
                        $_data['updated_time'] = $do->getUpdatedAt();
                        $_data['expiration_date'] = '';
                        $_data['order_id'] = $do->getOrderId();
                        $_data['current_credit'] = $trugiftcard_account->getTrugiftcardCredit();
                        $_data['changed_credit'] = $qty * $this->getTruGiftCardValue();
                        $_data['receiver_email'] = '';
                        $_data['receiver_customer_id'] = '';
                        
                        $truGiftCard_transaction->setData($_data);
                        $transactionSave->addObject($truGiftCard_transaction);
                    }
                }

                $transactionSave->save();
                $connection->commit();
                echo 'success';
            } catch (Exception $e) {
                $connection->rollback();
                zend_debug::dump($e->getMessage());
            }
        }

        /*$reward_transactions = Mage::getModel('rewardpoints/transaction')->getCollection()
            ->addFieldToFilter('action_type', 8)
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
                    $trugiftcard_account = Mage::helper('trugiftcard/account')->loadByCustomerId($transaction->getCustomerId());
                    if($transaction->getCustomerId() != null && $trugiftcard_account != null)
                    {
                        $truGiftCard_transaction = Mage::getModel('trugiftcard/transaction');
                        $_data = array();
                        $_data['trugiftcard_id'] = $trugiftcard_account->getId();
                        $_data['customer_id'] = $transaction->getCustomerId();
                        $_data['customer_email'] = $transaction->getCustomerEmail();
                        $_data['title'] = $transaction->getTitle();
                        $_data['store_id'] = $transaction->getStoreId();

                        switch($transaction->getStatus())
                        {
                            case 3:
                                $_data['status'] = Magestore_TruGiftCard_Model_Status::STATUS_TRANSACTION_COMPLETED;
                                break;

                            case 4:
                                $_data['status'] = Magestore_TruGiftCard_Model_Status::STATUS_TRANSACTION_CANCELLED;
                                break;

                            case 1:
                                $_data['status'] = Magestore_TruGiftCard_Model_Status::STATUS_TRANSACTION_PENDING;
                                break;

                            case 2:
                                $_data['status'] = Magestore_TruGiftCard_Model_Status::STATUS_TRANSACTION_ON_HOLD;
                                break;

                            case 5:
                                $_data['status'] = Magestore_TruGiftCard_Model_Status::STATUS_TRANSACTION_EXPIRED;
                                break;
                        }

                        $_data['action_type'] = Magestore_TruGiftCard_Model_Type::TYPE_TRANSACTION_PURCHASE_GIFT_CARD;

                        $_data['created_time'] = $transaction->getCreatedTime();
                        $_data['updated_time'] = $transaction->getUpdatedTime();
                        $_data['expiration_date'] = $transaction->getExpirationDate();
                        $_data['order_id'] = $transaction->getOrderId() == 0 ? null : $transaction->getOrderId();
                        $_data['current_credit'] = $trugiftcard_account->getTrugiftcardCredit();
                        $_data['changed_credit'] = $transaction->getProductCredit();
                        $_data['receiver_email'] = $transaction->getReceiverEmail();
                        $_data['receiver_customer_id'] = $transaction->getReceiverCustomerId();

                        $truGiftCard_transaction->setData($_data);
                        $transactionSave->addObject($truGiftCard_transaction);
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
        }*/
    }
	
}
