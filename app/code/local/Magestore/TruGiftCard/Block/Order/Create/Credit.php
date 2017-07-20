<?php

class Magestore_TruGiftCard_Block_Order_Create_Credit extends Mage_Core_Block_Template
{
    /**
     * @return mixed
     */
    public function getCustomerCredit()
    {
        $store = $this->getStore();
        $customer_id = Mage::getSingleton('adminhtml/session_quote')->getCustomerId();

        $account = Mage::helper('trugiftcard/account')->loadByCustomerId($customer_id);

        $credit = $store->convertPrice($account->getTrugiftcardCredit());
        $session = Mage::getSingleton('checkout/session');
        if ($session->getBaseTrugiftcardCreditAmount())
            $credit -= $session->getBaseTrugiftcardCreditAmount();
        return $credit;
    }

    /**
     * @return mixed
     */
    public function getStore()
    {
        $quote = Mage::getSingleton('adminhtml/session_quote')->getQuote();
        $store = Mage::app()->getStore($quote->getData('store_id'));
        return $store;
    }

    /**
     * @return mixed
     */
    public function getTrugiftcardBalanceLabel()
    {
        $store = $this->getStore();
        $credit = $this->getCustomerCredit();
        return $store->getCurrentCurrency()->format($credit);
    }
}
