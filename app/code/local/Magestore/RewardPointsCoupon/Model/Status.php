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
 * @package     Magestore_RewardPointsCoupon
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * RewardPointsCoupon Status Model
 * 
 * @category    Magestore
 * @package     Magestore_RewardPointsCoupon
 * @author      Magestore Developer
 */
class Magestore_RewardPointsCoupon_Model_Status extends Varien_Object {
    const STATUS_INACTIVE = 0;
    const STATUS_ACTIVE = 1;    
    const STATUS_USED = 2;
    const STATUS_EXPIRED = 3;

    /**
     * get model option as array
     *
     * @return array
     */
    static public function getOptionArray() {
        return array(
            self::STATUS_ACTIVE => Mage::helper('rewardpointscoupon')->__('Active'),
            self::STATUS_INACTIVE => Mage::helper('rewardpointscoupon')->__('Inactive')
        );
    }

    /**
     * get model option hash as array
     *
     * @return array
     */
    static public function getOptionHash() {
        $options = array();
        foreach (self::getOptionArray() as $value => $label) {
            $options[] = array(
                'value' => $value,
                'label' => $label
            );
        }
        return $options;
    }

}
