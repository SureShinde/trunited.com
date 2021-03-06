<?php

class Magestore_Custompromotions_Helper_Configuration extends Mage_Core_Helper_Abstract
{
	public function getDataConfig($group, $field)
    {
        return Mage::getStoreConfig('custompromotions/'.$group.'/'.$field, Mage::app()->getStore()->getId());
    }

    public function isEnable()
    {
        return $this->getDataConfig('general','enable');
    }

    public function getStartPromotion()
    {
        return $this->getDataConfig('general','start_date');
    }

    public function getEndPromotion()
    {
        return $this->getDataConfig('general','end_date');
    }

    public function getRewardsAmount()
    {
        return $this->getDataConfig('general','rewards');
    }

    public function getMaxCustomers()
    {
        return $this->getDataConfig('general','max_customers');
    }

    public function isEnableTruWalletProduct()
    {
        return Mage::helper('truwallet')->isEnableTruWalletProduct();
    }

    public function getTruWalletSku()
    {
        return Mage::helper('truwallet')->getTruWalletSku();
    }

    public function getTruWalletValue()
    {
        return Mage::helper('truwallet')->getTruWalletValue();
    }

    public function getTruWalletOrderStatus()
    {
        return Mage::helper('truwallet')->getTruWalletOrderStatus();
    }

    public function getNotifyMessage()
    {
        return Mage::getStoreConfig('truwallet/product/notify_message', Mage::app()->getStore()->getId());
    }

    public function getNotifyMessageGiftCard()
    {
        return Mage::getStoreConfig('trugiftcard/product/notify_message', Mage::app()->getStore()->getId());
    }

    public function getHtmlNotifyMessage()
    {
        return '<ul class="messages"><li class="notice-msg"><ul><li><span>'.$this->getNotifyMessage().'</span></li></ul></li></ul>';
    }

    public function getShipStationEnable()
    {
        return $this->getDataConfig('shipment','enable');
    }
    
    public function getShipStationOrderStatus()
    {
        return $this->getDataConfig('shipment','order_status');
    }

    public function getCmsMessageLogin()
    {
        return $this->getDataConfig('cms_page','message_login');
    }

    public function isEnableTruGiftCardProduct()
    {
        return Mage::helper('trugiftcard')->isEnableTruGiftCardProduct();
    }

    public function getTruGiftCardSku()
    {
        return Mage::helper('trugiftcard')->getTruGiftCardSku();
    }

    public function getTruGiftCardValue()
    {
        return Mage::helper('trugiftcard')->getTruGiftCardValue();
    }

    public function getTruGiftCardOrderStatus()
    {
        return Mage::helper('trugiftcard')->getTruGiftCardOrderStatus();
    }

    public function getApplyToProductTypes()
    {
        $types = $this->getDataConfig('custom_product','product_type');
        if($types != null)
            return explode(',', $types);
        else
            return null;
    }

    public function getFilterAZCategories()
    {
        $types = $this->getDataConfig('category','categories_filtering');
        if($types != null)
            return explode(',', $types);
        else
            return null;
    }

    public function getSearchCategories()
    {
        $types = $this->getDataConfig('category','categories_searching');
        if($types != null)
            return explode(',', $types);
        else
            return null;
    }

    public function enableYTDMTD()
    {
        return $this->getDataConfig('ytd_mtd','enable');
    }

    public function getLabelForProfit()
    {
        return str_replace('{{current_year}}',date('Y', time()), $this->getDataConfig('ytd_mtd','profit'));
    }

    public function getLabelForSharing()
    {
        return $this->getDataConfig('ytd_mtd','shared');
    }
}