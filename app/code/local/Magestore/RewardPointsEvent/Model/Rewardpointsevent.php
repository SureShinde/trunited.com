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
 * Rewardpointsevent Model
 * 
 * @category    Magestore
 * @package     Magestore_RewardPointsEvent
 * @author      Magestore Developer
 */
class Magestore_RewardPointsEvent_Model_Rewardpointsevent extends Mage_Rule_Model_Rule {

    public function _construct() {
        parent::_construct();
        $this->_init('rewardpointsevent/rewardpointsevent');
        $this->setIdFieldName('event_id');
    }

    public function getConditionsInstance() {
        return Mage::getModel('rewardpointsevent/rule_condition_combine');
    }

    public function loadPost(array $rule) {
        $arr = $this->_convertFlatToRecursive($rule);
        if (isset($arr['conditions'])) {
            $this->getConditions()->setConditions(array())->loadArray($arr['conditions'][1]);
        }
        return $this;
    }

    protected function _beforeSave() {
        parent::_beforeSave();
        if ($this->hasWebsiteIds()) {
            $websiteIds = $this->getWebsiteIds();
            if (is_array($websiteIds) && !empty($websiteIds)) {
                $this->setWebsiteIds(implode(',', $websiteIds));
            }
        }

        if ($this->hasCustomerGroupIds()) {
            $groupIds = $this->getCustomerGroupIds();
            if (is_array($groupIds) && !empty($groupIds)) {
                $this->setCustomerGroupIds(implode(',', $groupIds));
            }
        }

        return $this;
    }

    /**
     * Fix error when load and save with collection
     */
    protected function _afterLoad() {
        $this->setConditions(null);
        return parent::_afterLoad();
    }

    public function checkRule($customer) {
        if ($this->getStatus() == Magestore_RewardPointsEvent_Model_Status::STATUS_ENABLED) {
//            $this->_afterLoad();
            if (!$customer->getId()) {
                $conditions = $this->getConditions()->asArray();
                if (array_key_exists('conditions', $conditions))
                    return false;
            }
            return $this->validate($customer);
        }
        return false;
    }
    /**
     * Send event email to customer
     * 
     * @param type $email
     * @param type $name
     * @param type $emailTemplate
     * @param type $point_amount
     * @param type $title
     * @param type $message
     * @param type $store_id
     * @return \Magestore_RewardPointsEvent_Model_Transaction
     */
    public function sendEventEmail($sender, $email, $name, $emailTemplate, $point_amount, $title, $message, $store){
        $translate = Mage::getSingleton('core/translate');
        $translate->setTranslateInline(false);
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
        
        $translate->setTranslateInline(true);
        return $this;
    }
}
