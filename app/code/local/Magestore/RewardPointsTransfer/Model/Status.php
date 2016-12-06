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
 * @package     Magestore_RewardPointsTransfer
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * RewardPointsTransfer Status Model
 * 
 * @category    Magestore
 * @package     Magestore_RewardPointsTransfer
 * @author      Magestore Developer
 */
class Magestore_RewardPointsTransfer_Model_Status extends Varien_Object {

    const STATUS_HOLDING = 1;
    const STATUS_PENDING = 2;
    const STATUS_COMPLETED = 3;
    const STATUS_CANCEL = 4;

    /**
     * get model option as array
     *
     * @return array
     */
    static public function getOptionArray() {
        return array(
            self::STATUS_PENDING => Mage::helper('rewardpointstransfer')->__('Pending'),
            self::STATUS_HOLDING => Mage::helper('rewardpointstransfer')->__('Holding'),
            self::STATUS_COMPLETED => Mage::helper('rewardpointstransfer')->__('Completed'),
            self::STATUS_CANCEL => Mage::helper('rewardpointstransfer')->__('Cancel')
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