<?php

class Magestore_Nationpassport_Block_Share extends Mage_Core_Block_Template
{
	public function _prepareLayout(){
		return parent::_prepareLayout();
	}

    public function getBackUrl()
    {
        return $this->getUrl('*/*/');
    }

    public function getMaxAmount() {
        return Mage::helper('trugiftcard/account')->getTruGiftCardCredit(false);
    }

    public function getActionForm()
    {
        return Mage::getUrl('*/*/sharePost');
    }

    public function getTruGiftCardCredit()
    {
        return Mage::helper('trugiftcard/account')->getTruGiftCardCredit();
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

    public function getLoadMemberHtml()
    {
        return $this->getUrl('*/*/loadMember');
    }

    public function getAffiliatesFromCustomer()
    {
        return Mage::helper('nationpassport')->getAffiliatesFromCustomer(
            $this->getCurrentCustomerId()
        );
    }

    public function getCurrentCustomerId()
    {
        return Mage::getSingleton('customer/session')->getCustomer()->getId();
    }

    public function getSearchMemberUrl()
    {
        return $this->getUrl('*/*/searchMember');
    }

}
