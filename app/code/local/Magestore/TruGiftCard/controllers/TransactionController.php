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
class Magestore_TruGiftCard_TransactionController extends Mage_Core_Controller_Front_Action
{
    public function sendTruGiftCardAction()
    {
        $amount = $this->getRequest()->getParam('share_amount');
        $day_of_expiration = $this->getRequest()->getParam('share_day_expiration');
        $message = $this->getRequest()->getParam('message');
        $email = filter_var($this->getRequest()->getParam('share_email'), FILTER_SANITIZE_EMAIL);
        $customer = Mage::getModel('customer/customer')->load(Mage::getSingleton('customer/session')->getCustomerId());
        $transaction_helper = Mage::helper('trugiftcard/transaction');

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            Mage::getSingleton('core/session')->addError(
                Mage::helper('trugiftcard')->__($email . ' is not a valid email address')
            );
            $this->_redirectUrl(Mage::getUrl('*/index/shareTruGiftCard'));
            return;
        }

        if (strcasecmp($email, $customer->getEmail()) == 0) {
            Mage::getSingleton('core/session')->addError(
                Mage::helper('trugiftcard')->__('You cannot share truWallet balance to your email')
            );
            $this->_redirectUrl(Mage::getUrl('*/index/shareTruGiftCard'));
            return;
        }

        if (!filter_var($amount, FILTER_VALIDATE_FLOAT)) {
            Mage::getSingleton('core/session')->addError(
                Mage::helper('trugiftcard')->__($amount . ' is not a valid number')
            );
            $this->_redirectUrl(Mage::getUrl('*/index/shareTruGiftCard'));
            return;
        }

        if (!filter_var($day_of_expiration, FILTER_VALIDATE_INT)) {
            Mage::getSingleton('core/session')->addError(
                Mage::helper('trugiftcard')->__($day_of_expiration . ' is not a valid Integer number')
            );
            $this->_redirectUrl(Mage::getUrl('*/index/shareTruGiftCard'));
            return;
        } else if ($day_of_expiration < 1 || $day_of_expiration > 31) {
            Mage::getSingleton('core/session')->addError(
                Mage::helper('trugiftcard')->__('The # days of expiration is not smaller than 0 and greater than 31 ')
            );
            $this->_redirectUrl(Mage::getUrl('*/index/shareTruGiftCard'));
            return;
        }
        $expiration_date = Mage::helper('trugiftcard/transaction')->addDaysToDate(now(), $day_of_expiration);

        $account = Mage::getModel('trugiftcard/customer')->load($customer->getId(), 'customer_id');
        if ($account->getTrugiftcardCredit() < 0 || $amount > $account->getTrugiftcardCredit()) {
            Mage::getSingleton('core/session')->addError(
                Mage::helper('trugiftcard')->__('Sorry, your balance is less than what you want to share. Please enter a new amount.')
            );
            $this->_redirectUrl(Mage::getUrl('*/index/shareTruGiftCard'));
            return;
        }

        $customer_receiver = Mage::getModel("customer/customer");
        $customer_receiver->setWebsiteId(Mage::app()->getStore()->getWebsiteId());
        $customer_receiver->loadByEmail($email);
        $is_exist = false;
        if ($customer_receiver->getId())
            $is_exist = true;

        $status = Magestore_TruGiftCard_Model_Status::STATUS_TRANSACTION_PENDING;

