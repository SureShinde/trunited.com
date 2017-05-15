<?php
/**
 * Magestore
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magestore.com license that is
 * available through the world-wide-web at this URL:
 * http://www.magestore.com/license-agreement.html
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Magestore
 * @package     Magestore_RewardPoints
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * RewardPoints Name and Image Helper
 *
 * @category    Magestore
 * @package     Magestore_RewardPoints
 * @author      Magestore Developer
 */
class Magestore_RewardPoints_Helper_Transaction extends Mage_Core_Helper_Abstract
{
    public function getReceiveCreditTransaction($email)
    {
        $collection = Mage::getModel('rewardpoints/transaction')->getCollection()
            ->addFieldToFilter('receiver_email', $email)
            ->addFieldToFilter('action_type', Magestore_RewardPoints_Model_Transaction::ACTION_TYPE_SHARE)
            ->addFieldToFilter('status', Magestore_RewardPoints_Model_Transaction::STATUS_PENDING)
            ->setOrder('transaction_id', 'desc');

        if (sizeof($collection) > 0)
            return $collection;
        else
            return null;
    }

    public function processTransaction($customer)
    {
        $collection = $this->getReceiveCreditTransaction($customer->getEmail());
        if ($collection != null && sizeof($collection) > 0) {
            foreach ($collection as $transaction) {
                $receiveObject = new Varien_Object();
                $receiveObject->setData('product_credit', abs($transaction->getProductCredit()));
                $receiveObject->setData('point_amount', 0);
                $receiveObject->setData('customer_exist', true);
                $receiveObject->setData('email', $transaction->getCustomerEmail());
                Mage::helper('rewardpoints/action')
                    ->addTransaction(
                        'receive_credit', $customer, $receiveObject
                    );

                $transaction->setStatus(Magestore_RewardPoints_Model_Transaction::STATUS_COMPLETED);
                $transaction->setUpdatedTime(now());
                $transaction->save();
            }
        }
    }

