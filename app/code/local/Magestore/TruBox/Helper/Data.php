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
 * @module     RewardPoints
 * @author      Magestore Developer
 *
 * @copyright   Copyright (c) 2016 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 *
 */

/**
 * RewardPoints Helper
 *
 * @category    Magestore
 * @package     Magestore_RewardPoints
 * @author      Magestore Developer
 */
class Magestore_TruBox_Helper_Data extends Mage_Core_Helper_Abstract
{
    const XML_PATH_ENABLE = 'rewardpoints/general/enable';

    /**
     * get rewards points label to show on Account Navigation
     *
     * @return string
     */
    public function getTruboxLabel()
    {
        return $this->__('My TruBox');
    }

    public function getCurrentTruBoxId() {
        $customer = Mage::getSingleton('customer/session')->getCustomer();
        $id = $customer->getId();
        $truBox = Mage::getModel('trubox/trubox')->getCollection()->addFieldToFilter('status', 'open')
            ->addFieldToFilter('customer_id', $id)->getFirstItem();
        $truBoxId = $truBox->getTruboxId();
        return $truBoxId;
    }

}