        $transactionSave = Mage::getModel('core/resource_transaction');
        $connection = Mage::getSingleton('core/resource')->getConnection('core_write');
        try {
            $connection->beginTransaction();

            $truGiftCardAccount = Mage::helper('trugiftcard/account')->updateCredit($customer->getId(), -$amount);

            $recipient_transaction_id = null;
            $recipient_transaction = null;
            /* create transaction for recipient first */
            if ($is_exist) {
                $receiverAccount = Mage::helper('trugiftcard/account')->updateCredit($customer_receiver->getId(), $amount);
                $params = array(
                    'credit' => $amount,
                    'title' => '',
                    'receiver_email' => $customer->getEmail(),
                    'receiver_customer_id' => $customer->getId(),
                    'expiration_date' => $expiration_date,
                );
                if ($receiverAccount != null) {
                    $recipient_transaction = $transaction_helper->createTransaction(
                        $receiverAccount,
                        $params,
                        Magestore_TruGiftCard_Model_Type::TYPE_TRANSACTION_RECEIVE_FROM_SHARING,  // type
                        $status
                    );

                    if ($recipient_transaction != null && $recipient_transaction->getId())
                        $recipient_transaction_id = $recipient_transaction->getId();
                    else
                        throw new Exception(
                            Mage::helper('trugiftcard')->__('Cannot create a transaction for recipient.')
                        );
                }
            } else {
                $params = array(
                    'credit' => $amount,
                    'title' => '',
                    'customer_email' => $email,
                    'receiver_email' => $customer->getEmail(),
                    'receiver_customer_id' => $customer->getId(),
                    'expiration_date' => $expiration_date,
                );

                $recipient_transaction = $transaction_helper->createNonTransaction(
                    $customer,
                    $params,
                    Magestore_TruGiftCard_Model_Type::TYPE_TRANSACTION_RECEIVE_FROM_SHARING,  // type
                    $status
                );

                if ($recipient_transaction != null && $recipient_transaction->getId())
                    $recipient_transaction_id = $recipient_transaction->getId();
                else
                    throw new Exception(
                        Mage::helper('trugiftcard')->__('Cannot create a transaction for recipient.')
                    );
            }
            /* END create transaction for recipient first */

            /* CREATE transaction for sender */
            $params = array(
                'credit' => -$amount,
                'title' => '',
                'receiver_email' => $email,
                'receiver_customer_id' => $is_exist ? $customer_receiver->getId() : '',
                'expiration_date' => $expiration_date,
                'recipient_transaction_id' => $recipient_transaction_id,
            );

            if ($truGiftCardAccount != null) {
                $share_transaction = $transaction_helper->createTransaction(
                    $truGiftCardAccount,
                    $params,
                    Magestore_TruGiftCard_Model_Type::TYPE_TRANSACTION_SHARING,  // type
                    $status
                );

                if ($share_transaction != null && $share_transaction->getId()) {
                    $recipient_transaction->setData('recipient_transaction_id', $share_transaction->getId())->save();
                } else {
                    throw new Exception(
                        Mage::helper('trugiftcard')->__('Cannot create a transaction for sender.')
                    );
                }
            }
            /* END transaction for sender */

            $transaction_helper->sendEmailWhenSharingTruGiftCard(
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
                Mage::helper('trugiftcard')->__($e->getMessage())
            );
        }

        $money = Mage::helper('core')->currency($amount, true, false);
        Mage::getSingleton('core/session')->addSuccess(
            Mage::helper('trugiftcard')->__('Congrats! You have just sent <b>' . $money . '</b> to <b>' . $email . '</b> successfully!')
        );
        $this->_redirectUrl(Mage::getUrl('*/index/shareTruGiftCard'));
    }

    public function cancelTransactionAction()
    {
        $transaction_id = $this->getRequest()->getParam('id');
        $transaction = Mage::getModel('trugiftcard/transaction')->load($transaction_id);
        $connection = Mage::getSingleton('core/resource')->getConnection('core_write');
        try {
            $connection->beginTransaction();
            if (!$transaction->getId()) {
                throw new Exception(Mage::helper('trugiftcard')->__('Transaction doesn\'t exist'));
            }

            $transaction->setUpdatedTime(now());
            $transaction->setStatus(Magestore_TruGiftCard_Model_Status::STATUS_TRANSACTION_CANCELLED);
            $transaction->save();

            $truGiftCardAccountAccount = Mage::getModel('trugiftcard/customer')->load($transaction->getTrugiftcardId());
            if ($truGiftCardAccountAccount->getId()) {
                $current_credit = $truGiftCardAccountAccount->getTrugiftcardCredit();
                $_new_credit = $current_credit + abs($transaction->getChangedCredit());
                $truGiftCardAccountAccount->setTrugiftcardCredit($_new_credit);
                $truGiftCardAccountAccount->save();

                $receiver_transaction = Mage::getModel('trugiftcard/transaction')->load($transaction->getRecipientTransactionId());
                if($receiver_transaction != null && $receiver_transaction->getId())
                {
                    $receiver_transaction->setStatus(Magestore_TruGiftCard_Model_Status::STATUS_TRANSACTION_CANCELLED)->save();
                }
            }

            $connection->commit();
            Mage::getSingleton('core/session')->addSuccess(
                Mage::helper('trugiftcard')->__('Transaction has been cancelled successfully')
            );
        } catch (Exception $ex) {
            $connection->rollback();
            Mage::getSingleton('core/session')->addError(
                $ex->getMessage()
            );
        }

        $this->_redirectUrl(Mage::getUrl('*/index/shareTruGiftCard'));
    }

    public function registerAction()
    {
        $email = $this->getRequest()->getParam('email');
        Mage::getSingleton('core/session')->setEmailRefer($email);
        $this->_redirectUrl(Mage::getUrl('customer/account/create/'));
        return;
    }


}
