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
 * Rewardpointstransfer Block
 * 
 * @category    Magestore
 * @package     Magestore_RewardPointsTransfer
 * @author      Magestore Developer
 */
class Magestore_RewardPointsTransfer_Block_Rewardpointstransfer extends Mage_Core_Block_Template
{
    /**
     * prepare block's layout
     *
     * @return Magestore_RewardPointsTransfer_Block_Rewardpointstransfer
     */
    public function _prepareLayout()
    {
        return parent::_prepareLayout();
    }
    /**
     * check enable plugin
     * @return bolean
     */
    public function isEnabled(){
        return $this->_helper()->isEnable();
    }
    
    /**
     * get label of day
     * @param type $day
     * @return string
     */
    public function getDay($day=0){
        if($day==1)
            return $this->__('days');
        else return $this->__('day');
    }
    /**
     * get send transaction
     * @return collection
     */
    public function getSendCollection(){
        $customer = Mage::helper('customer')->getCustomer();
        $collection = Mage::getModel('rewardpointstransfer/rewardpointstransfer')->getCollection()
                ->addFieldToFilter('sender_email', $customer->getEmail());
        return $collection;
    }
    /**
     * get receive transaction
     * @return collection
     */
    public function getReceiveCollection(){
        $customer = Mage::helper('customer')->getCustomer();
        $collection = Mage::getModel('rewardpointstransfer/rewardpointstransfer')->getCollection()
                ->addFieldToFilter('receiver_email', $customer->getEmail());
        return $collection;
    }
    /**
     * Get holding day to complete transfer transaction
     * @return boolean|string
     */
    public function getDayHolding(){
        $day = trim($this->_helper()->getTransferConfig('holding_day'));
        if($day<=0 || $day=='' || !is_numeric($day)) return false;
        else{
            if($day==1) return '1 '.$this->getDay();
            else return $day.' '.$this->getDay(1);
        }
    }
    /**
     * Get day pending for wait create customer
     * @return string
     */
    public function getDayPending(){
        $day = trim($this->_helper()->getTransferConfig('pending_day'));
        if($day<=0 || $day=='' || !is_numeric($day)) return '30 '.$this->getDay(1);
        else{
            if($day==1) return '1 '.$this->getDay();
            else return $day.' '.$this->getDay(1);
        }
    }
    /**
     * Get maximum of point transfer per time
     * @return boolean|int
     */
    public function getPointTransfer(){
        $point = trim($this->_helper()->getTransferConfig('maximum_point'));
        if($point<=0 || $point=='' || !is_numeric($point)) return false;
        else{
            return Mage::helper('rewardpoints/point')->format($point);
        }
    }
    /**
     * Get point balance after transfer to check transfer points
     * @return boolean|int
     */
    public function getPointExist(){
        $point = trim($this->_helper()->getTransferConfig('minimum_point'));
        if($point<=0 || $point=='' || !is_numeric($point)) return false;
        else{
            return Mage::helper('rewardpoints/point')->format($point);
        }
    }
    /**
     * get number of point customer can transfer
     * @return string
     */
    public function getPointAvailable(){
        $customer = Mage::helper('customer')->getCustomer();
        $customerId = $customer->getId();
        $pointMax = trim($this->_helper()->getTransferConfig('maximum_point'));
        if($pointMax == '' || $pointMax <= 0 || is_nan($pointMax)) $pointMax = 0;
        $pointMin = trim($this->_helper()->getTransferConfig('minimum_point'));
        if($pointMin == '' || $pointMin <= 0 || is_nan($pointMin)) $pointMin = 0;
        $rewardCustomer = Mage::getModel('rewardpoints/customer')->load($customerId, 'customer_id');
        
        /* check customer group */
        $group = $customer->getGroupId();
        $customerGroup = $this->_helper()->getTransferconfig('customer_group');
        $cusGroup = explode(',', $customerGroup);
        $check = 0;
        foreach($cusGroup as $key=>$val){
            if($val == $group) $check = 1;
        }
        if(!$check) return -1;
        /* end check */
        
        if($rewardCustomer->getId()){
            $accumulatedPoints = $rewardCustomer->getAccumulatedPoints();
            $currentPoints = $rewardCustomer->getPointBalance();
            if($accumulatedPoints < $pointMin){
                $findTransfer = Mage::getModel('rewardpointstransfer/rewardpointstransfer')->load($customer->getEmail(), 'sender_email');
                if($findTransfer->getId()) $pointReturn = ($point < $pointMax)? $point :  $pointMax;
                else $pointReturn = 0;
            }
            else{
                $pointReturn = ($currentPoints < $pointMax) ? $currentPoints :  $pointMax;
                if($pointMax == 0) $pointReturn = $currentPoints;
            }
        }else{
            $pointReturn = 0;
        }
        return $pointReturn;
    }
    /**
     * get Helper
     * @return helper
     */
    protected function _helper(){
        return Mage::helper('rewardpointstransfer');
    }
    /**
     * get Status label
     * @return string
     */
    public function getStatusLabel($transferId){
        $transfer = Mage::getModel('rewardpointstransfer/rewardpointstransfer')->load($transferId);
        $statusLabel = '';
        if(count($transfer)){
            $status = $transfer->getStatus();
            switch($status){
                case Magestore_RewardPointsTransfer_Model_Status::STATUS_HOLDING :
                    $statusLabel = 'Holding';
                    break;
                case Magestore_RewardPointsTransfer_Model_Status::STATUS_PENDING :
                    $statusLabel = 'Pending';
                    break;
                case Magestore_RewardPointsTransfer_Model_Status::STATUS_COMPLETED :
                    $statusLabel = 'Completed';
                    break;
                case Magestore_RewardPointsTransfer_Model_Status::STATUS_CANCEL :
                    $statusLabel = 'Cancel';
                    break;
                default :
                    $statusLabel = 'Unavailable';
            }
        }
        return $statusLabel;
    }
//    public function getCancelConfig(){
//        return $this->_helper()->getTransferConfig('allow_cancel');
//    }
    
    /**
     * get send email setting for transfer
     * @return int
     */
    public function getTransferNotification(){
        $rewardAccount = Mage::helper('rewardpoints/customer')->getAccount();
        if (!$rewardAccount->getId()) {
            $rewardAccount->setTransferNotification(1);
        }
        return $rewardAccount->getTransferNotification();
    }
    public function getCancelConfig(){
        return $this->_helper()->getTransferconfig('allow_cancel');
    }
	
	 /**
     * xuanbinh 
     * get total point accumulate of customer
     * @return number|NULL
     */
    public function getTotalAccumulatePoint(){
    	$customer = Mage::helper('customer')->getCustomer();
    	$customerId = $customer->getId();
		
    	$rewardCustomer = Mage::getModel('rewardpoints/customer')->load($customerId, 'customer_id');
    	
    	if($rewardCustomer->getId()){
    		$pointBanlance 			= $rewardCustomer->getPointBalance();
    		$pointSpentBalance 		= $rewardCustomer->getSpentBalance();
    		$pointHoldingBalance 	= $rewardCustomer->getHoldingBalance();
    		
    		return Mage::helper('rewardpoints/point')->format($pointBanlance+$pointSpentBalance+$pointHoldingBalance);
    	}
    	return null;
    }
}