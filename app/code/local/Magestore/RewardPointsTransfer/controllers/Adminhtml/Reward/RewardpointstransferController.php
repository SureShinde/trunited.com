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
 * Rewardpointstransfer Adminhtml Controller
 * 
 * @category    Magestore
 * @package     Magestore_RewardPointsTransfer
 * @author      Magestore Developer
 */
class Magestore_RewardPointsTransfer_Adminhtml_Reward_RewardpointstransferController extends Mage_Adminhtml_Controller_Action {

    /**
     * init layout and set active for current menu
     *
     * @return Magestore_RewardPointsTransfer_Adminhtml_RewardpointstransferController
     */
    protected function _initAction() {
        $this->loadLayout()
                ->_setActiveMenu('rewardpoints/transfer')
                ->_addBreadcrumb(
                        Mage::helper('adminhtml')->__('All Points Transfer'), Mage::helper('adminhtml')->__('All Points Transfer')
        );
        return $this;
    }

    /**
     * index action
     */
    public function indexAction() {
        $this->_initAction()
                ->renderLayout();
    }

    /**
     * view and edit item action
     */
    public function editAction() {
        $rewardpointstransferId = $this->getRequest()->getParam('id');
        $model = Mage::getModel('rewardpointstransfer/rewardpointstransfer')->load($rewardpointstransferId);

        if ($model->getId() || $rewardpointstransferId == 0) {
            $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
            if (!empty($data)) {
                $model->setData($data);
            }
            Mage::register('rewardpointstransfer_data', $model);

            $this->loadLayout();
            $this->_setActiveMenu('rewardpointstransfer/rewardpointstransfer');

            $this->_addBreadcrumb(
                    Mage::helper('adminhtml')->__('Item Manager'), Mage::helper('adminhtml')->__('Item Manager')
            );
            $this->_addBreadcrumb(
                    Mage::helper('adminhtml')->__('Item News'), Mage::helper('adminhtml')->__('Item News')
            );

            $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
            $this->_addContent($this->getLayout()->createBlock('rewardpointstransfer/adminhtml_rewardpointstransfer_edit'))
                    ->_addLeft($this->getLayout()->createBlock('rewardpointstransfer/adminhtml_rewardpointstransfer_edit_tabs'));

            $this->renderLayout();
        } else {
            Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('rewardpointstransfer')->__('Item does not exist')
            );
            $this->_redirect('*/*/');
        }
    }

    public function customerAction() {
        $this->loadLayout()
                ->renderLayout();
    }

    public function customerGridAction() {
        $this->loadLayout()
                ->renderLayout();
    }

    public function checkCustomerAction() {
        $sender_email = $this->getRequest()->getParam('sender_email');
        $receiver_email = $this->getRequest()->getParam('receiver_email');
        if ($sender_email == $receiver_email) {
            echo 'duplicate';
            return;
        }
        $website_id = $this->getRequest()->getParam('website_id');
        $customer = Mage::getModel('customer/customer')->setWebsiteId((int) $website_id == 0 ? 1 : $website_id)->loadByEmail($receiver_email);
        if (!$customer->getId()) {
            echo 'notexist';
            return;
        } else {
            echo 'success';
            return;
        }
    }

    public function newAction() {
        $this->_forward('edit');
    }
/**
 * cancel a transfer 
 * @return type
 */
    public function cancelAction() {
        $id = $this->getRequest()->getParam('id');
        $transaction = Mage::getModel('rewardpointstransfer/rewardpointstransfer')->load($id);
        try {
            if ($transaction->getId()) {
                $cancel = $this->_cancelTransaction($transaction);
                if ($cancel && $cancel->getId())
                    Mage::getSingleton('adminhtml/session')->addSuccess(
                            $this->__('Transaction has been canceled successfully.')
                    );
                else {
                    Mage::getSingleton('adminhtml/session')->addError($this->__('Transaction can\'t cancel.'));
                }
            }
            $this->_redirect('*/*/edit', array('id' => $transaction->getId()));
            return;
        } catch (Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
        }
        $this->_redirect('*/*/');
    }
