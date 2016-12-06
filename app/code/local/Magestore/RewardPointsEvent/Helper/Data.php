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
 * RewardPointsEvent Helper
 * 
 * @category    Magestore
 * @package     Magestore_RewardPointsEvent
 * @author      Magestore Developer
 */
class Magestore_RewardPointsEvent_Helper_Data extends Mage_Core_Helper_Abstract {

    const XML_PATH_ENABLE = 'rewardpoints/eventplugin/enable';

    /**
     * check reward points system is enabled
     * 
     * @param mixed $store
     * @return boolean
     */
    public function isEnable($store = null) {
        return Mage::getStoreConfigFlag(self::XML_PATH_ENABLE, $store);
    }

}
