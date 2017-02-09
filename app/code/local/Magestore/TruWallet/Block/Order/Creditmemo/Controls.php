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

/**
 * Refund to customer balance functionality block
 *
 */
class Magestore_TruWallet_Block_Order_Creditmemo_Controls extends Mage_Core_Block_Template
{

    /**
     * @return mixed
     */
    public function getGrandTotal()
    {
        $totalsBlock = Mage::getBlockSingleton('sales/order_creditmemo_totals');
        $creditmemo = $totalsBlock->getCreditmemo();
        return $creditmemo->getGrandTotal();
    }
}