    public function checkExpiryDateTransaction()
    {
        $collection = Mage::getModel('rewardpoints/transaction')->getCollection()
            ->addFieldToFilter('action_type', Magestore_RewardPoints_Model_Transaction::ACTION_TYPE_SHARE)
            ->addFieldToFilter('status', Magestore_RewardPoints_Model_Transaction::STATUS_PENDING)
            ->setOrder('transaction_id', 'desc');

        if (sizeof($collection) > 0) {
            $timestamp = Mage::getModel('core/date')->timestamp(time());
            $expiry_date = Mage::getStoreConfig('rewardpoints/sharewallet/expire_date', Mage::app()->getStore()->getId());
            foreach ($collection as $transaction) {
                $updated_time = strtotime($transaction->getUpdatedTime());
                $compare_time = $this->compareTime($updated_time, $timestamp);
                if ($compare_time > $expiry_date) {
                    $transaction->setUpdatedTime(now());
                    $transaction->setStatus(Magestore_RewardPoints_Model_Transaction::STATUS_CANCELED);
                    $transaction->save();

                    $rewardAccount = Mage::helper('rewardpoints/customer')->getAccountByCustomerId($transaction->getCustomerId());
                    $rewardAccount->setProductCredit($rewardAccount->getProductCredit() + abs($transaction->getProductCredit()));
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
        if (!$customer->getId())
            return $this;

        $email_path = Mage::getStoreConfig(Magestore_RewardPoints_Model_Transaction::XML_PATH_EMAIL_SHARE_EMAIL_EXPIRY_DATE, $store);


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

    public function getDaysOfHold()
    {
        return Mage::getStoreConfig('rewardpoints/general/day_hold_point', Mage::app()->getStore());
    }

    public function getOnHoldTransaction()
    {
        $collection = Mage::getModel('rewardpoints/transaction')->getCollection()
            ->addFieldToFilter('action_type', Magestore_RewardPoints_Model_Transaction::ACTION_TYPE_BOTH)
            ->addFieldToFilter('action', 'admin')
            ->addFieldToFilter('status', Magestore_RewardPoints_Model_Transaction::STATUS_ON_HOLD)
            ->addFieldToFilter('is_on_hold', 1)
            ->addFieldToFilter('customer_id', Mage::getSingleton('customer/session')->getCustomer()->getId())
            ->setOrder('transaction_id', 'desc');

        return $collection;
    }

    public function isShowOnHold()
    {
        $collection = $this->getOnHoldTransaction();
        $days_on_hold = $this->getDaysOfHold();
        if (sizeof($collection) > 0 && $days_on_hold > 0) {
            $transaction = $collection->getFirstItem();
            if ($transaction->getId()) {
                $date = $this->addDaysToDate(
                    $transaction->getCreatedTime(),
                    $days_on_hold
                );

                if (strtotime($date) >= time())
                    return true;
                else
                    return false;
            } else
                return false;
        } else
            return false;
    }

    public function addDaysToDate($date, $days, $operator = '+')
    {
        $date = strtotime($operator." " . $days . " days", strtotime($date));
        return date("Y-m-d H:i:s", $date);
    }

    public function getDataOnHold()
    {
        $collection = $this->getOnHoldTransaction();
        $days_on_hold = $this->getDaysOfHold();
        $data = array();

        $number_display = 0;
        if (sizeof($collection) > 0 && $days_on_hold > 0) {
            $t = time();
            foreach ($collection as $tran) {
                $date = $this->addDaysToDate(
                    $tran->getCreatedTime(),
                    $days_on_hold
                );

                if (strtotime($date) >= $t) {
                    $dur = (int)(date('m',strtotime($date)) - date('m',$t));
                    $number_display = $dur;
                    if($dur > 1)
                    {
                        $_days = (int) ($days_on_hold / $dur);

                        $flag = '';
                        for ($i = 1; $i < $dur; $i++)
                        {
                            if($flag == '')
                                $_date = $this->addDaysToDate($date, $_days, '-');
                            else
                                $_date = $this->addDaysToDate($flag, $_days, '-');

                            $data = $this->addDateToDate($data, $_date, 0);
                            $flag = $_date;
                        }
                        $data = $this->addDateToDate($data, $date, $tran->getHoldPoint());
                    } else {
                        $data = $this->addDateToDate($data, $date, $tran->getHoldPoint());
                    }
                }
            }
        }

        ksort($data);

        if($number_display > 0)
        {
            $size = sizeof($data);
            $count = 0;
            foreach ($data as $k => $v)
            {
                if($count < ($size - $number_display))
                {
                    unset($data[$k]);
                }

                $count++;
            }
        }

        return $data;
    }

    public function addDateToDate($data, $date, $amount)
    {
        if(sizeof($data) == 0)
        {
            $data[strtotime($date)] = $amount;
        } else {
            $flag = false;
            foreach ($data as $k=>$v)
            {
                if(date('m',$k) == date('m',strtotime($date)) &&
                    date('Y',$k) == date('Y',strtotime($date)))
                {
                    $data[$k] = $v + $amount;
                    $flag = true;
                }
            }

            if(!$flag)
                $data[strtotime($date)] = $amount;
        }

        return $data;
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
                $type_data = array(
                    Magestore_RewardPoints_Model_Transaction::STATUS_COMPLETED,
                    Magestore_RewardPoints_Model_Transaction::STATUS_ON_HOLD
                );
                $line = 0;
                foreach ($csvData as $csv) {
                    $amount = str_replace($current_symbol, '', $csv[2]);

                    if (isset($csv[0]) && !filter_var($csv[0], FILTER_VALIDATE_INT) === false
                        && isset($csv[1]) && !filter_var($csv[1], FILTER_VALIDATE_EMAIL) === false
                        && isset($csv[4]) && !filter_var($csv[4], FILTER_VALIDATE_INT) === false
                        && isset($amount) && !filter_var($amount, FILTER_VALIDATE_FLOAT) === false
                        && in_array($csv[4], $type_data) && $line > 0
                    ) {
                        $customer = Mage::getModel('customer/customer')->load($csv[0]);

                        if ($customer->getId() && strcasecmp($customer->getEmail(), $csv[1]) == 0) {
                            $account = Mage::helper('rewardpoints/customer')->loadByCustomerId($customer->getId());
                            if ($account != null && $account->getId()) {
//                                $_point_before = $account->getPointBalance();
//                                $_point_new = $_point_before + $amount;
                                $status = isset($csv[4]) ? $csv[4] : '';
                                $is_on_hold = false;

                                /*if($status == Magestore_RewardPoints_Model_Transaction::STATUS_COMPLETED)
                                {
                                    $account->setPointBalance($_point_new)
                                        ->setUpdatedTime(now())
                                        ->save();
                                } else if($status == Magestore_RewardPoints_Model_Transaction::STATUS_ON_HOLD) {
                                    $is_on_hold = true;
                                } else {
                                    throw new Exception(
                                        Mage::helper('rewardpoints')->__('Status is not valid !')
                                    );
                                }*/

                                if($status == Magestore_RewardPoints_Model_Transaction::STATUS_ON_HOLD) {
                                    $is_on_hold = true;
                                }

                                Mage::helper('rewardpoints/action')->addTransaction('admin', $customer, new Varien_Object(array(
                                        'point_amount' => $amount,
                                        'title' => isset($csv[3]) ? $csv[3] : '',
                                        'is_on_hold' => $is_on_hold,
                                        'status' => $status
                                    ))
                                );

                               /* $obj = Mage::getModel('rewardpoints/transaction');
                                $_data = array(
                                    'reward_id' => $account->getId(),
                                    'customer_id' => $customer->getId(),
                                    'customer_email' => $customer->getEmail(),
                                    'title' => isset($csv[3]) ? $csv[3] : '',
                                    'action' => 'admin',
                                    'action_type' => 0,
                                    'store_id' => Mage::app()->getStore()->getId(),
                                    'created_time' => now(),
                                    'updated_time' => now(),
                                    'point_amount' => $amount,
                                    'real_point' => $amount,
                                    'status' => $status,
                                    'is_on_hold' => $is_on_hold,
                                    'hold_point' => $is_on_hold == true ? $amount : 0,
                                );*/
//                                $obj->setData($_data);
//                                $transactionSave->addObject($obj);
                                $import_count++;
                            }
                        }
                    }
                    $line++;
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
}
