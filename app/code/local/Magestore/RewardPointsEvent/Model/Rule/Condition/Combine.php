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
 * RewardpointsEvent Model
 * 
 * @category    Magestore
 * @package     Magestore_RewardPointsEvent
 * @author      Magestore Developer
 * 
 */
class Magestore_RewardPointsEvent_Model_Rule_Condition_Combine extends Mage_Rule_Model_Condition_Combine {

    public function __construct() {
        parent::__construct();
        $this->setType('rewardpointsevent/rule_condition_combine');
    }

    public function getNewChildSelectOptions() {
        $customerCondition = Mage::getModel('rewardpointsevent/rule_condition_customer');
        $customerAttributes = $customerCondition->loadAttributeOptions()->getAttributeOption();
        $attributes = array();
        foreach ($customerAttributes as $code => $label) {
            $attributes[] = array('value' => 'rewardpointsevent/rule_condition_customer|' . $code, 'label' => $label);
        }
        $addressCondition = Mage::getModel('rewardpointsevent/rule_condition_address');
        $addressAttributes = $addressCondition->loadAttributeOptions()->getAttributeOption();
        $address = array();
        foreach ($addressAttributes as $code => $label) {
            $address[] = array('value' => 'rewardpointsevent/rule_condition_address|' . $code, 'label' => $label);
        }
        $conditions = parent::getNewChildSelectOptions();
        $conditions = array_merge_recursive($conditions, array(
            array(
                'label' => Mage::helper('rewardpointsevent')->__('Customer Attribute'),
                'value' => $attributes
            ),
            array(
                'value' => $address,
                'label' => Mage::helper('rewardpointsevent')->__('Customer Address'),
            ),
        ));
        return $conditions;
    }

    public function collectValidatedAttributes($customerCollection) {
        foreach ($this->getConditions() as $condition) {
            $condition->collectValidatedAttributes($customerCollection);
        }
        return $this;
    }

}
