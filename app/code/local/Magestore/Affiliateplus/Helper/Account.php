<?php

class Magestore_Affiliateplus_Helper_Account extends Mage_Core_Helper_Abstract {

    public function customerNotLogin() {
        return !$this->customerLoggedIn();
    }

    public function customerLoggedIn() {
        return Mage::getSingleton('customer/session')->isLoggedIn();
    }

    public function accountNotRegistered() {
        return !$this->isRegistered();
    }

    public function isRegistered() {
        return Mage::getSingleton('affiliateplus/session')->isRegistered();
    }

    public function accountNotLogin() {
        return !$this->isLoggedIn();
    }

    public function isLoggedIn() {
        return Mage::getSingleton('affiliateplus/session')->isLoggedIn();
    }

    /**
     * get Affiliate Session
     *
     * @return Magestore_Affiliateplus_Model_Session
     */
    public function getSession() {
        return Mage::getSingleton('affiliateplus/session');
    }

    /**
     * get Affiliate Account
     *
     * @return Magestore_Affiliateplus_Model_Account
     */
    public function getAccount() {
        return $this->getSession()->getAccount();
    }

    public function getNavigationLabel() {
        return $this->__('My truDashboard');
    }

    public function getAccountBalanceFormated() {
        $scope = Mage::getStoreConfig('affiliateplus/account/balance');
        if($scope == 'website')
            return Mage::helper('core')->currency($this->getAccount()->getWebsiteBalance());
        else
            return Mage::helper('core')->currency($this->getAccount()->getBalance());
        //$baseCurrency = Mage::app()->getStore()->getBaseCurrency();
        //return $baseCurrency->format($this->getAccount()->getBalance());
    }

    public function getBalanceLabel() {
        return $this->__('Balance: %s', $this->getAccountBalanceFormated());
    }

    public function isEnoughBalance() {
        return ($this->getAccount()->getBalance() >= Mage::helper('affiliateplus/config')->getPaymentConfig('payment_release'));
    }

    public function disableStoreCredit() {
        if ($this->accountNotLogin()) {
            return true;
        }
        if (Mage::helper('affiliateplus/config')->getPaymentConfig('store_credit')) {
            return false;
        }
        return true;
    }

    public function disableWithdrawal() {
        if ($this->accountNotLogin()) {
            return true;
        }
        if (Mage::helper('affiliateplus/config')->getPaymentConfig('withdrawals')) {
            return false;
        }
        return true;
    }

    public function hideWithdrawalMenu() {
        return $this->disableStoreCredit() && $this->disableWithdrawal();
    }

    public function getWithdrawalLabel() {
        if ($this->disableWithdrawal()) {
            return 'Store Credits';
        }
        return 'Withdrawals';
    }

    
    
    //hainh 22-07-2014
    public function createAffiliateAccount($address, $paypalEmail, $customer, $notification, $referringWebsite, $successMessage, $referredBy=null, $coreSession=null, $keyShop) {

        $account = Mage::getModel('affiliateplus/account')
                ->setData('customer_id', $customer->getId())
                //->setData('address_id',$address->getId())
                ->setData('name', $customer->getName())
                ->setData('email', $customer->getEmail())
                ->setData('paypal_email', $paypalEmail)
                ->setData('created_time', now())
                ->setData('balance', 0)
                ->setData('total_commission_received', 0)
                ->setData('total_paid', 0)
                ->setData('total_clicks', 0)
                ->setData('unique_clicks', 0)
                ->setData('status', 1)
                ->setData('status_default', 1)
                ->setData('approved_default', 1)
                ->setData('notification', $notification)
                /*
                  hainh update for adding referring website
                  22-04-2014
                 */
                ->setData('referring_website', $referringWebsite)
                /*
                  Adam update for adding referring by to database
                  11-09-2014
                 */
                ->setData('referred_by', $referredBy)
                /*
                  Adam update for adding key shop to database
                  27/08/2016
                 */
                ->setData('key_shop', $keyShop)
        ;
        $successMessage = Mage::helper('affiliateplus/config')->getSharingConfig('notification_after_signing_up');
        $coreSession = $coreSession ? $coreSession : Mage::getSingleton('core/session');
        if (Mage::helper('affiliateplus/config')->getSharingConfig('need_approved')) {
            $account->setData('status', 2);
            $account->setData('approved', 2);
            $coreSession->setData('has_been_signup', true);
            $successMessage .= ' ' . $this->__('Thank you for signing up for our Affiliate program. Your registration will be reviewed and we\'ll inform you as soon as possible.');
        }
        if ($address)
            $account->setData('address_id', $address->getId());
        $account->setData('identify_code', $account->generateIdentifyCode());
        $account->setStoreId(Mage::app()->getStore()->getId())->save();
        $account->updateUrlKey();

        //send email
        $account->sendMailToNewAccount($account->getIdentifyCode());  //@variable identify_code
        $account->sendNewAccountEmailToAdmin();
        return $successMessage;
    }
    //end editing
    /*add by blanka*/
    /**
     * get store ids by website id
     * @param type $websiteId
     * @return store ids
     */
    public function getStoreIdsByWebsite($websiteId){
        if(is_null($websiteId))
            $websiteId = Mage::app()->getWebsite()->getId();
        $stores = Mage::getModel('core/store')->getCollection()
            ->addFieldToFilter('website_id', $websiteId)
            ->addFieldToFilter('is_active', 1)
            ->getAllIds();
        return $stores;
    }
    /*end*/

