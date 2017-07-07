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
class Magestore_TruWallet_Block_Order_Totals extends Mage_Core_Block_Template
{

    public function initTotals()
    {
        $order = $this->getParentBlock()->getOrder();
        if ($order->getTruwalletDiscount() > 0) {
            $this->getParentBlock()->addTotal(new Varien_Object(array(
                'code' => $this->getCode(),
                'value' => -$order->getTruwalletDiscount(),
                'base_value' => -$order->getBaseTruwalletDiscount(),
                'label' => Mage::helper('truwallet')->__('truWallet Balance'),
            )), 'subtotal');
        }
    }

}
