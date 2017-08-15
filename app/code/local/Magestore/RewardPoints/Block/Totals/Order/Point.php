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
 * Rewardpoints Total Point Spend Block
 * You should write block extended from this block when you write plugin
 * 
 * @category    Magestore
 * @package     Magestore_RewardPoints
 * @author      Magestore Developer
 */
class Magestore_RewardPoints_Block_Totals_Order_Point extends Magestore_RewardPoints_Block_Template
{
    /**
     * add points value into order total
     *     
     */
    public function initTotals()
    {
        if (!$this->isEnable()) {
            return $this;
        }
        $totalsBlock = $this->getParentBlock();
        $order = $totalsBlock->getOrder();
		$bonusPoints = $order->getRewardpointsBonus();
        $bonusPickupPoints = $order->getRewardpointsPickup();
        if(!$bonusPoints)
            $bonusPoints = 0;

        if(!$bonusPickupPoints)
            $bonusPickupPoints = 0;
		
        if ($order->getRewardpointsEarn()) {
            $totalsBlock->addTotal(new Varien_Object(array(
                'code' => 'rewardpoints_earn_label',
                'label' => $this->__('Earn Points'),
                'value' => Mage::helper('rewardpoints/point')->format($order->getRewardpointsEarn() - $bonusPoints - $bonusPickupPoints),
                'base_value' => Mage::helper('rewardpoints/point')->format($order->getRewardpointsEarn() - $bonusPoints - $bonusPickupPoints),
                'is_formated' => true,
                    )), 'subtotal');
        }
        if ($bonusPoints) {
            $totalsBlock->addTotal(new Varien_Object(array(
                'code' => 'rewardpoints_bonus_label',
                'label' => $this->__('Bonus'),
                'value' => Mage::helper('rewardpoints/point')->format($bonusPoints),
                'base_value' => Mage::helper('rewardpoints/point')->format($bonusPoints),
                'is_formated' => true,
                    )), 'subtotal');
        }

        if ($bonusPickupPoints) {
            $totalsBlock->addTotal(new Varien_Object(array(
                'code'  => 'rewardpoints_bonus_label',
                'label' => Mage::helper('storepickup')->getDataConfig('bonus_label'),
                'value' => Mage::helper('rewardpoints/point')->format($bonusPickupPoints),
                'base_value' => Mage::helper('rewardpoints/point')->format($bonusPickupPoints),
                'is_formated'   => true,
            )), 'subtotal');
        }

        if ($order->getRewardpointsDiscount()>=0.0001) {
            $totalsBlock->addTotal(new Varien_Object(array(
                'code'  => 'rewardpoints',
                'label' => $this->__('Use points on spend'),
                'value' => -$order->getRewardpointsDiscount(),
                'base_value' => -$order->getRewardpointsBaseDiscount(),
            )), 'subtotal');
        }
    }
}
