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
 * 
 * @category    Magestore
 * @package     Magestore_RewardPoints
 * @author      Magestore Developer
 */
class Magestore_TruWallet_Block_Adminhtml_Totals_Order_Point extends Mage_Adminhtml_Block_Sales_Order_Totals_Item
{
    /**
     * add points value into order total
     *     
     */
    public function initTotals()
    {
        $totalsBlock = $this->getParentBlock();
        $order = $totalsBlock->getOrder();

        $refundEarnedTruWallet = Mage::getResourceModel('truwallet/transaction_collection')
            ->addFieldToFilter('action_type', Magestore_TruWallet_Model_Type::TYPE_TRANSACTION_REFUND_ORDER)
            ->addFieldToFilter('changed_credit', array('gt' => 0))
            ->addFieldToFilter('order_id', $order->getId())
            ->getFieldTotal()
            ;

        $display_balance = Mage::helper('core')->currency($refundEarnedTruWallet, true, false);

        if ($refundEarnedTruWallet > 0) {
            $totalsBlock->addTotal(new Varien_Object(array(
                'code'  => 'truWallet_refund_earned',
                'label' => $this->__('Refund TruWallet Funds'),
                'value' => $display_balance,
                'is_formated'   => true,
                'area'  => 'footer',
            )));
        }
    }
}