/**
 * complete a transfer
 * @return type
 */
    public function completeAction() {
        $id = $this->getRequest()->getParam('id');
        $model = Mage::getModel('rewardpointstransfer/rewardpointstransfer')->load($id);
        try {
            if ($model->getId() && $model->getStatus() == Magestore_RewardPointsTransfer_Model_Status::STATUS_HOLDING) {
                $this->_completeTransaction($model);
                $model->setUpdateTime(now());
                $model->save();
                Mage::getSingleton('adminhtml/session')->addSuccess($this->__('Transaction has been canceled successfully.'));
            } else {
                Mage::getSingleton('adminhtml/session')->addError($this->__('Transaction can\'t cancel.'));
            }

            $this->_redirect('*/*/edit', array('id' => $model->getId()));
            return;
        } catch (Exception $exc) {
            Mage::getSingleton('adminhtml/session')->addError($exc->getMessage());
        }
    }

    /**
     * save item action
     */
    public function saveAction() {
        if ($data = $this->getRequest()->getPost()) {

            $model = Mage::getModel('rewardpointstransfer/rewardpointstransfer');
            $id = $this->getRequest()->getParam('id');
            $model->setData($data)
                    ->setId($id);
            $website_id = $this->getRequest()->getParam('website_id', 1);
            $store_id = Mage::app()->getStore()->getId();
            $sender_email = $data['sender_email'];
            $receiver_email = $data['receiver_email'];
            $customer = Mage::getModel('customer/customer')->setWebsiteId((int) $website_id);
            $sender_id = $customer->loadByEmail($sender_email)->getId();
            $receiver_id = $customer->loadByEmail($receiver_email)->getId();
            if (!$this->_checkEmail($sender_email, $receiver_email)) {

                $message_error = Mage::helper('rewardpointstransfer')->__('Duplicated email error!');
                $this->_redirectValidate($data, $message_error);
                return;
            }
            if ($data['point_amount'] <= 0) {
                $message_error = Mage::helper('rewardpointstransfer')->__('The transferred point amount must be greater than 0');
                $this->_redirectValidate($data, $message_error);
                return;
            }
            if (!$this->_checkPoints($sender_id, $data['point_amount'])) {
                $message_error = Mage::helper('rewardpointstransfer')->__('Account balance is not enough to create this transaction');
                $this->_redirectValidate($data, $message_error);
                return;
            }

            try {
                if ($model->getCreatedTime() == NULL || $model->getUpdateTime() == NULL) {
                    $model->setCreatedTime(now())
                            ->setUpdateTime(now());
                } else {
                    $model->setUpdateTime(now());
                }
                $holding_day = $data['holding_day'];
                $model->setSenderCustomerId($sender_id);
                if ($data['status'] == Magestore_RewardPointsTransfer_Model_Status::STATUS_COMPLETED) {
                    $holding_day = 0;
                } else {
                    if ($holding_day == '' || $holding_day == 0) {
                        $model->setStatus(Magestore_RewardPointsTransfer_Model_Status::STATUS_COMPLETED);
                    }
                }
                if ($receiver_id) {
                    $model->setReceiverCustomerId($receiver_id);
                    $pending_day = 0;
                } else {
                    $model->setStatus(Magestore_RewardPointsTransfer_Model_Status::STATUS_PENDING);
                    $pending_day = Mage::helper('rewardpointstransfer')->getTransferConfig('pending_day');
                    if ($pending_day == 0 || $pending_day == '') {
                        $pending_day = 30;
                    }
                }
                $model->setPendingDay($pending_day);
                $model->setHoldingDay($holding_day);
                $model->setStoreId($store_id);
                //                create transaction
                $this->_commitTransaction($model);
                //                save transfer
                $model->save();
                if (!$receiver_id && $data['status'] == Magestore_RewardPointsTransfer_Model_Status::STATUS_COMPLETED)
                    Mage::getSingleton('adminhtml/session')->addSuccess(
                            Mage::helper('rewardpointstransfer')->__('Recipient’s Email does not exist, so the status changes to “pending”.')
                    );
                Mage::getSingleton('adminhtml/session')->addSuccess(
                        Mage::helper('rewardpointstransfer')->__('Point transfer was successfully saved!')
                );
                Mage::getSingleton('adminhtml/session')->setFormData(false);

                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', array('id' => $model->getId()));
                    return;
                }
                $this->_redirect('*/*/');
                return;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setFormData($data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(
                Mage::helper('rewardpointstransfer')->__('Unable to find point transfer to save!')
        );
        $this->_redirect('*/*/');
    }

    /**
     * check email
     * @param type $sender_email
     * @param type $receiver_email
     * @return type
     */
    protected function _checkEmail($sender_email, $receiver_email) {
        return ($sender_email != $receiver_email);
    }

    /**
     * check input point amount
     * @param type $customer_id
     * @param type $points
     * @return type
     */
    protected function _checkPoints($customer_id, $points) {
        $points_balance = Mage::getModel('rewardpoints/customer')->load($customer_id, 'customer_id')->getPointBalance();
        return ($points_balance >= $points);
    }

    /**
     * redirect url
     * @param type $model
     * @return type
     */
    protected function _redirectValidate($data, $message) {
        Mage::getSingleton('adminhtml/session')->addError($message);
        Mage::getSingleton('adminhtml/session')->setFormData($data);
        $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
    }
/**
 * create transaction
 * @param type $transfer
 */
    protected function _commitTransaction($transfer) {

//        create send transaction
        if ($transfer->getStatus() <= Magestore_RewardPointsTransfer_Model_Status::STATUS_COMPLETED) {
            if (!$transfer->getSendTransactionId()) {
                $point = $transfer->getPointAmount();
                $sendObject = new Varien_Object();
                $sendObject->setData('point_amount', $point);
                $sendObject->setData('store_id', $transfer->getStoreId());
                $sender = Mage::getModel('customer/customer')->load($transfer->getSenderCustomerId());
                if ($sender->getId()) {
                    $send_transaction = Mage::helper('rewardpoints/action')
                            ->addTransaction(
                            'sendpoint', $sender, $sendObject, array('transfer_id' => $transfer->getId())
                    );
                    $transfer->setSendTransactionId($send_transaction->getId());
                }
                //send email
                if (!$transfer->getReceiverCustomerId()) {
                    $transfer->sendTransferEmail();
                } else {
                    $transfer->sendAccountTransferEmail();
                }
            }
        }
//        create receive transaction
        if ($transfer->getStatus() == Magestore_RewardPointsTransfer_Model_Status::STATUS_COMPLETED) {
            if (!$transfer->getReceiveTransactionId()) {
                $point = $transfer->getPointAmount();
                $receiveObject = new Varien_Object();
                $receiveObject->setData('point_amount', $point);
                $receiveObject->setData('store_id', $transfer->getStoreId());
                $receiver = Mage::getModel('customer/customer')->load($transfer->getReceiverCustomerId());
                if ($receiver->getId()) {
                    $receive_transaction = Mage::helper('rewardpoints/action')
                            ->addTransaction(
                            'receivepoint', $receiver, $receiveObject, array('transfer_id' => $transfer->getId())
                    );
                    $transfer->setReceiveTransactionId($receive_transaction->getId());
                }
            }
        }
    }

    /**
     * massaction complete transfer
     */
    public function massCompleteAction() {
        $tranIds = $this->getRequest()->getParam('rewardpointstransfer');
        if (!is_array($tranIds) || !count($tranIds)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select item(s)'));
        } else {
            $collection = Mage::getResourceModel('rewardpointstransfer/rewardpointstransfer_collection')
                    ->addFieldToFilter('point_amount', array('gt' => 0))
                    ->addFieldToFilter('status', array(
                        'eq' => Magestore_RewardPointsTransfer_Model_Status::STATUS_HOLDING
                    ))
                    ->addFieldToFilter('transfer_id', array('in' => $tranIds));
            $total = 0;
            foreach ($collection as $transaction) {
                try {
                    $this->_completeTransaction($transaction);
                    $transaction->setUpdateTime(now());
                    $transaction->save();//xuanbinh
                    $total++;
                } catch (Exception $e) {
                    Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                }
            }
            if ($total > 0) {
                Mage::getSingleton('adminhtml/session')->addSuccess(
                        $this->__('Total of %d transaction(s) were successfully completed', $total)
                );
            } else {
                Mage::getSingleton('adminhtml/session')->addError(
                        $this->__('No transaction was completed')
                );
            }
        }
        $this->_redirect('*/*/index');
    }

    protected function _completeTransaction($transfer) {
        $transfer->setStatus(Magestore_RewardPointsTransfer_Model_Status::STATUS_COMPLETED);
        $this->_commitTransaction($transfer);
    }

    /**
     * mass cancel transaction(s) action
     */
    public function massCancelAction() {
        $tranIds = $this->getRequest()->getParam('rewardpointstransfer');
        if (!is_array($tranIds) || !count($tranIds)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select item(s)'));
        } else {
            $collection = Mage::getResourceModel('rewardpointstransfer/rewardpointstransfer_collection')
                    ->addFieldToFilter('point_amount', array('gt' => 0))
                    ->addFieldToFilter('status', array(
                        'lteq' => Magestore_RewardPointsTransfer_Model_Status::STATUS_COMPLETED //xuanbinh
                    ))
                    ->addFieldToFilter('transfer_id', array('in' => $tranIds));
            $total = 0;
            foreach ($collection as $transaction) {
                try {
                    if($this->_cancelTransaction($transaction)){ //xuanbinh
                        $total++;
                    }
                } catch (Exception $e) {
                    Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                }
            }
            if ($total > 0) {
                Mage::getSingleton('adminhtml/session')->addSuccess(
                        $this->__('Total of %d transaction(s) were successfully canceled', $total)
                );
            } else {
                Mage::getSingleton('adminhtml/session')->addError(
                        $this->__('No transaction was canceled')
                );
            }
        }
        $this->_redirect('*/*/index');
    }

    /**
     * cancel a transaction
     * @param type $transaction
     */
    protected function _cancelTransaction($transaction) {
        try {
            $reason = Mage::helper('rewardpointstransfer')->__('Transfer was canceled by admin.');

            return Mage::helper('rewardpointstransfer')->cancelTransfer($transaction, $reason);
        } catch (Exception $exc) {
            echo $exc->getTraceAsString();
        }
    }

    /**
     * export grid item to CSV type
     */
    public function exportCsvAction() {
        $fileName = 'rewardpointstransfer.csv';
        $content = $this->getLayout()
                ->createBlock('rewardpointstransfer/adminhtml_rewardpointstransfer_grid')
                ->getCsv();
        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * export grid item to XML type
     */
    public function exportXmlAction() {
        $fileName = 'rewardpointstransfer.xml';
        $content = $this->getLayout()
                ->createBlock('rewardpointstransfer/adminhtml_rewardpointstransfer_grid')
                ->getXml();
        $this->_prepareDownloadResponse($fileName, $content);
    }

    protected function _isAllowed() {
        return Mage::getSingleton('admin/session')->isAllowed('rewardpoints/transfer');
    }

}