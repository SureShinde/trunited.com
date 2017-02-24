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
 * RewardPoints Index Controller
 *
 * @category    Magestore
 * @package     Magestore_RewardPoints
 * @author      Magestore Developer
 */
class Magestore_TruWallet_TransactionController extends Mage_Core_Controller_Front_Action
{
    public function sendTruWalletAction()
    {
        $amount = $this->getRequest()->getParam('share_amount');
        $message = $this->getRequest()->getParam('message');
        $email = filter_var($this->getRequest()->getParam('share_email'), FILTER_SANITIZE_EMAIL);
        $customer = Mage::getModel('customer/customer')->load(Mage::getSingleton('customer/session')->getCustomerId());
        $transaction_helper = Mage::helper('truwallet/transaction');

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            Mage::getSingleton('core/session')->addError(
                Mage::helper('truwallet')->__($email . ' is not a valid email address')
            );
            $this->_redirectUrl(Mage::getUrl('*/index/shareTruWallet'));
            return;
        }

        if (strcasecmp($email, $customer->getEmail()) == 0) {
            Mage::getSingleton('core/session')->addError(
                Mage::helper('truwallet')->__('You cannot share truWallet balance to your email')
            );
            $this->_redirectUrl(Mage::getUrl('*/index/shareTruWallet'));
            return;
        }

        if (!filter_var($amount, FILTER_VALIDATE_FLOAT)) {
            Mage::getSingleton('core/session')->addError(
                Mage::helper('truwallet')->__($amount . ' is not a valid number')
            );
            $this->_redirectUrl(Mage::getUrl('*/index/shareTruWallet'));
            return;
        }

        $account = Mage::getModel('truwallet/customer')->load($customer->getId(), 'customer_id');
        if ($account->getTruwalletCredit() < 0 || $amount > $account->getTruwalletCredit()) {
            Mage::getSingleton('core/session')->addError(
                Mage::helper('truwallet')->__('Sorry, your balance is less than what you want to share. Please enter a new amount.')
            );
            $this->_redirectUrl(Mage::getUrl('*/index/shareTruWallet'));
            return;
        }

        $customer_receiver = Mage::getModel("customer/customer");
        $customer_receiver->setWebsiteId(Mage::app()->getStore()->getWebsiteId());
        $customer_receiver->loadByEmail($email);
        $is_exist = false;
        if ($customer_receiver->getId())
            $is_exist = true;

        if ($is_exist) {
            $status = Magestore_TruWallet_Model_Status::STATUS_TRANSACTION_COMPLETED;
        } else {
            $status = Magestore_TruWallet_Model_Status::STATUS_TRANSACTION_PENDING;
        }

        $transactionSave = Mage::getModel('core/resource_transaction');
        $connection = Mage::getSingleton('core/resource')->getConnection('core_write');
        try {
            $connection->beginTransaction();

            $truWalletAccount = Mage::helper('truwallet/account')->updateCredit($customer->getId(), -$amount);
            $params = array(
                'credit' => -$amount,
                'title' => '',
                'receiver_email' => $email,
                'receiver_customer_id' => $is_exist ? $customer_receiver->getId() : '',
            );
            if ($truWalletAccount != null) {
                $transaction_helper->createTransaction(
                    $truWalletAccount,
                    $params,
                    Magestore_TruWallet_Model_Type::TYPE_TRANSACTION_SHARING,  // type
                    $status
                );
            }

            if ($is_exist) {
                $receiverAccount = Mage::helper('truwallet/account')->updateCredit($customer_receiver->getId(), $amount);
                $params = array(
                    'credit' => $amount,
                    'title' => '',
                    'receiver_email' => $customer_receiver->getEmail(),
                    'receiver_customer_id' => $customer_receiver->getId(),
                );
                if ($receiverAccount != null) {
                    $transaction_helper->createTransaction(
                        $receiverAccount,
                        $params,
                        Magestore_TruWallet_Model_Type::TYPE_TRANSACTION_RECEIVE_FROM_SHARING,  // type
                        $status
                    );
                }
            }

            $transaction_helper->sendEmailWhenSharingTruWallet(
                $customer->getId(),
                $amount,
                $is_exist,
                $email,
                $message,
                $status
            );

            $transactionSave->save();
            $connection->commit();

        } catch (Exception $e) {
            $connection->rollback();
            Mage::getSingleton('core/session')->addError(
                Mage::helper('truwallet')->__($e->getMessage())
            );
        }

        $money = Mage::helper('core')->currency($amount, true, false);
        Mage::getSingleton('core/session')->addSuccess(
            Mage::helper('truwallet')->__('Congrats! You have just sent <b>' . $money . '</b> to <b>' . $email . '</b> successfully!')
        );
        $this->_redirectUrl(Mage::getUrl('*/index/shareTruWallet'));
    }

    public function cancelTransactionAction()
    {
        $transaction_id = $this->getRequest()->getParam('id');
        $transaction = Mage::getModel('truwallet/transaction')->load($transaction_id);
        $connection = Mage::getSingleton('core/resource')->getConnection('core_write');
        try {
            $connection->beginTransaction();
            if (!$transaction->getId()) {
                throw new Exception(Mage::helper('truwallet')->__('Transaction doesn\'t exist'));
            }

            $transaction->setUpdatedTime(now());
            $transaction->setStatus(Magestore_TruWallet_Model_Status::STATUS_TRANSACTION_CANCELLED);
            $transaction->save();

            $truWalletAccount = Mage::getModel('truwallet/customer')->load($transaction->getTruwalletId());
            if ($truWalletAccount->getId()) {
                $current_credit = $truWalletAccount->getTruwalletCredit();
                $_new_credit = $current_credit + abs($transaction->getChangedCredit());
                $truWalletAccount->setTruwalletCredit($_new_credit);
                $truWalletAccount->save();
            }

            $connection->commit();
            Mage::getSingleton('core/session')->addSuccess(
                Mage::helper('truwallet')->__('Transaction has been cancelled successfully')
            );
        } catch (Exception $ex) {
            $connection->rollback();
            Mage::getSingleton('core/session')->addError(
                $ex->getMessage()
            );
        }

        $this->_redirectUrl(Mage::getUrl('*/index/shareTruWallet'));
    }

    public function registerAction()
    {
        $email = $this->getRequest()->getParam('email');
        Mage::getSingleton('core/session')->setEmailRefer($email);
        $this->_redirectUrl(Mage::getUrl('customer/account/create/'));
        return;
    }


}
