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
 * @package     Magestore_RewardPointsTransfer
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * Rewardpointstransfer Model
 * 
 * @category    Magestore
 * @package     Magestore_RewardPointsTransfer
 * @author      Magestore Developer
 */
class Magestore_RewardPointsTransfer_Model_Rewardpointstransfer extends Mage_Core_Model_Abstract {

    const XML_PATH_EMAIL_ENABLE = 'rewardpoints/email/enable';
    const XML_PATH_EMAIL_SENDER = 'rewardpoints/email/sender';
    const XML_PATH_EMAIL_ACCOUNT = 'rewardpoints/transferplugin/email_template_have_account';
    const XML_PATH_EMAIL_NO_ACCOUNT = 'rewardpoints/transferplugin/email_template_no_account';
    const XML_PATH_EMAIL_CANCEL_TRANSFER = 'rewardpoints/transferplugin/email_template_cancel_transfer';

    public function _construct() {
        parent::_construct();
        $this->_init('rewardpointstransfer/rewardpointstransfer');
    }

    /**
     * Send email to customer receive points who exist in this store
     * @param type $transfer
     * @return \Magestore_RewardPointsTransfer_Model_Rewardpointstransfer
     */
    public function sendAccountTransferEmail($transfer = null) {
        if (!Mage::getStoreConfigFlag(self::XML_PATH_EMAIL_ENABLE, $this->getStoreId())) {
            return $this;
        }
        if (!Mage::helper('rewardpointstransfer')->getTransferConfig('email_notification', $this->getStoreId()))
            return $this;
        if ($transfer == null)
            $transfer = $this;
        $customer_id = $transfer->getReceiverCustomerId();
        $customer = Mage::getModel('customer/customer')->load($customer_id);
        if (!$customer->getId()) {
            return $this;
        }
        $rewardAccount = Mage::getModel('rewardpoints/customer')->load($customer_id, 'customer_id');
        if ($rewardAccount->getId() && !$rewardAccount->getTransferNotification()) {
            return $this;
        }

        $sender = Mage::getModel('customer/customer')->load($transfer->getSenderCustomerId());
        $store = Mage::app()->getStore();
        $translate = Mage::getSingleton('core/translate');
        $translate->setTranslateInline(false);
        $message = $transfer->getExtraContent();
        $ismessage = FALSE;
        if (strlen($message) > 0)
            $ismessage = TRUE;

        Mage::getModel('core/email_template')
                ->setDesignConfig(array(
                    'area' => 'frontend',
                    'store' => $store->getId()
                ))->sendTransactional(
                Mage::getStoreConfig(self::XML_PATH_EMAIL_ACCOUNT, $store), Mage::getStoreConfig(self::XML_PATH_EMAIL_SENDER, $store), $customer->getEmail(), $customer->getName(), array(
            'store' => $store,
            'customer' => $customer,
            'sender' => $sender,
            'message' => $message,
            'ismessage' => $ismessage,
            'amount' => $transfer->getPointAmount(),
            //'total' => $rewardAccount->getPointBalance(),
            'point_amount' => Mage::helper('rewardpoints/point')->format($transfer->getPointAmount(), $store),
            //'point_balance' => Mage::helper('rewardpoints/point')->format($rewardAccount->getPointBalance(), $store),
            'status' => Mage::getBlockSingleton('rewardpointstransfer/rewardpointstransfer')->getStatusLabel($transfer->getId()),
                )
        );

        $translate->setTranslateInline(true);
        return $this;
    }

