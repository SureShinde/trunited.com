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
 * TruGiftCard Block
 *
 * @category    Magestore
 * @package     Magestore_TruGiftCard
 * @author      Magestore Developer
 */
class Magestore_TruGiftCard_Block_Order_Totals extends Mage_Core_Block_Template
{

    public function initTotals()
    {
        $order = $this->getParentBlock()->getOrder();
        if ($order->getTrugiftcardDiscount() > 0) {
            $this->getParentBlock()->addTotal(new Varien_Object(array(
                'code' => $this->getCode().rand(1,100),
                'value' => -$order->getTrugiftcardDiscount(),
                'base_value' => -$order->getBaseTrugiftcardDiscount(),
                'label' => Mage::helper('trugiftcard')->getSpendConfig('discount_label'),
            )), 'subtotal');
        }
    }

}
