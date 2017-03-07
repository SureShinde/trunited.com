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
 * @package     Magestore_Storecredit
 * @module      Storecredit
 * @author      Magestore Developer
 *
 * @copyright   Copyright (c) 2016 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 *
 */

/**
 * TruWallet Block
 * 
 * @category    Magestore
 * @package     Magestore_TruWallet
 * @author      Magestore Developer
 */
class Magestore_TruWallet_Block_Order_Creditmemo_Totals extends Mage_Core_Block_Template
{

    public function initTotals()
    {
        $totalsBlock = $this->getParentBlock();
        $creditmemo = $totalsBlock->getCreditmemo();

        if ($creditmemo->getTruwalletDiscount() > 0.0001) {
            $totalsBlock->addTotal(new Varien_Object(array(
                'code' => $this->getCode(),
                'label' => Mage::helper('truwallet')->__('truWallet Balance'),
                'value' => -$creditmemo->getTruwalletDiscount(),
                'base_value' => -$creditmemo->getBaseTruwalletDiscount(),
                )), 'subtotal');
        }
    }

}
