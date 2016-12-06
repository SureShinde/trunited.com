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
 * @package     Magestore_RewardPointsEvent
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * Rewardpointsevent Resource Model
 * 
 * @category    Magestore
 * @package     Magestore_RewardPointsEvent
 * @author      Magestore Developer
 */
class Magestore_RewardPointsEvent_Model_Mysql4_Rewardpointsevent extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {
        $this->_init('rewardpointsevent/rewardpointsevent', 'event_id');
    }
    public function addEventTransaction($customers, $event){
        $select = $event->getCustomerApply();
        
        $point_amount = $event->getPointAmount();
//        $store_id = $event->getStoreId();
        $sendmail = $event->getEnableEmail();
        $emailTemplate = $event->getEmailTemplateId();
        $title = $event->getTitle();
        $message = $event->getMessage();
        $expire_date = $this->getExpirationDate($event->getExpireDay());
        
        $dataTransaction = array();
        $recipients = array();
        $recipientsTransaction = array();
        $enable_email = $event->getEnableEmail();
        $statusLabel = Mage::getModel('rewardpoints/transaction')->getStatusHash();
        $write = $this->_getWriteAdapter();
        $write->beginTransaction();
        try{
            foreach($customers as $customer){
                if($select == Magestore_RewardPointsEvent_Model_Scope::SCOPE_CUSTOMER){
                    if(!$event->checkRule($customer)) continue;
                }elseif($select == Magestore_RewardPointsEvent_Model_Scope::SCOPE_CSV){
                    $customer->setEntityId($customer->getCustomerId());
                }
                if($customer->getStoreId()) $store_id = $customer->getStoreId();
                else $store_id = Mage::app()->getDefaultStoreView()->getId();
                $customer_id = $customer->getEntityId();
                $customer_email = $customer->getEmail();
                $customer_name = $customer->getName();
                $preTransaction = array(
                    'customer_id'       => $customer_id,
                    'customer_email'    => $customer_email,
                    'title'             => $this->getTitleTransaction()."'".$title."'",
                    'action'            => 'event',
                    'action_type'       => Magestore_RewardPoints_Model_Transaction::ACTION_TYPE_EARN,
                    'status'            => Magestore_RewardPoints_Model_Transaction::STATUS_COMPLETED,
                    'store_id'          => $store_id,
                    'extra_content'     => 'event_id='.$event->getId(),
                    'point_amount'      => $point_amount,
                    'point_used'        => 0,
                    'real_point'        => $point_amount,
                    'expiration_date'   => $expire_date,
                    'created_time'      => now(),
                    'updated_time'      => now(),
                );
                $preReward = array(); 

                $rewardAccount = Mage::helper('rewardpoints/customer')->getAccountByCustomerId($customer_id);
                if (!$rewardAccount->getId()) {
                    $rewardAccount->setCustomerId($customer_id)
                        ->setData('point_balance', 0)
                        ->setData('holding_balance', 0)
                        ->setData('spent_balance', 0)
                        ->setData('is_notification', 1)
                        ->setData('expire_notification', 1)
                        ->save();
                }
                $preTransaction['reward_id']  = $rewardAccount->getId();
                $point_balance = $rewardAccount->getPointBalance();
                $maxBalance = (int)Mage::getStoreConfig(Magestore_RewardPoints_Model_Transaction::XML_PATH_MAX_BALANCE, $store_id);
                if ($maxBalance > 0 && $point_amount > 0
                    && $point_balance + $point_amount > $maxBalance
                ) {
                    if ($maxBalance > $point_balance) {
                        $preTransaction['point_amount']  = $maxBalance - $point_balance;
                        $preTransaction['real_point']  = $maxBalance - $point_balance;
                        $preReward['point_balance'] = $maxBalance;
                    } else {
                        continue;
                    }
                } else {
                    $preReward['point_balance'] = $point_balance + $point_amount;
                }

                $dataTransaction[] = $preTransaction;
                if($enable_email) $recipients[$customer_email] = $customer_name;
                if(!$enable_email && $rewardAccount->getIsNotification()){
                    $recipientsTransaction[$customer_email] = array(
                        'store_id'     => $store_id,
                        'customer'  => array('name'=>$customer_name),
                        'point_amount'  => $preTransaction['point_amount'],
                        'point_balance' => $preReward['point_balance'],
                        'status'    => $statusLabel[$preTransaction['status']],
                    );
                }
                $write->update($this->getTable('rewardpoints/customer'), $preReward, "customer_id = $customer_id");
                if (count($dataTransaction) >= 1000) {
                    $write->insertMultiple($this->getTable('rewardpoints/transaction'), $dataTransaction);
                    $dataTransaction = array();
                }
            }
            if (!empty($dataTransaction)) {
                $write->insertMultiple($this->getTable('rewardpoints/transaction'), $dataTransaction);
            }
            $write->commit();
        }catch(Exception $e){
            $write->rollback();
            throw $e;
        }
        unset($customers);
        
        if(!$enable_email){
            foreach($recipientsTransaction as $email=>$info){
                $store = Mage::app()->getStore($info['store_id']);
                $titleMail = $this->getTitleTransaction().$title;
                $customer = new Varien_Object();
                $customer->setData('name', $info['customer']['name']);
                
                $translate = Mage::getSingleton('core/translate');
                $translate->setTranslateInline(false);

                Mage::getModel('core/email_template')
                    ->setDesignConfig(array(
                        'area'  => 'frontend',
                        'store' => $store->getId()
                    ))->sendTransactional(
                        Mage::getStoreConfig(Magestore_RewardPoints_Model_Transaction::XML_PATH_EMAIL_UPDATE_BALANCE_TPL, $store),
                        Mage::getStoreConfig(Magestore_RewardPoints_Model_Transaction::XML_PATH_EMAIL_SENDER, $store),
                        $email,
                        $info['customer']['name'],
                        array(
                            'store'     => $store,
                            'customer'  => $customer,
                            'title'     => $titleMail,
                            'amount'    => $info['point_amount'],
                            'total'     => $rewardAccount->getPointBalance(),
                            'point_amount'  => Mage::helper('rewardpoints/point')->format($info['point_amount'], $store),
                            'point_balance' => Mage::helper('rewardpoints/point')->format($info['point_balance'], $store),
                            'status'    => $info['status'],
                        )
                    );

                $translate->setTranslateInline(true);
            }
            return; 
        }
        
        $store_id = Mage::app()->getDefaultStoreView()->getId();
        $store = Mage::app()->getStore($store_id);
        if (!Mage::getStoreConfigFlag(Magestore_RewardPoints_Model_Transaction::XML_PATH_EMAIL_ENABLE, $store_id)) {
            return;
        }
        $sender = Mage::getStoreConfig(Magestore_RewardPoints_Model_Transaction::XML_PATH_EMAIL_SENDER, $store);
        
        $emailTemplate = $event->getEmailTemplateId();
        if($emailTemplate == '') $emailTemplate = 'rewardpoints_eventplugin_email_event';
        $point_amount = $event->getPointAmount();
        $translate = Mage::getSingleton('core/translate');
        $translate->setTranslateInline(false);
        foreach($recipients as $email=>$name){
            Mage::getModel('core/email_template')
                ->setDesignConfig(array(
                    'area'  => 'frontend',
                    'store' => $store->getId()
                ))->sendTransactional(
                    $emailTemplate,
                    $sender,
                    $email,
                    $name,
                    array(
                        'store'     => $store,
                        'customer_name'  => $name,
                        'title'     => $title,
                        'message'   => $message,
                        'point_amount'  => Mage::helper('rewardpoints/point')->format($point_amount, $store),
                    )
                );
        }
        
        $translate->setTranslateInline(true);
        return true;
    }
    /**
     * Calculate Expiration Date for transaction
     * 
     * @param int $days Days to be expired
     * @return null|string
     */
    public function getExpirationDate($days = 0)
    {
        if ($days <= 0) {
            return null;
        }
        $timestamp = time() + $days * 86400;
        return date('Y-m-d H:i:s', $timestamp);
    }
    public function getTitleTransaction(){
        return 'Receive points from event ';
    }
}