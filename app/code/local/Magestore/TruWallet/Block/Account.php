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
 * @package     Magestore_TruWallet
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * TruWallet show on customer account dashboard Block
 * 
 * @category    Magestore
 * @package     Magestore_TruWallet
 * @author      Magestore Developer
 */
class Magestore_TruWallet_Block_Account extends Mage_Core_Block_Template
{
    public function _prepareLayout(){
        return parent::_prepareLayout();
    }

    public function getTruWalletCredit()
    {
        return Mage::helper('truwallet/account')->getTruWalletCredit();
    }
}
