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
        return $this->getDataConfig('product','enable');
    }

    public function getTruWalletSku()
    {
        return $this->getDataConfig('product','truwallet_sku');
    }

    public function getTruWalletValue()
    {
        return $this->getDataConfig('product','truwallet_value');
    }

    public function getTruWalletOrderStatus()
    {
        return $this->getDataConfig('product','order_status');
    }

    public function getNotifyMessage()
    {
        return $this->getDataConfig('product','notify_message');
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
}