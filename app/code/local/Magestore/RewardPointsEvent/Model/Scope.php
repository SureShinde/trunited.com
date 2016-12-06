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
 * RewardPointsEvent Scope Model
 * 
 * @category    Magestore
 * @package     Magestore_RewardPointsEvent
 * @author      Magestore Developer
 */
class Magestore_RewardPointsEvent_Model_Scope extends Varien_Object {

    const SCOPE_GLOBAL = '0';
    const SCOPE_GROUPS = '1';
    const SCOPE_CUSTOMER = '2';
    const SCOPE_CSV = '3';

    static public function getOptionArray() {
        return array(
            self::SCOPE_GLOBAL => Mage::helper('rewardpointsevent')->__('Choose Global'),
            self::SCOPE_GROUPS => Mage::helper('rewardpointsevent')->__('Filter by Websites and Customer Groups'),
            self::SCOPE_CUSTOMER => Mage::helper('rewardpointsevent')->__('Configure Customer Conditions'),
            self::SCOPE_CSV => Mage::helper('rewardpointsevent')->__('Import from a CSV file'),
        );
    }

    static public function getOptions() {
        $options = array();
        foreach (self::getOptionArray() as $value => $label)
            $options[] = array(
                'value' => $value,
                'label' => $label
            );
        return $options;
    }

    public function toOptionArray() {
        return self::getOptions();
    }

}