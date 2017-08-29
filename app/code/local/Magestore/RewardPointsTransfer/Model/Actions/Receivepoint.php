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
 * Action change points by admin
 * 
 * @category    Magestore
 * @package     Magestore_RewardPoints
 * @author      Magestore Developer
 */
class Magestore_RewardPointsTransfer_Model_Actions_Receivepoint extends Magestore_RewardPoints_Model_Action_Abstract implements Magestore_RewardPoints_Model_Action_Interface {

    /**
     * Calculate and return point amount that admin changed
     * 
     * @return int
     */
    public function getPointAmount() {
        $actionObject = $this->getData('action_object');
        if (!is_object($actionObject)) {
            return 0;
        }
        return (int) $actionObject['point_amount'];
    }

    /**
     * get Label for this action, this is the reason to change 
     * customer reward points balance
     * 
     * @return string
     */
    public function getActionLabel() {
        return Mage::helper('rewardpointstransfer')->__('Receive points from point transfer');
    }

    public function getActionType() {
        return Magestore_RewardPoints_Model_Transaction::ACTION_TYPE_EARN;
    }

    /**
     * get Text Title for this action, used when create an transaction
     * 
     * @return string
     */
    public function getTitle() {
        return Mage::helper('rewardpointstransfer')->__('Receive points from your friends');
    }

    /**
     * get HTML Title for action depend on current transaction
     * 
     * @param Magestore_RewardPoints_Model_Transaction $transaction
     * @return string
     */
    public function getTitleHtml($transaction = null) {
        if (is_null($transaction)) {
            return $this->getTitle();
        }
        if (Mage::app()->getStore()->isAdmin()) {
            return '<strong>' . $transaction->getExtraContent() . ': </strong>' . $transaction->getTitle();
        }
        return $transaction->getTitle();
    }

    /**
     * prepare data of action to storage on transactions
     * the array that returned from function $action->getData('transaction_data')
     * will be setted to transaction model
     * 
     * @return Magestore_RewardPoints_Model_Action_Interface
     */
    public function prepareTransaction() {
        $actionObject = $this->getData('action_object');
        $extraContent = $this->getData('extra_content');
        if (isset($extraContent['notice']))
            $extraContent['notice'] = htmlspecialchars($extraContent['notice']);

        if (isset($extraContent['extra_content']) && is_array($extraContent['extra_content'])) {
            $extra_content = new Varien_Object($extraContent['extra_content']);
            $extraContent['extra_content'] = $extra_content->serialize(null, '=', '&', '');
        }
        $extraContent = new Varien_Object($extraContent);
        $transactionData = array();
        $transactionData['status'] = Magestore_RewardPoints_Model_Transaction::STATUS_COMPLETED;
        $transactionData['store_id'] = $actionObject['store_id'];
        $transactionData['extra_content'] = $extraContent->serialize(null, '=', '&', '');
        $this->setData('transaction_data', $transactionData);
        return parent::prepareTransaction();
    }

}