    /**
     *	Changed by Adam (19/08/2015): show list lifetimecustomer in frontend
     */
    public function notEnableLifetime() {
        if(Mage::helper('affiliateplus/config')->getCommissionConfig('life_time_sales') && $this->isLoggedIn())
            return false;
        return true;
    }

    //customize by vietbq
    public function getReferEmailUrl(){

        $account = Mage::getSingleton('affiliateplus/session')->getAccount();

        $requestPath = $this->_getConfigHelper()->getReferConfig('url_prefix') . trim($account->getEmail());
        $store = Mage::app()->getStore();

        $idPath = 'affiliateplus/' . $store->getId() . '/' . $account->getId(). '/fixemail' ;

        /* Magic fix url include '@','©','®','À'... */
        $requestPath = Mage::helper('catalog/product_url')->format($requestPath);
        $requestPath = str_replace(" ", "", $requestPath);
        /* END */
        $existedRewirte = Mage::getResourceModel('core/url_rewrite_collection')
            ->addFieldToFilter('store_id', $store->getId())
            ->addFieldToFilter('request_path', $requestPath)
            ->addFieldToFilter('id_path', $idPath)
            ->getFirstItem();
        $urlQuick = $this->getPrefixUrl();
        if ($existedRewirte->getId()) {
            return $urlQuick. $existedRewirte->getRequestPath();
        }
        $targetPath = $this->_getDefaultPath($store);
        if (strpos($targetPath, '?') === false)
            $targetPath .= '/?acc=';
        else
            $targetPath .= '&acc=';
        $targetPath .= $account->getIdentifyCode();
        $rewrite = Mage::getModel('core/url_rewrite')->load($idPath, 'id_path');
        $rewrite->addData(array(
            'store_id' => $store->getId(),
            'id_path' => $idPath,
            'request_path' => $requestPath,
            'target_path' => $targetPath,
            'is_system' => 0,
        ));
        try {
            $rewrite->save();
            return $urlQuick. $requestPath;
//            $session->addSuccess($this->__('Your custom url has been saved successfully!'));
        } catch (Exception $e) {
//            $session->addError($e->getMessage());
//            $session->setAffilateCustomUrl($data['personal_url']);
        }
    }

    protected function _getDefaultPath($store = null) {
        $defaultPath = Mage::getStoreConfig('web/default/front', $store);
        $p = explode('/', $defaultPath);
        switch (count($p)) {
            case 1: $p[] = 'index';
            case 2: $p[] = 'index';
        }
        return implode('/', $p);
    }
    protected function _getConfigHelper() {
        return Mage::helper('affiliateplus/config');
    }
    public function getPrefixUrl() {
        $prefixurl = str_replace(" ", "", Mage::getBaseUrl());
        $requestPath = Mage::helper('catalog/product_url')->format($prefixurl);
        $response = str_replace(" ", "", $requestPath);
        return $response;
    }

    public function getAffiliateInfoFromCookie(){
        $cookie = Mage::getSingleton('core/cookie');
        $map_index = $cookie->get('affiliateplus_map_index');
        if($map_index) {
//            for ($i = $map_index; $i > 0; $i--) {
//                $accountCode = $cookie->get("affiliateplus_account_code_$i");
//            }
            $accountCode = $cookie->get("affiliateplus_account_code_$map_index");

            if (Mage::getStoreConfig('affiliateplus/general/url_param_value') == 2) {
                $account = Mage::getModel('affiliateplus/account')->loadByIdentifyCode($accountCode);
            } else {
                $account = Mage::getModel('affiliateplus/account')->getCollection()->addFieldToFilter('identify_code', $accountCode)->getFirstItem();
            }
            return $account;
        }
        return false;
    }

    //get affiliate information neu khong lay duoc tu cookie de save theo tier
    public function getAffiliateInfoToRefer($accountId){
        $info = array();
        $account = Mage::getModel('affiliateplus/account')->getCollection()->addFieldToFilter('account_id', $accountId)->getFirstItem();
        $accountCode = $account->getIdentifyCode();
        if($account && $account->getStatus() == 1) {
            $info[$accountCode] = array(
                'index' => 1,
                'code' => $accountCode,
                'account' => $account,
            );
        }
        $infoObj = new Varien_Object(array(
            'info' => $info,
        ));
        return $infoObj->getInfo();
    }
    //get affiliate information neu khong lay duoc tu cookie de save theo tier
    //customize by vietbq
}
