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
 * Rewardpoints Total Label Block
 * 
 * @category    Magestore
 * @package     Magestore_RewardPoints
 * @author      Magestore Developer
 */
class Magestore_RewardPoints_Block_Adminhtml_Totals_Order_Label extends Mage_Adminhtml_Block_Sales_Order_Totals_Item
{
    /**
     * add points label into order total
     *     
     */
    public function initTotals()
    {
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
                'code'  => 'rewardpoints_earn_label',
                'label' => $this->__('Earned Points'),
                'value' => $order->getRewardpointsEarn() - $bonusPoints - $bonusPickupPoints,
                'strong'        => true,
                'is_formated'   => true,
            )), 'subtotal');
        }
        if ($bonusPoints) {
            $totalsBlock->addTotal(new Varien_Object(array(
                'code'  => 'rewardpoints_bonus_label',
                'label' => $this->__('Bonus'),
                'value' => $bonusPoints,
                'strong'        => true,
                'is_formated'   => true,
            )), 'subtotal');
        }

        if ($bonusPickupPoints) {
            $totalsBlock->addTotal(new Varien_Object(array(
                'code'  => 'rewardpoints_pickup_label',
                'label' => Mage::helper('storepickup')->getDataConfig('bonus_label'),
                'value' => number_format($bonusPickupPoints),
                'strong'        => true,
                'is_formated'   => true,
            )), 'subtotal');
        }
        
        if ($order->getRewardpointsSpent()) {
            $totalsBlock->addTotal(new Varien_Object(array(
                'code'  => 'rewardpoints_spent_label',
                'label' => $this->__('Spent Points'),
                'value' => $order->getRewardpointsSpent(),
                'strong'        => true,
                'is_formated'   => true,
            )), 'subtotal');
        }
    }
}
