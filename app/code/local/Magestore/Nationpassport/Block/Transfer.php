<?php

class Magestore_Nationpassport_Block_Transfer extends Mage_Core_Block_Template
{
	public function _prepareLayout(){
		return parent::_prepareLayout();
	}

    public function getTruGiftCardCredit()
    {
        return Mage::helper('trugiftcard/account')->getTruGiftCardCredit();
    }

    public function getBalanceOrigin()
    {
        $account = Mage::getSingleton('affiliateplus/session')->getAccount();
        if($account != null && $account->getId())
            return Mage::helper('core')->currency($account->getBalance(), true, false);
        else
            return Mage::helper('core')->currency(0, true, false);
    }

    public function getBackUrl()
    {
        return $this->getUrl('*/*/');
    }

    public function getAccount(){
        return Mage::getSingleton('affiliateplus/session')->getAccount();
    }

    public function getBalance(){
        /*Changed By Adam 15/09/2014: to fix the issue of request withdrawal when scope is website*/
        $balance = 0;
        if(Mage::getStoreConfig('affiliateplus/account/balance') == 'website') {
            $website = Mage::app()->getStore()->getWebsite();

            $stores = $website->getStores();

            foreach($stores as $store) {
                $account = Mage::getModel('affiliateplus/account')->setStoreId($store->getId())->load($this->getAccount()->getId());
                $balance += $account->getBalance();
            }
        } else {
            $balance = $this->getAccount()->getBalance();
        }
        $balance = Mage::app()->getStore()->convertPrice($balance);
        return $balance;
    }

    public function getFormatedBalance(){
        $balance = 0;
        if(Mage::getStoreConfig('affiliateplus/account/balance') == 'website') {
            $website = Mage::app()->getStore()->getWebsite();

            $stores = $website->getStores();

            foreach($stores as $store) {
                $account = Mage::getModel('affiliateplus/account')->setStoreId($store->getId())->load($this->getAccount()->getId());
                $balance += $account->getBalance();
            }
            return Mage::helper('core')->currency($balance, true, false);
        } else {
            return Mage::helper('core')->currency($this->getAccount()->getBalance(), true, false);
        }
    }

    public function getTransferFormActionUrl(){
        $url = $this->getUrl('nationpassport/balance/transferPost');
        return $url;
    }

    public function getMaxAmount() {
        $taxRate = Mage::helper('affiliateplus/payment_tax')->getTaxRate();
        if (!$taxRate) {
            return $this->getBalance();
        }
        $balance = $this->getBalance();
        $maxAmount = $balance * 100 / (100 + $taxRate);
        return round($maxAmount, 2);
    }

    public function isEnableTerm()
    {
        return Mage::getStoreConfig('trugiftcard/transfer/enable_term');
    }

    public function getContentTerm()
    {
        return Mage::getStoreConfig('trugiftcard/transfer/content_term');
    }
}
