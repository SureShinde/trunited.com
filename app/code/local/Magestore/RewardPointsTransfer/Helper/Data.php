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
 * RewardPointsTransfer Helper
 * 
 * @category    Magestore
 * @package     Magestore_RewardPointsTransfer
 * @author      Magestore Developer
 */
class Magestore_RewardPointsTransfer_Helper_Data extends Mage_Core_Helper_Abstract {

    const XML_PATH_ENABLE = 'rewardpoints/transferplugin/enable';

    /**
     * get enable referfriends plugin
     * @param type $store
     * @return boolean
     */
    public function isEnable($store = null) {
        return Mage::getStoreConfigFlag(self::XML_PATH_ENABLE, $store);
    }
    /**
     * get transfer config
     * @param type $code
     * @param type $store
     * @return type
     */
    public function getTransferConfig($code, $store = null) {
        return Mage::getStoreConfig('rewardpoints/transferplugin/' . $code, $store);
    }
    /**
     * add a new transfer
     * @param type $emailSend
     * @param type $emailReceive
     * @param type $pointAmount
     * @param type $extraContent
     * @return type
     */
    public function addTransferTransaction($emailSend, $emailReceive, $pointAmount, $extraContent, $storeId=null) {
        if($storeId==null) $storeId = Mage::app()->getStore()->getId();
        $websiteId = Mage::getModel('core/store')->load($storeId)->getWebsiteId();
        $receiver = Mage::getModel('customer/customer')->setWebsiteId($websiteId)->loadByEmail($emailReceive);
        $sender = Mage::getModel('customer/customer')->setWebsiteId($websiteId)->loadByEmail($emailSend);

        $pending_day = $this->getTransferConfig('pending_day');
        if ($pending_day == '' || $pending_day <= 0 || is_nan($pending_day))
            $pending_day = 30;
        $holding_day = $this->getTransferConfig('holding_day');
        if ($holding_day == '' || $holding_day <= 0 || is_nan($holding_day))
            $holding_day = 0;
        
//        $pendDay = time() + $pending_day*86400;
//        $holdDay = time() + $holding_day*86400;

        $transfer = Mage::getModel('rewardpointstransfer/rewardpointstransfer')->setId(null);
        $data = array(
            'sender_email' => $emailSend,
            'receiver_email' => $emailReceive,
            'point_amount' => $pointAmount,
            'sender_customer_id' => $sender->getId(),
            'receiver_customer_id' => $receiver->getId(),
            'extra_content' => $extraContent,
            'store_id' => $storeId,
            'pending_day' => $pending_day,
            'holding_day' => $holding_day,
            'created_time' => now(),
            'update_time' => now(),
        );
        if ($receiver->getId()) {
            if ($this->getTransferConfig('holding_day') > 0)
                $status = Magestore_RewardPointsTransfer_Model_Status::STATUS_HOLDING;
            else
                $status = Magestore_RewardPointsTransfer_Model_Status::STATUS_COMPLETED;
            $data['status'] = $status;
            $transfer->setData($data);
            try {
                $transfer->save();
            } catch (Exception $e) {
                
            }
            $this->addSenderTransaction($sender, $transfer);
            if ($status == Magestore_RewardPointsTransfer_Model_Status::STATUS_COMPLETED) {
                $this->addReceiverTransaction($receiver, $transfer);
            }
            Mage::getModel('rewardpointstransfer/rewardpointstransfer')->sendAccountTransferEmail($transfer);
        } else {
            try {
                $transfer->setData($data);
                $status = Magestore_RewardPointsTransfer_Model_Status::STATUS_PENDING;
                $transfer->setStatus($status);
                $transfer->save();
                $this->addSenderTransaction($sender, $transfer);
            } catch (Exception $e) {
            }
            Mage::getModel('rewardpointstransfer/rewardpointstransfer')->sendTransferEmail($transfer);
        }
        return $transfer;
    }
    public function addSenderTransaction($sender, $transfer){
        $storeId = $transfer->getStoreId();
        if(!$storeId) $storeId = Mage::app()->getStore()->getId();
        
        $rewardSender = Mage::getModel('rewardpoints/customer')->load($sender->getId(), 'customer_id');
        if($rewardSender->getPointBalance() < $transfer->getPointAmount()) return 1;
        
        $transferObject = new Varien_Object();
        $transferObject->setData('point_amount', $transfer->getPointAmount());
        $transferObject->setData('store_id', $storeId);
        try{
            $transactionSend = Mage::helper('rewardpoints/action')->addTransaction('sendpoint', $sender, $transferObject, array('transfer_id' => $transfer->getId()));
            $this->updateTransactionId($transfer->getId(), $transactionSend->getId());
            if($transactionSend->getId()) return 2;
        }catch(Exception $e){}
        return false;
    }
    public function addReceiverTransaction($receiver, $transfer){
        $storeId = $transfer->getStoreId();
        if(!$storeId) $storeId = Mage::app()->getStore()->getId();
        
        $transferObject = new Varien_Object();
        $transferObject->setData('point_amount', $transfer->getPointAmount());
        $transferObject->setData('store_id', $storeId);
        try{
            $transactionReceive = Mage::helper('rewardpoints/action')->addTransaction('receivepoint', $receiver, $transferObject, array('transfer_id' => $transfer->getId()));
            $this->updateTransactionId($transfer->getId(), null, $transactionReceive->getId());
            if($transactionReceive->getId()) return true;
        }catch(Exception $e){}
        return false;
    }
//    public function addTransactionTransfer($sender, $receiver, $transfer){
//        $storeId = $transfer->getStoreId();
//        if(!$storeId) $storeId = Mage::app()->getStore()->getId();
//        
//        $rewardSender = Mage::getModel('rewardpoints/customer')->load($sender->getId(), 'customer_id');
//        if($rewardSender->getPointBalance() < $transfer->getPointAmount()) return 1;
//        
//        $transferObject = new Varien_Object();
//        $transferObject->setData('point_amount', $transfer->getPointAmount());
//        $transferObject->setData('store_id', $storeId);
//        try{
//            $transactionSend = Mage::helper('rewardpoints/action')->addTransaction('sendpoint', $sender, $transferObject, array('transfer_id' => $transfer->getId()));
//            $transactionReceive = Mage::helper('rewardpoints/action')->addTransaction('receivepoint', $receiver, $transferObject, array('transfer_id' => $transfer->getId()));
//            $this->updateTransactionId($transfer->getId(), $transactionSend->getId(), $transactionReceive->getId());
//            if($transactionSend->getId() && $transactionReceive->getId()) return 2;
//        }catch(Exception $e){}
//        return false;
//    }
    
