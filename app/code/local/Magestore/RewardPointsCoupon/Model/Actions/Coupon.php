<?php

class Magestore_RewardPointsCoupon_Model_Actions_Coupon extends Magestore_RewardPoints_Model_Action_Abstract implements Magestore_RewardPoints_Model_Action_Interface {

    public function getPointAmount() {
        $codeObject = $this->getData('action_object');
        return (int) $codeObject['point_balance'];
    }
    
    public function getActionLabel() {
        return Mage::helper('rewardpointscoupon')->__('Receive point(s) for redeem Reward Code');
    }

    public function getActionType() {
        return Magestore_RewardPoints_Model_Transaction::ACTION_TYPE_EARN;
    }

    public function getTitle() {
        return Mage::helper('rewardpointscoupon')->__('Receive point(s) for redeem Reward Code');
    }

    /**
     * prepare data of action to storage on transactions
     * the array that returned from function $action->getData('transaction_data')
     * will be setted to transaction model
     * 
     * @return Magestore_RewardPoints_Model_Action_Interface
     */
    public function prepareTransaction() {
        $codeObject = $this->getData('action_object');
        $transactionData = array(
            'status' => Magestore_RewardPoints_Model_Transaction::STATUS_COMPLETED,
            'store_id' => Mage::app()->getStore()->getId(),
            'extra_content' => $codeObject['name'],
        );
        $expireDays = $codeObject['point_expired'];
        if($expireDays > 0)
            $transactionData['expiration_date'] = $this->getExpirationDate($expireDays);
        $this->setData('transaction_data', $transactionData);
        return parent::prepareTransaction();
    }
}