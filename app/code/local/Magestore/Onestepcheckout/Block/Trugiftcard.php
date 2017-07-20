<?php
/**
 * Created by PhpStorm.
 * User: anthony
 * Date: 3/17/17
 * Time: 11:27 AM
 */

class Magestore_Onestepcheckout_Block_Trugiftcard extends Mage_Core_Block_Template
{
    public function _prepareLayout(){
        return parent::_prepareLayout();
    }

    public function getGiftWrapUrl()
    {
        return Mage::getUrl('onestepcheckout/ajax/add_giftwrap');
    }

    public function getGiftWrapAmount()
    {
        return Mage::helper('onestepcheckout')->getGiftwrapAmount();
    }

    public function isPlastic()
    {
        return Mage::helper('truwallet/giftcard')->plasticInCart();
    }
}