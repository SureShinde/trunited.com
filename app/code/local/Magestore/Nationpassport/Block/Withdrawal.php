<?php

class Magestore_Nationpassport_Block_Withdrawal extends Mage_Core_Block_Template
{
    /**
     * Get Affiliate Payment Helper
     *
     * @return Magestore_Affiliateplus_Helper_Payment
     */
    protected function _getPaymentHelper(){
        return Mage::helper('affiliateplus/payment');
    }

	public function _prepareLayout(){
		return parent::_prepareLayout();

        $layout = $this->getLayout();
        $paymentMethods = $this->getAllPaymentMethod();
        foreach ($paymentMethods as $code => $method){
            $paymentMethodFormBlock = $layout->createBlock($method->getFormBlockType(),"payment_method_form_$code")->setPaymentMethod($method);
            $this->setChild("payment_method_form_$code",$paymentMethodFormBlock);
        }

        return $this;

	}

    public function getAllPaymentMethod(){
        if (!$this->hasData('all_payment_method')){
            $this->setData('all_payment_method',$this->_getPaymentHelper()->getAvailablePayment());
        }
        return $this->getData('all_payment_method');
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

    public function getFormActionUrl()
    {
        return $this->getUrl('*/*/withdrawalPost');
    }

    public function canRequest() {
        return !Mage::helper('affiliateplus/account')->disableWithdrawal();
    }

    public function isPaidAffiliateFee()
    {
        return Mage::helper('affiliateplus/account')->isPaidAffiliateFee();
    }

    public function getErrorMessageFee()
    {
        $affiliateFeeProduct = Mage::getModel('catalog/product')->load(177);
        if ($affiliateFeeProduct)
            $url = $affiliateFeeProduct->getProductUrl();
        else
            $url = Mage::helper('core/url')->getCurrentUrl();

        return $this->__('A signed affiliate agreement is required to withdraw affiliate funds. Please <a href="%s">click here</a>.', $url);

    }

    /**
     * get Tax rate when withdrawal
     *
     * @return float
     */
    public function getTaxRate() {
        if (!$this->hasData('tax_rate')) {
            $this->setData('tax_rate', Mage::helper('affiliateplus/payment_tax')->getTaxRate());
        }
        return $this->getData('tax_rate');
    }

    public function includingFee() {
        return (Mage::getStoreConfig('affiliateplus/payment/who_pay_fees') != 'payer');
    }

    public function getPriceFormatJs() {
        $priceFormat = Mage::app()->getLocale()->getJsPriceFormat();
        return Mage::helper('core')->jsonEncode($priceFormat);
    }

    /*add by blanka*/
    /**
     * get default payment method
     * @return type
     */
    protected function _getDefaultPaymentMethod(){
        return Mage::getStoreConfig('affiliateplus/payment/default_method');
    }

    /**
     * check a method is default or not
     * @param type $code
     * @return boolean
     */
    public function methodSelected($code){
        if($code == $this->_getDefaultPaymentMethod()){
            return true;
        }
        return false;
    }

    public function getBankAccounts(){
        if (!$this->hasData('bank_accounts')){
            $bankAccounts = Mage::getModel('affiliatepluspayment/bankaccount')
                ->getBankAccounts($this->getAccount());
            $this->setData('bank_accounts',$bankAccounts);
        }
        return $this->getData('bank_accounts');
    }

    public function hasBankAccount(){
        return $this->getBankAccounts()->getSize();
    }

    public function getBankAccountHtmlSelect($type){
        $data = $this->getPostData();

        if ($this->hasBankAccount()){
            $options = array();
            foreach ($this->getBankAccounts() as $bankAccount) {
                $options[] = array(
                    'value' => $bankAccount->getId(),
                    'label'	=> $bankAccount->format(false)
                );
                $bankAccountId = $bankAccount->getId();
            }

            if(isset($data['payment_bankaccount_id']))
                $bankAccountId = $data['payment_bankaccount_id'];
            //Zend_Debug::dump($bankAccountId);
            if($bankAccountId)
                $this->setBankAccountId($bankAccountId);
            $select = $this->getLayout()->createBlock('core/html_select')
                ->setName($type.'_bankaccount_id')
                ->setId($type.'-bank-select')
                ->setClass('bank-select')
                ->setExtraParams('onchange=lsRequestTrialNewAccount(this.value);')
                ->setValue($bankAccountId)
                ->setOptions($options);

            $select->addOption('', Mage::helper('checkout')->__('New Bank Account'));

            return $select->getHtml();
        }
        return '';
    }

    public function getBank(){
        $bank = Mage::getModel('affiliatepluspayment/bankaccount');
        return $bank;
    }



}
