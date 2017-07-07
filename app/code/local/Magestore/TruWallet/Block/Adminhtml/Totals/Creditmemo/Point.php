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
class Magestore_TruWallet_Block_Adminhtml_Totals_Creditmemo_Point
    extends Mage_Adminhtml_Block_Sales_Order_Totals_Item
{
    /**
     * add points value into creditmemo total
     *     
     */
    public function initTotals()
    {
        $totalsBlock = $this->getParentBlock();
        $creditmemo = $totalsBlock->getCreditmemo();

        if ($creditmemo->getTruwalletEarn() > 0) {
            $totalsBlock->addTotal(new Varien_Object(array(
                'code'  => 'truwallet_earn_label',
                'label' => $this->__('Refund truWallet Balances'),
                'value' => $creditmemo->getTruwalletEarn(),
                'is_formated'   => true,
            )), 'subtotal');
        }
    }
}