    /**
     * insert send_transaction_id, receive_transaction_id vao table transfer
     * @param type $transferId
     * @param type $transactionId
     * @return type
     */
    public function updateTransactionId($transferId, $transactionSendId = null, $transactionReceiveId = null) {
        $transfer = Mage::getModel('rewardpointstransfer/rewardpointstransfer')->setId($transferId);
        if($transactionSendId != null) $transfer->setData('send_transaction_id', $transactionSendId);
        if($transactionReceiveId != null) $transfer->setData('receive_transaction_id', $transactionReceiveId);
        $transfer->save();
        return $transfer;
    }
        

    /**
     * complete a transfer
     * @param type $transfer
     * @return type
     */
    public function completeTransfer($transfer) {
        if ($transfer->getStatus() == Magestore_RewardPointsTransfer_Model_Status::STATUS_HOLDING) {
//            $sendId = $transfer->getSenderCustomerId();
            $receiveId = $transfer->getReceiverCustomerId();
//            if($sendId == null || $receiveId == null) return;
//            $sender = Mage::getModel('customer/customer')->load($sendId);
            $receiver = Mage::getModel('customer/customer')->load($receiveId);
//            if(!$sender->getId() || !$receiver->getId()){
//                $reason = $this->__('Error ocur in database.');
//                $this->cancelTransfer($transfer, $reason);
//                return;
//            }
//            
//            $rewardCustomer = Mage::getModel('rewardpoints/customer')->load($sendId, 'customer_id');
//            $store = Mage::getModel('core/store')->load($transfer->getStoreId());
//            $storeCode = $store->getCode();
//            $point = $transfer->getPointAmount();
//            $pointMin = $this->getTransferConfig('minimum_point');
//            if($storeCode != 'admin'){
//                if($rewardCustomer->getPointBalance() < $pointMin){
//                    $reason = $this->__('Sender is not enought balance to transfer.');
//                    $this->cancelTransfer($transfer, $reason);
//                    return;
//                }
//            }
//            if($point > $rewardCustomer->getPointBalance()){
//                $reason = $this->__('The point balance of %s (%s) is not enough to complete transfer.', $sender->getName(), $sender->getEmail());
//                $this->cancelTransfer($transfer, $reason);
//                return;
//            }
            
            $transferTransaction = $this->addReceiverTransaction($receiver, $transfer);
            if(!$transferTransaction) {
                $reason = $this->__('Cannot complete transfer!');
                $this->cancelTransfer ($transfer, $reason);
            }
            if($transferTransaction){
                $transfer->setData('status', Magestore_RewardPointsTransfer_Model_Status::STATUS_COMPLETED);
                $transfer->setData('update_time', now());
                $transfer->save();
            }
            return $transfer;
        }
        return false;
    }