    /**
     * Send email to customer receive points who does not exist in this store
     * @param type $transfer
     * @return \Magestore_RewardPointsTransfer_Model_Rewardpointstransfer
     */
    public function sendTransferEmail($transfer = null) {
        if (!Mage::getStoreConfigFlag(self::XML_PATH_EMAIL_ENABLE, $this->getStoreId())) {
            return $this;
        }
        if ($transfer == null)
            $transfer = $this;
        $sender = Mage::getModel('customer/customer')->load($transfer->getSenderCustomerId());
        $store = Mage::app()->getStore();
        $translate = Mage::getSingleton('core/translate');
        $translate->setTranslateInline(false);
        $message = $transfer->getExtraContent();
        $ismessage = FALSE;
        if (strlen($message) > 0)
            $ismessage = TRUE;

        Mage::getModel('core/email_template')
                ->setDesignConfig(array(
                    'area' => 'frontend',
                    'store' => $store->getId()
                ))->sendTransactional(
                Mage::getStoreConfig(self::XML_PATH_EMAIL_NO_ACCOUNT, $store), Mage::getStoreConfig(self::XML_PATH_EMAIL_SENDER, $store), $transfer->getReceiverEmail(), $transfer->getReceiverEmail(), array(
            'store' => $store,
            'customer' => $transfer->getReceiverEmail(),
            'sender' => $sender->getName(),
            'message' => $message,
            'ismessage' => $ismessage,
            'amount' => $transfer->getPointAmount(),
            'link' => $this->getUrl('customer/account/create', array('rwp' => $transfer->getReceiverEmail())),
            'point_amount' => Mage::helper('rewardpoints/point')->format($transfer->getPointAmount(), $store),
            'status' => Mage::getBlockSingleton('rewardpointstransfer/rewardpointstransfer')->getStatusLabel($transfer->getId()),
                )
        );

        $translate->setTranslateInline(true);
        return $this;
    }

    /**
     * send email 
     * @param type $reason
     */
    public function sendCancelTransfer($reason, $isSenderCancel = false) {
        if (!Mage::getStoreConfigFlag(self::XML_PATH_EMAIL_ENABLE, $this->getStoreId())) {
            return $this;
        }
        if (!Mage::helper('rewardpointstransfer')->getTransferConfig('email_cancel_transfer', $this->getStoreId()))
            return $this;
        if(!$isSenderCancel)
            $this->_cancelTransferEmail($reason, 'sender');
        $this->_cancelTransferEmail($reason, 'receiver');
    }

    protected function _cancelTransferEmail($reason, $type) {
        $store = Mage::app()->getStore($this->getStoreId());
        $translate = Mage::getSingleton('core/translate');
        $translate->setTranslateInline(false);
        $message = $this->getExtraContent();
        $ismessage = FALSE;
        if (strlen($message) > 0)
            $ismessage = TRUE;
        if ($type == 'sender') {
            $customer_id = $this->getSenderCustomerId();
            $rewardAccount = Mage::getModel('rewardpoints/customer')->load($customer_id, 'customer_id');
            if ($rewardAccount->getId() && !$rewardAccount->getTransferNotification()) {
                return $this;
            }
            $customer = Mage::getModel('customer/customer')->load($customer_id);
            if ($customer->getId()) {
                $email = $customer->getEmail();
                $name = $customer->getName();
            }
        } else {
            $customer_id = $this->getReceiverCustomerId();
            $customer = Mage::getModel('customer/customer')->load($customer_id);
            if ($customer->getId()) {
                $rewardAccount = Mage::getModel('rewardpoints/customer')->load($customer_id, 'customer_id');
                if ($rewardAccount->getId() && !$rewardAccount->getTransferNotification()) {
                    return $this;
                }
                $email = $customer->getEmail();
                $name = $customer->getName();
            } else {
                $email = $this->getReceiverEmail();
                $name = $this->getReceiverEmail();
            }
        }
        Mage::getModel('core/email_template')
                ->setDesignConfig(array(
                    'area' => 'frontend',
                    'store' => $store->getId()
                ))->sendTransactional(
                Mage::getStoreConfig(self::XML_PATH_EMAIL_CANCEL_TRANSFER, $store), Mage::getStoreConfig(self::XML_PATH_EMAIL_SENDER, $store), $email, $name, array(
            'store' => $store,
            'name' => $name,
            'receiver' => $this->getReceiverEmail(),
            'sender' => $this->getSenderEmail(),
            'transfer_id' => $this->getTransferId(),
            'created_time' => $this->getCreatedTime(),
            'reason' => $reason,
            'message' => $message,
            'point_amount' => Mage::helper('rewardpoints/point')->format($this->getPointAmount(), $store),
            'ismessage' => $ismessage,
                )
        );

        $translate->setTranslateInline(true);
        return $this;
    }

}
