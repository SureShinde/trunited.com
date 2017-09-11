<?php

class Magestore_Nationpassport_Block_Nationpassport extends Mage_Core_Block_Template
{
	public function _prepareLayout(){
		return parent::_prepareLayout();
	}

	public function getAccountDashboardUrl()
    {
        return $this->getUrl('customer/account');
    }

    public function getCurrentCustomer()
    {
        return  Mage::getModel('customer/customer')->load(
            Mage::getSingleton('customer/session')->getCustomer()->getId()
        );
    }

    public function getAffiliateRefer()
    {
        return Mage::helper('nationpassport')->getAffiliateRefer(
            $this->getCurrentCustomer()->getId()
        );
    }

    public function getTruGiftCardProduct()
    {
        return Mage::helper('nationpassport')->getTruGiftCardProduct();
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

    public function getShareTruGiftCardUrl()
    {
        return $this->getUrl('*/balance/share');
    }

    public function getBalanceDashboardUrl()
    {
        return $this->getUrl('*/balance/');
    }

    public function getTransferUrl()
    {
        return $this->getUrl('*/balance/transfer');
    }

    public function getWithdrawalUrl()
    {
        return $this->getUrl('*/balance/withdrawal');
    }
}
