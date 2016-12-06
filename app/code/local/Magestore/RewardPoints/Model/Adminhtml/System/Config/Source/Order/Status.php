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
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * Rewardpoints Config Field Clone Block
 *
 * @category    Magestore
 * @package     Magestore_RewardPoints
 * @author      Magestore Developer
 */
class Magestore_RewardPoints_Model_Adminhtml_System_Config_Source_Order_Status
{
    const ORDER_STATE_PENDING = 'pending';

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array(
                'value' => self::ORDER_STATE_PENDING,
                'label' => ucfirst(self::ORDER_STATE_PENDING)
            ),
            array(
                'value' => Mage_Sales_Model_Order::STATE_PROCESSING,
                'label' => ucfirst(Mage_Sales_Model_Order::STATE_PROCESSING)
            ),
            array(
                'value' => Mage_Sales_Model_Order::STATE_COMPLETE,
                'label' => ucfirst(Mage_Sales_Model_Order::STATE_COMPLETE)
            ),
        );
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        return array(
            self::ORDER_STATE_PENDING => ucfirst(self::ORDER_STATE_PENDING),
            Mage_Sales_Model_Order::STATE_PROCESSING => ucfirst(Mage_Sales_Model_Order::STATE_PROCESSING),
            Mage_Sales_Model_Order::STATE_COMPLETE => ucfirst(Mage_Sales_Model_Order::STATE_COMPLETE),
        );
    }
}
