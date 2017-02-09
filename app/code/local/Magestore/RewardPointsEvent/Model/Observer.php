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
 * RewardPointsEvent Observer Model
 *
 * @category    Magestore
 * @package     Magestore_RewardPointsEvent
 * @author      Magestore Developer
 */
class Magestore_RewardPointsEvent_Model_Observer
{
    /**
     * process controller_action_predispatch event
     *
     * @return Magestore_RewardPointsEvent_Model_Observer
     */
    public function controllerActionPredispatch($observer)
    {
        $action = $observer->getEvent()->getControllerAction();
        return $this;
    }

    public function customerCsvSaveAfter($observer)
    {
        if (!Mage::getStoreConfig('rewardpoints/eventplugin/enable')) return;
        $customer = $observer->getCustomer();
        $evented_points = Mage::getResourceModel('rewardpoints/transaction_collection')
            ->addFieldToFilter('action', 'event')
            ->addFieldToFilter('customer_id', $customer->getId());
        if (count($evented_points))
            return;
        $events = Mage::getModel('rewardpointsevent/rewardpointsevent')->getCollection()
            ->addFieldToFilter('is_running', true)
            ->addFieldToFilter('is_apply', true);
        foreach ($events as $event) {
            if (!$this->_checkRuleCustomer($customer, $event)) continue;
            $this->addEventTransaction($customer, $event);
        }
        return $this;
    }
//    public function customerRegisterSuccess($observer){
//        if(!Mage::getStoreConfig('rewardpoints/eventplugin/enable')) return;
//        $customer = $observer->getCustomer();
//        $evented_points = Mage::getResourceModel('rewardpoints/transaction_collection')
//                ->addFieldToFilter('action', 'event')
//                ->addFieldToFilter('customer_id', $customer->getId());
//        if (count($evented_points))
//            return;
//        $events = Mage::getModel('rewardpointsevent/rewardpointsevent')->getCollection()
//                ->addFieldToFilter('status', 1);
//        foreach($events as $event){
//            if(!$this->_checkRuleCustomer($customer, $event)) continue;
//            $this->addEvent($customer, $event);
//        }
//        return $this;
//    }
    public function customerCommitSaveAfter($observer)
    {
        $customer_reg = $observer->getCustomer();
//        if (version_compare(Mage::getVersion(), '1.6.0.0', '>=')) {
//            if($customer_reg->getStoreId()!= '0') return;
//        }
        if (!Mage::getStoreConfig('rewardpoints/eventplugin/enable')) return;
        $customer = Mage::getModel('customer/customer')->load($customer_reg->getId());
        if (!$customer->getId() || Mage::app()->getRequest()->getActionName() == 'editPost')
            return;
        $evented_points = Mage::getResourceModel('rewardpoints/transaction_collection')
            ->addFieldToFilter('action', 'event')
            ->addFieldToFilter('customer_id', $customer->getId());
        if (count($evented_points))
            return;
        $events = Mage::getModel('rewardpointsevent/rewardpointsevent')->getCollection()
            ->addFieldToFilter('is_running', true)
            ->addFieldToFilter('is_apply', true);
        foreach ($events as $event) {
            if (!$this->_checkRuleCustomer($customer, $event)) continue;
            $this->addEventTransaction($customer, $event);
        }
    }
//    public function addEvent($customer, $event){
//        $repeatType = $event->getRepeatType();
//        $repeatTime = $event->getRepeatTime();
//        $dateRepeat = $event->getDateRepeat();
//        $dateCommit = $event->getDateCommit();
//        $dateApply = $event->getApplyFrom();
//        $dateApplyTo = $event->getApplyTo();
//
//        if($dateApplyTo == null) $dateApplyTo = $dateApply;
//        if($repeatType == Magestore_RewardPointsEvent_Model_Repeattype::TYPE_NONE){
//            if($this->compareDateEvent($dateApply) <= 0 && $this->compareDateEvent($dateApplyTo)>=0) $this->addEventTransaction($customer, $event);
//            return;
//        }elseif($repeatType == Magestore_RewardPointsEvent_Model_Repeattype::TYPE_YEAR) $timeLeter = "year";
//        elseif($repeatType == Magestore_RewardPointsEvent_Model_Repeattype::TYPE_MONTH) $timeLeter = "month";
//        else $timeLeter = "day";
//
//        if($this->checkEventRule($dateApply, $dateApplyTo, $timeLeter, $repeatTime, $dateCommit)) $this->addEventTransaction($customer, $event);
//    }
    public function addEventTransaction($customer, $event)
    {
        Mage::helper('rewardpoints/action')->addTransaction('event', $customer, $event, array('event_id' => $event->getId()));
        if ($event->getEnableEmail()) {
            $emailTemplate = $event->getEmailTemplateId();
            if ($emailTemplate == '') $emailTemplate = 'rewardpoints_eventplugin_email_event';
            if ($customer->getStoreId()) $store_id = $customer->getStoreId();
            else $store_id = Mage::app()->getDefaultStoreView()->getId();
            $store = Mage::app()->getStore($store_id);
            if (!Mage::getStoreConfigFlag(Magestore_RewardPoints_Model_Transaction::XML_PATH_EMAIL_ENABLE, $store_id)) {
//                continue;
            }
            $sender = Mage::getStoreConfig(Magestore_RewardPoints_Model_Transaction::XML_PATH_EMAIL_SENDER, $store);
            try {
                $event->sendEventEmail($sender, $customer->getEmail(), $customer->getName(), $emailTemplate, $event->getPointAmount(), $event->getTitle(), $event->getMessage(), $store);
            } catch (Exception $e) {
            }
        }
    }

    protected function _checkRuleCustomer($customer, $event)
    {
        $select = $event->getCustomerApply();
        if ($select == Magestore_RewardPointsEvent_Model_Scope::SCOPE_GROUPS) {
            $group = explode(',', $event->getCustomerGroupIds());
            if (in_array($customer->getGroupId(), $group)) return true;
            else return false;
        } elseif ($select == Magestore_RewardPointsEvent_Model_Scope::SCOPE_CUSTOMER) {
            return $event->checkRule($customer);
        } else return true;
    }
//    public function compareDateEvent($date1, $date2=null){
//        return Mage::getModel('rewardpointsevent/cron')->compareDateEvent($date1, $date2);
//    }
//    public function checkEventRule($datefrom, $dateto, $timeLeter, $repeatTime, $dateCommit){
//        $i = 1;
//        for($i;;$i++){
//            if($this->compareDateEvent($datefrom, $dateCommit)<=0 && $this->compareDateEvent($dateCommit)<=0 && $this->compareDateEvent($dateto) >=0){
//                return true;
//            }
//            if($this->compareDateEvent($datefrom)>0) return false;
//            $datefrom = date('Y-m-d', strtotime("$datefrom +$repeatTime $timeLeter"));
//            $dateto = date('Y-m-d', strtotime("$dateto +$repeatTime $timeLeter"));
//        }
//    }
}