<?php

class Magestore_Nationpassport_Block_Balance extends Mage_Core_Block_Template
{
	public function _prepareLayout(){
		return parent::_prepareLayout();
	}

    public function getTruWalletCredit()
    {
        return Mage::helper('truwallet/account')->getTruWalletCredit();
    }

    public function getTruGiftCardCredit()
    {
        return Mage::helper('trugiftcard/account')->getTruGiftCardCredit();
    }

    public function getBalance()
    {
        $account = Mage::getSingleton('affiliateplus/session')->getAccount();
        if($account != null && $account->getId())
            return Mage::helper('core')->currency($account->getBalance(), true, false);
        else
            return Mage::helper('core')->currency(0, true, false);
    }
}
