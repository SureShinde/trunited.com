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
 * RewardPoints Action Library Helper
 * 
 * @category    Magestore
 * @package     Magestore_RewardPoints
 * @author      Magestore Developer
 */
class Magestore_RewardPoints_Helper_Action extends Mage_Core_Helper_Abstract
{
    const XML_CONFIG_ACTIONS    = 'global/rewardpoints/actions';
    
    /**
     * reward points actions config
     * 
     * @var array
     */
    protected $_config = array();
    
    /**
     * Actions Array (code => label)
     * 
     * @var array
     */
    protected $_actions = null;
    
    public function __construct()
    {
        $actionConfig = (array)Mage::getConfig()->getNode(self::XML_CONFIG_ACTIONS);
        foreach ($actionConfig as $code => $model) {
            $this->_config[$code] = (string)$model;
        }
    }

    /**
     * @param $actionCode
     * @param $customer
     * @param null $object
     * @param array $extraContent
     * @return false|Mage_Core_Model_Abstract
     * @throws Exception
     */
    public function addTransaction($actionCode, $customer, $object = null, $extraContent = array())
    {

        Varien_Profiler::start('REWARDPOINTS_HELPER_ACTION::addTransaction');
        if (!$customer->getId()) {
            throw new Exception($this->__('Customer must be existed.'));
        }
        $actionModel = $this->getActionModel($actionCode);
        /** @var $actionModel Magestore_RewardPoints_Model_Action_Interface */
        $actionModel->setData(array(
            'customer'      => $customer,
            'action_object' => $object,
            'extra_content' => $extraContent
        ))->prepareTransaction();

        $transaction = Mage::getModel('rewardpoints/transaction');
        if (is_array($actionModel->getData('transaction_data'))) {
            $transaction->setData($actionModel->getData('transaction_data'));
        }

        $transaction->setData('point_amount', $actionModel->getPointAmount());
        $transaction->setData('product_credit', $actionModel->getProductCredit());
        $transaction->setData('product_credit_title', (int)$actionModel->getProductCreditTitle());

        if($object->getStatus() !== null && $object->getStatus() > 0)
            $transaction->setData('status', (int)$object->getStatus());

        if($actionCode == 'admin')
        {
            $transaction->setData('is_on_hold', $object->getIsOnHold() != null ? $object->getIsOnHold() : 0);
        }

        if (!$transaction->hasData('store_id')) {
            $transaction->setData('store_id', Mage::app()->getStore()->getId());
        }

        if ($actionCode == 'spending_order') {
            $transaction->createTransactionProductCredit(array(
                'customer_id' => $customer->getId(),
                'customer' => $customer,
                'customer_email' => $customer->getEmail(),
                'title' => $actionModel->getProductCreditTitle(),
                'action' => $actionCode,
                'action_type' => $actionModel->getActionType(),
                'created_time' => now(),
                'updated_time' => now(),
            ));
        } else {
            if ($actionModel->getPointAmount() >= 0) {
                if($actionCode == 'admin' && $object->getIsOnHold() == 1)
                {
                    $transaction->createTransaction(array(
                        'customer_id'   => $customer->getId(),
                        'customer'      => $customer,
                        'customer_email'=> $customer->getEmail(),
                        'title'         => $actionModel->getTitle(),
                        'action'        => $actionCode,
                        'action_type'   => $actionModel->getActionType(),
                        'created_time'  => now(),
                        'updated_time'  => now(),
                        'is_on_hold'    => $object->getIsOnHold(),
                        'status'        => Magestore_RewardPoints_Model_Transaction::STATUS_ON_HOLD
                    ));
                } else {
                    $transaction->createTransaction(array(
                        'customer_id'   => $customer->getId(),
                        'customer'      => $customer,
                        'customer_email'=> $customer->getEmail(),
                        'title'         => $actionModel->getTitle(),
                        'action'        => $actionCode,
                        'action_type'   => $actionModel->getActionType(),
                        'created_time'  => now(),
                        'updated_time'  => now(),
                    ));
                }
            }
        }

        if ($actionModel->getProductCredit()) {
            $is_exist = false;
            if($actionModel->getActionObject()->getData('customer_exist')){
                $is_exist = $actionModel->getActionObject()->getData('customer_exist');
            }

            $email = '';
            if($actionModel->getActionObject()->getData('email')){
                $email = $actionModel->getActionObject()->getData('email');
            }

            $is_send = false;
            if($actionModel->getActionObject()->getData('is_send')){
                $is_send = $actionModel->getActionObject()->getData('is_send');
            }

            $message = '';
            if($actionModel->getActionObject()->getData('message')){
                $message = $actionModel->getActionObject()->getData('message');
            }
            $transaction->createTransactionProductCredit(array(
                'customer_id' => $customer->getId(),
                'customer' => $customer,
                'customer_email' => $customer->getEmail(),
                'title' => $actionModel->getTitle(),
                'action' => $actionCode,
                'action_type' => $actionModel->getActionType(),
                'created_time' => now(),
                'updated_time' => now(),
                'store_id' => Mage::app()->getStore()->getStoreId(),
                'customer_exist' => $is_exist,
                'email' => $email,
                'is_send' => $is_send,
                'message' => $message,
            ));
        }

        Varien_Profiler::stop('REWARDPOINTS_HELPER_ACTION::addTransaction');
        return $transaction;
    }
    
    /**
     * get Class Model for Action by code
     * 
     * @param string $code
     * @return string
     * @throws Exception
     */
    public function getActionModelClass($code) {
        if (isset($this->_config[$code]) && $this->_config[$code]) {
            return $this->_config[$code];
        }
        throw new Exception($this->__('Action code %s not found on config.', $code));
    }
    
    /**
     * get action Model by Code
     * 
     * @param string $code
     * @return Magestore_RewardPoints_Model_Action_Interface
     * @throws Exception
     */
    public function getActionModel($code) {
        $modelClass = $this->getActionModelClass($code);
        $model = Mage::getSingleton($modelClass);
        if (!($model instanceof Magestore_RewardPoints_Model_Action_Interface)) {
            throw new Exception($this->__('Action model need implements from %s',
                'Magestore_RewardPoints_Model_Action_Interface')
            );
        }
        if (!$model->getCode()) {
            $model->setCode($code);
        }
        return $model;
    }
    
    /**
     * get actions hash options
     * 
     * @return array
     */
    public function getActionsHash() {
        if (is_null($this->_actions)) {
            $this->_actions = array();
            foreach ($this->_config as $code => $class) {
                try {
                    $model = $this->getActionModel($code);
                    $this->_actions[$code] = $model->getActionLabel();
                } catch (Exception $e) {
                    Mage::logException($e);
                }
            }
        }
        return $this->_actions;
    }
    
    /**
     * get actions array options
     * 
     * @return array
     */
    public function getActionsArray() {
        $actions = array();
        foreach ($this->getActionsHash() as $value => $label) {
            $actions[] = array(
                'value' => $value,
                'label' => $label
            );
        }
        return $actions;
    }
}
