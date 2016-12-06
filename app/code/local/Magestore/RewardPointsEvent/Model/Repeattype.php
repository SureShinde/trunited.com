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
class Magestore_RewardPointsEvent_Model_Repeattype extends Varien_Object {

    const TYPE_NONE = '0';
    const TYPE_YEAR = '1';
    const TYPE_MONTH = '2';
    const TYPE_DAY = '3';

    static public function getOptionArray() {
        return array(
            self::TYPE_NONE => Mage::helper('rewardpointsevent')->__('None'),
            self::TYPE_YEAR => Mage::helper('rewardpointsevent')->__('Yearly'),
            self::TYPE_MONTH => Mage::helper('rewardpointsevent')->__('Monthly'),
            self::TYPE_DAY => Mage::helper('rewardpointsevent')->__('Weekly'),
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