    /**
     * cancel a transfer
     * @param type $transfer
     * @return type
     */
    public function cancelTransfer($transfer, $reason = null, $isSenderCancel = false) {
        if ($transfer->getStatus() <= Magestore_RewardPointsTransfer_Model_Status::STATUS_PENDING) {
            
            $sender = Mage::getModel('customer/customer')->load($transfer->getSenderCustomerId());
            $transferObject = new Varien_Object();
            $transferObject->setData('point_amount', $transfer->getPointAmount());
            $transferObject->setData('store_id', $transfer->getStoreId());
            
            $transactionSend = Mage::helper('rewardpoints/action')->addTransaction('refundpoint', $sender, $transferObject, 'transfer_id='.$transfer->getId());
            
            $transfer->setData('status', Magestore_RewardPointsTransfer_Model_Status::STATUS_CANCEL);
            $transfer->setData('update_time', now());
            $transfer->save();
            $transfer->sendCancelTransfer($reason, $isSenderCancel);
            return $transfer;
        }
        
        /**
         * xuanbinh
         */
        if($transfer->getStatus() == Magestore_RewardPointsTransfer_Model_Status::STATUS_COMPLETED){
            $sender = Mage::getModel('customer/customer')->load($transfer->getSenderCustomerId());
            $receive_transaction_id = $transfer->getReceiveTransactionId();

            $transferObject = new Varien_Object();
            $transferObject->setData('point_amount', $transfer->getPointAmount());
            $transferObject->setData('store_id', $transfer->getStoreId());
            

            $transactionReceive   = Mage::getModel('rewardpoints/transaction')->load($receive_transaction_id);
            if($transactionReceive->cancelTransaction()){
                $transactionSend = Mage::helper('rewardpoints/action')->addTransaction('refundpoint', $sender, $transferObject, 'transfer_id='.$transfer->getId());
            }
            
            $transfer->setData('status', Magestore_RewardPointsTransfer_Model_Status::STATUS_CANCEL);
            $transfer->setData('update_time', now());
            $transfer->save();
            $transfer->sendCancelTransfer($reason, $isSenderCancel);
            return $transfer;
        }
        /**
         * end
         */
        
        return false;
    }
	
	/**
     * xuanbinh
     * 16/07/2015
     * check customer group
     * @return boolean
     */
    public function enableLink(){
		$customer = Mage::helper('customer')->getCustomer();
		$group = $customer->getGroupId();
		$customerGroup = Mage::helper('rewardpointstransfer')->getTransferconfig('customer_group');
		$cusGroup = explode(',', $customerGroup);
		
		foreach($cusGroup as $key=>$val){
			if($val == $group) return true;
		}
	
		return false;		
    }
}
