<?php

class Magestore_Nationpassport_BalanceController extends Mage_Core_Controller_Front_Action
{
    protected function _getAccountHelper()
    {
        return Mage::helper('affiliateplus/account');
    }

    /**
     * get Core Session
     *
     * @return Mage_Core_Model_Session
     */
    protected function _getCoreSession()
    {
        return Mage::getSingleton('core/session');
    }

    /**
     * get Affiliateplus helper
     *
     * @return Magestore_Affiliateplus_Helper_Data
     */
    protected function _getHelper()
    {
        return Mage::helper('affiliateplus');
    }

    /**
     * getConfigHelper
     *
     * @return Magestore_Affiliateplus_Helper_Config
     */
    protected function _getConfigHelper()
    {
        return Mage::helper('affiliateplus/config');
    }

    /**
     * get Affiliate Payment Helper
     *
     * @return Magestore_Affiliateplus_Helper_Payment
     */
    protected function _getPaymentHelper()
    {
        return Mage::helper('affiliateplus/payment');
    }


    /**
     * check customer is logged in
     */
    public function preDispatch()
    {
        parent::preDispatch();
        if (!$this->getRequest()->isDispatched()) {
            return;
        }
        $action = $this->getRequest()->getActionName();
        if ($action != 'policy' && $action != 'redirectLogin') {
            // Check customer authentication
            if (!Mage::getSingleton('customer/session')->isLoggedIn()) {
                Mage::getSingleton('customer/session')->setAfterAuthUrl(
                    Mage::getUrl($this->getFullActionName('/'))
                );
                $this->_redirect('customer/account/login');
                $this->setFlag('', Mage_Core_Controller_Varien_Action::FLAG_NO_DISPATCH, true);
            }
        }
    }

    public function indexAction()
    {
        $this->loadLayout();

        $this->_title(Mage::helper('nationpassport')->__('Money Movement Balance'));

        $breadcrumbs = $this->getLayout()->getBlock("breadcrumbs");
        $breadcrumbs->addCrumb("home", array(
            "label" => $this->__("Home"),
            "title" => $this->__("Home"),
            "link" => Mage::getBaseUrl()
        ));

        $breadcrumbs->addCrumb("my_account", array(
            "label" => $this->__("My Account"),
            "title" => $this->__("My Account"),
            "link" => Mage::getUrl('customer/account')
        ));

        $breadcrumbs->addCrumb("balance", array(
            "label" => $this->__("Balance"),
            "title" => $this->__("Balance"),
        ));

        $this->renderLayout();
    }

    public function transferAction()
    {
        $this->loadLayout();

        $this->_title(Mage::helper('nationpassport')->__('Money Movement Transfer'));

        $breadcrumbs = $this->getLayout()->getBlock("breadcrumbs");
        $breadcrumbs->addCrumb("home", array(
            "label" => $this->__("Home"),
            "title" => $this->__("Home"),
            "link" => Mage::getBaseUrl()
        ));

        $breadcrumbs->addCrumb("my_account", array(
            "label" => $this->__("My Account"),
            "title" => $this->__("My Account"),
            "link" => Mage::getUrl('customer/account')
        ));

        $breadcrumbs->addCrumb("transfer", array(
            "label" => $this->__("Transfer"),
            "title" => $this->__("Transfer"),
        ));

        $this->renderLayout();
    }

    public function transferPostAction()
    {
        if (!Mage::helper('affiliateplus')->isAffiliateModuleEnabled())
            return $this->_redirectUrl(Mage::getBaseUrl());

        if (!Mage::helper('magenotification')->checkLicenseKeyFrontController($this)) {
            return;
        }

        $baseCurrency = Mage::app()->getStore()->getBaseCurrency();
        if ($this->_getAccountHelper()->accountNotLogin())
            return $this->_redirect('affiliateplus/account/login');

        if (Mage::getStoreConfig('trugiftcard/transfer/enable_term')) {
            $agree = $this->getRequest()->getParam('transfer_term');
            if ($agree == null || $agree != 'on') {
                $this->_getCoreSession()->addNotice($this->__('You have to agree the terms and conditions.'));
                return $this->_redirect('*/*/transfer');
            }
        }

        $transfer_amount = $this->getRequest()->getParam('transfer_amount');
        if ($transfer_amount < 0 || !is_numeric($transfer_amount)) {
            $this->_getCoreSession()->addNotice($this->__('Please enter a number greater than 0 to transfer.'));
            return $this->_redirect('*/*/transfer');
        }

        if (!$this->_getAccountHelper()->isEnoughBalance()) {
            $this->_getCoreSession()->addNotice($this->__('The minimum balance required to transfer dollars is %s'
                , $baseCurrency->format($this->_getConfigHelper()->getPaymentConfig('payment_release'), array(), false)));
            return $this->_redirect('*/*/transfer');
        }

        $affiliate_account = $this->_getAccountHelper()->getAccount();
        if (!$affiliate_account->getId()) {
            $this->_getCoreSession()->addNotice($this->__('Please login to your account first.'));
            return $this->_redirect('affiliateplus/account/login');
        }
        $balance = $affiliate_account->getBalance();
        if ($transfer_amount > $balance) {
            $this->_getCoreSession()->addNotice($this->__('The maximum balance required to transfer dollars is %s'
                , $baseCurrency->format($balance, array(), false)));
            return $this->_redirect('*/*/transfer');
        }

        try {
            $customer = Mage::getModel('customer/customer')->load($affiliate_account->getCustomerId());

            $object_transfer = Mage::helper('affiliateplus/config')->getTransferConfig();
            $new_amount = null;
            if ($object_transfer == 1) {
                $enable_bonus = Mage::helper('truwallet')->getEnableTransferBonus();
                if ($enable_bonus) {
                    $percent = Mage::helper('truwallet')->getTransferBonus();
                    $new_amount = $transfer_amount + ($transfer_amount * $percent) / 100;
                } else {
                    $new_amount = $transfer_amount;
                }
                $truWalletAccount = Mage::helper('truwallet/account')->updateCredit(
                    $customer->getId(),
                    $new_amount
                );
                $params = array(
                    'credit' => $new_amount,
                    'title' => Mage::helper('truwallet')->__('Transfer dollars from balance to truWallet'),
                    'receiver_email' => '',
                    'receiver_customer_id' => '',
                );
                if ($truWalletAccount != null) {
                    Mage::helper('truwallet/transaction')->createTransaction(
                        $truWalletAccount,
                        $params,
                        Magestore_TruWallet_Model_Type::TYPE_TRANSACTION_TRANSFER,  // type
                        Magestore_TruWallet_Model_Status::STATUS_TRANSACTION_COMPLETED
                    );
                }
            } else if ($object_transfer == 2) {
                $enable_bonus = Mage::helper('trugiftcard')->getEnableTransferBonus();
                $new_amount = $transfer_amount;
                if ($enable_bonus) {
                    $percent = Mage::helper('trugiftcard')->getTransferBonus();
                    $new_amount += ($transfer_amount * $percent) / 100;

                }

                $truGiftCardAccount = Mage::helper('trugiftcard/account')->updateCredit(
                    $customer->getId(),
                    $new_amount
                );

                $params = array(
                    'credit' => $new_amount,
                    'title' => Mage::helper('trugiftcard')->__('Transfer dollars from Affiliate cash to Trunited Gift Card'),
                    'receiver_email' => '',
                    'receiver_customer_id' => '',
                );
                if ($truGiftCardAccount != null) {
                    Mage::helper('trugiftcard/transaction')->createTransaction(
                        $truGiftCardAccount,
                        $params,
                        Magestore_TruGiftCard_Model_Type::TYPE_TRANSACTION_TRANSFER,
                        Magestore_TruGiftCard_Model_Status::STATUS_TRANSACTION_COMPLETED
                    );
                }
            }


            $storeId = Mage::app()->getStore()->getStoreId();
            $scope = Mage::getStoreConfig('affiliateplus/account/balance', $storeId);
            if ($scope == 'store') {

            } else {
                $storeId = null;
            }

            $amount = -$transfer_amount;
            $balance = $affiliate_account->getBalance() + $amount;
            $affiliateAccount = Mage::getModel('affiliateplus/account')->setStoreId($storeId)->load($affiliate_account->getId());
            $affiliateAccount->setBalance($balance)->save();
            Mage::helper('affiliateplus')->addTransaction($affiliateAccount->getId(), $affiliateAccount->getName(), $affiliateAccount->getEmail(), -$transfer_amount, $storeId);

            if ($object_transfer == 1)
                $this->_getCoreSession()->addSuccess($this->__('Transfer %s dollars from Affiliate cash to truWallet successfully'
                    , $baseCurrency->format($new_amount, array(), false)));
            else if ($object_transfer == 2)
                $this->_getCoreSession()->addSuccess($this->__('Transfer %s dollars from Affiliate cash to Trunited Gift Card successfully'
                    , $baseCurrency->format($new_amount, array(), false)));
        } catch (Exception $ex) {
            $this->_getCoreSession()->addError($ex->getMessage());
        }
        return $this->_redirect('*/*/transfer');
    }

    public function withdrawalAction()
    {

        $this->loadLayout();

        $this->_title(Mage::helper('nationpassport')->__('Money Movement Withdrawal'));

        $breadcrumbs = $this->getLayout()->getBlock("breadcrumbs");
        $breadcrumbs->addCrumb("home", array(
            "label" => $this->__("Home"),
            "title" => $this->__("Home"),
            "link" => Mage::getBaseUrl()
        ));

        $breadcrumbs->addCrumb("my_account", array(
            "label" => $this->__("My Account"),
            "title" => $this->__("My Account"),
            "link" => Mage::getUrl('customer/account')
        ));

        $breadcrumbs->addCrumb("withdrawal", array(
            "label" => $this->__("Withdrawal"),
            "title" => $this->__("Withdrawal"),
        ));

        $this->renderLayout();
    }

    public function withdrawalPostAction()
    {
        if (!Mage::helper('affiliateplus')->isAffiliateModuleEnabled()) return $this->_redirectUrl(Mage::getBaseUrl());

        if (!Mage::helper('magenotification')->checkLicenseKeyFrontController($this)) {
            return;
        }

        $session = Mage::getSingleton('affiliateplus/session');
        $account = $session->getAccount();
        if (Mage::getModel('affiliateplus/payment')->setAccountId($account->getId())->hasWaitingPayment()) {
            $this->_getCoreSession()->addError($this->__('You are having a pending request!'));
            return $this->_redirect('*/*/withdrawal');
        }

        $amount = Mage::getSingleton('affiliateplus/session')->getPaymentAmount();
        $payment = Mage::getSingleton('affiliateplus/session')->getPayment();

        if ($amount)
            $payment->setAmount($amount);

        if ($this->_getAccountHelper()->accountNotLogin())
            return $this->_redirect('affiliateplus/account/login');

        if ($this->_getAccountHelper()->disableWithdrawal()) {
            $this->_getCoreSession()->addError($this->__('Request withdrawal not allowed at this time.'));
            return $this->_redirect('*/*/withdrawal');
        }

        if (!$this->_getAccountHelper()->isEnoughBalance()) {
            $baseCurrency = Mage::app()->getStore()->getBaseCurrency();
            $this->_getCoreSession()->addNotice($this->__('The minimum balance required to request withdrawal is %s'
                , $baseCurrency->format($this->_getConfigHelper()->getPaymentConfig('payment_release'), array(), false)));
            return $this->_redirect('affiliateplus/index/listTransaction');
        }
        //is purchased 99affiliate product?
        if (!$this->_getAccountHelper()->isPaidAffiliateFee()) {
            $affiliateFeeProduct = Mage::getModel('catalog/product')->load(177);
            if ($affiliateFeeProduct)
                $url = $affiliateFeeProduct->getProductUrl();
            else
                $url = Mage::helper('core/url')->getCurrentUrl();

            $this->_getCoreSession()->addError($this->__('A signed affiliate agreement is required to withdraw affiliate funds. Please <a href="%s">click here</a>.', $url));
            return $this->_redirect('*/*/withdrawal');
        }

        $params = $this->getRequest()->getPost();
        if (!count($params))
            $params = $this->getRequest()->getParams();

        if (!isset($params['payment_method'])) {
            $storeId = Mage::app()->getStore()->getId();
            $paymentMethods = Mage::helper('affiliateplus/payment')->getAvailablePayment($storeId);

            /* Changed By Adam 23/10/2013 - hainh update 24-04-2014 */
            if (count($paymentMethods) == 1) {
                foreach ($paymentMethods as $code => $value) {
                    $params['payment_method'] = $code;
                }
            } else {
                $params['payment_method'] = 'paypal';
            }

            if ($params['payment_method'] == 'paypal') {
                if (isset($params['paypal_email']) && $params['paypal_email'])
                    $params['email'] = $params['paypal_email'];
                else
                    $params['email'] = $account->getPaypalEmail();
            } else if ($params['payment_method'] == 'moneybooker') {

                if (isset($params['moneybooker_email']) && $params['moneybooker_email'])
                    $params['email'] = $params['moneybooker_email'];
                else
                    $params['email'] = $account->getMoneybookerEmail();
            }
        } else {
            $params['payment_method'] = $params['payment_method'];
            if ($params['payment_method'] == 'paypal') {
                if (isset($params['paypal_email']) && $params['paypal_email'])
                    $params['email'] = $params['paypal_email'];
                else
                    $params['email'] = $account->getPaypalEmail();
            } else if ($params['payment_method'] == 'moneybooker') {
                if (isset($params['moneybooker_email']) && $params['moneybooker_email'])
                    $params['email'] = $params['moneybooker_email'];
                else
                    $params['email'] = $account->getMoneybookerEmail();
            }
        }

        /* check email verify */

        if (isset($params['payment_method']) && $params['payment_method']) {
            $require = Mage::helper('affiliateplus/payment')->isRequireAuthentication($params['payment_method']);

            if ($require) {
                if (isset($params['email']) && $params['email']) {
                    $verify = Mage::getModel('affiliateplus/payment_verify')->loadExist($account->getId(), $params['email'], $params['payment_method']);
                    if (!$verify->isVerified()) {
                        $this->_getCoreSession()->addError('The email is not authenticated. Please verify authentication code.');
                        $url = Mage::getUrl('*/*/withdrawal');
                        return $this->_redirectUrl($url);
                    }
                }
            }
        }
        /* end */

        $paramObject = new Varien_Object(array('params' => $params));
        Mage::dispatchEvent('affiliateplus_payment_prepare_data', array(
            'payment_data' => $paramObject,
            'file' => $_FILES
        ));
        $params = $paramObject->getParams();
        $payment = Mage::getModel('affiliateplus/payment');
        $payment->setData($params);

        Mage::register('confirm_payment_data', $payment);
        $session->setPayment($payment);
        $session->setPaymentMethod($payment->getPaymentMethod());

        if ($payment->getAmount())
            $session->setPaymentAmount($payment->getAmount());


        $paymentCodes = $this->_getPaymentHelper()->getAvailablePaymentCode();
        if (!count($paymentCodes)) {
            $this->_getCoreSession()->addError(
                $this->__('There is no payment method on file for your account. Please update your details or contact us to solve the problem.')
            );
            return $this->_redirect('*/*/withdrawal');

        } elseif (count($paymentCodes) == 1) {
            $paymentCode = $this->getRequest()->getParam('payment_method');
            if (!$paymentCode)
                $paymentCode = current($paymentCodes);

        } else
            $paymentCode = $this->getRequest()->getParam('payment_method');

        if (!$paymentCode) {
            $this->_getCoreSession()->addNotice(
                $this->__('Please chose an available payment method!')
            );
            return $this->_redirect('*/*/withdrawal', $this->getRequest()->getPost());
        }

        if (!in_array($paymentCode, $paymentCodes)) {
            $this->_getCoreSession()->addError(
                $this->__('This payment method is not available, please choose an alternative payment method.')
            );
            return $this->_redirect('*/*/withdrawal', $this->getRequest()->getPost());
        }

        $account = $this->_getAccountHelper()->getAccount();
        $store = Mage::app()->getStore();

        $amount = $this->getRequest()->getParam('amount');
        $amount = $amount / $store->convertPrice(1);

        if ($amount < $this->_getConfigHelper()->getPaymentConfig('payment_release')) {
            $this->_getCoreSession()->addNotice(
                $this->__('The minimum balance required to request withdrawal is %s',
                    Mage::helper('core')->currency($this->_getConfigHelper()->getPaymentConfig('payment_release'), true, false))
            );
            return $this->_redirect('*/*/withdrawal');
        }

        if ($amountInclTax = $this->getRequest()->getParam('amount_incl_tax')) {
            if ($amountInclTax > $amount && $amountInclTax > $account->getBalance()) {
                $this->_getCoreSession()->addError(
                    $this->__('The withdrawal requested cannot exceed your current balance (%s).'
                        , Mage::helper('core')->currency($account->getBalance(), true, false))
                );
                return $this->_redirect('*/*/withdrawal');
            }
        }
        if ($amount > $account->getBalance()) {
            $this->_getCoreSession()->addError($this->__('The withdrawal requested cannot exceed your current balance (%s).'
                , Mage::helper('core')->currency($account->getBalance(), true, false)));

            return $this->_redirect('*/*/withdrawal');
        }

        $payment = Mage::getModel('affiliateplus/payment')
            ->setPaymentMethod($paymentCode)
            ->setAmount($amount)
            ->setAccountId($account->getId())
            ->setAccountName($account->getName())
            ->setAccountEmail($account->getEmail())
            ->setRequestTime(now())
            ->setStatus(1)
            ->setIsRequest(1)
            ->setIsPayerFee(0);

        if ($this->_getConfigHelper()->getPaymentConfig('who_pay_fees') == 'payer' && $paymentCode == 'paypal')
            $payment->setIsPayerFee(1);

        if ($payment->hasWaitingPayment()) {
            $this->_getCoreSession()->addError($this->__('You are having a pending request!'));
            return $this->_redirect('*/*/withdrawal');
        }

        if ($this->_getConfigHelper()->getSharingConfig('balance') == 'store')
            $payment->setStoreIds($store->getId());

        $paymentMethod = $payment->getPayment();

        $paymentObj = new Varien_Object(array(
            'payment_code' => $paymentCode,
            'required' => true,
        ));

        Mage::dispatchEvent("affiliateplus_request_payment_action_$paymentCode", array(
            'payment_obj' => $paymentObj,
            'payment' => $payment,
            'payment_method' => $paymentMethod,
            'request' => $this->getRequest(),
        ));
        $paymentCode = $paymentObj->getPaymentCode();

        if ($paymentCode == 'paypal') {
            $paypalEmail = $this->getRequest()->getParam('paypal_email');

            //Change paypal email for affiliate account
            if ($paypalEmail && $paypalEmail != $account->getPaypalEmail()) {
                $accountModel = Mage::getModel('affiliateplus/account')
                    ->setStoreId($store->getId())
                    ->load($account->getId());
                try {
                    $accountModel->setPaypalEmail($paypalEmail)
                        ->setId($account->getId())
                        ->save();
                } catch (Exception $e) {

                }
            }

            $paypalEmail = $paypalEmail ? $paypalEmail : $account->getPaypalEmail();
            if ($paypalEmail) {
                $paymentMethod->setEmail($paypalEmail);
                $paymentObj->setRequired(false);
            }
        }

        if ($paymentObj->getRequired()) {
            $this->_getCoreSession()->addNotice($this->__('Please fill out all required fields in the form below.'));
            return $this->_redirect('*/*/withdrawal', $this->getRequest()->getPost());
        }

        // Save request payment for affiliate account
        try {
            $payment->save();
            $paymentMethod->savePaymentMethodInfo();
            $payment->sendMailRequestPaymentToSales();
            $this->_getCoreSession()->addSuccess($this->__('Your request has been sent to admin for approval.'));
        } catch (Exception $e) {
            $this->_getCoreSession()->addError($e->getMessage());
        }

        return $this->_redirect('*/*/withdrawal');

    }

    public function shareAction()
    {

        $this->loadLayout();

        $this->_title(Mage::helper('nationpassport')->__('Money Movement Share'));

        $breadcrumbs = $this->getLayout()->getBlock("breadcrumbs");
        $breadcrumbs->addCrumb("home", array(
            "label" => $this->__("Home"),
            "title" => $this->__("Home"),
            "link" => Mage::getBaseUrl()
        ));

        $breadcrumbs->addCrumb("my_account", array(
            "label" => $this->__("My Account"),
            "title" => $this->__("My Account"),
            "link" => Mage::getUrl('customer/account')
        ));

        $breadcrumbs->addCrumb("share", array(
            "label" => $this->__("Share Trunited Gift Card"),
            "title" => $this->__("Share Trunited Gift Card"),
        ));

        $this->renderLayout();
    }

    public function loadMemberAction()
    {
        $this->loadLayout();

        $html = $this->getLayout()->createBlock('nationpassport/share_member')
            ->setTemplate('nationpassport/account/share/member.phtml')->toHtml();

        echo $html;
    }

    public function searchMemberAction()
    {
        $sort_by = $this->getRequest()->getParam('sort');
        $keyword = $this->getRequest()->getParam('keyword');

        $sort = 'asc';
        if (strcasecmp($sort_by, 'name_desc') == 0)
            $sort = 'desc';

        $members = Mage::getModel('affiliateplus/tracking')->getCollection()
            ->addFieldToFilter('customer_id', Mage::getSingleton('customer/session')->getCustomer()->getId())
            ->addFieldToSelect('account_id');

        $accounts = null;
        if (sizeof($members) > 0) {
            $accounts = Mage::getModel('affiliateplus/account')->getCollection()
                ->addFieldToFilter('name', array('like' => '%' . $keyword . '%'))
                ->addFieldToFilter('account_id', array('in' => $members->getColumnValues('account_id')))
                ->setOrder('name', $sort);
        }

        $html = $this->getLayout()->createBlock('nationpassport/search')
            ->setData("accounts", $accounts)
            ->setTemplate('nationpassport/search.phtml')
            ->toHtml();

        echo $html;
    }

    public function sharePostAction()
    {
        $amount = $this->getRequest()->getParam('share_amount');
        $day_of_expiration = $this->getRequest()->getParam('share_day_expiration');
        $message = $this->getRequest()->getParam('message');
        $account_selected = $this->getRequest()->getParam('account_selected');
        $email = filter_var($this->getRequest()->getParam('share_email'), FILTER_SANITIZE_EMAIL);

        if ($account_selected != null) {
            $account_affiliate = Mage::getModel('affiliateplus/account')->load($account_selected);
            if ($account_affiliate == null) {
                Mage::getSingleton('core/session')->addError(
                    Mage::helper('trugiftcard')->__($email . ' is not a valid email address')
                );
                $this->_redirectUrl(Mage::getUrl('*/*/share'));
                return;
            }

            $email = $account_affiliate->getEmail();
        }


        $customer = Mage::getModel('customer/customer')->load(Mage::getSingleton('customer/session')->getCustomerId());
        $transaction_helper = Mage::helper('trugiftcard/transaction');

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            Mage::getSingleton('core/session')->addError(
                Mage::helper('trugiftcard')->__($email . ' is not a valid email address')
            );
            $this->_redirectUrl(Mage::getUrl('*/*/share'));
            return;
        }

        if (strcasecmp($email, $customer->getEmail()) == 0) {
            Mage::getSingleton('core/session')->addError(
                Mage::helper('trugiftcard')->__('You cannot share truWallet balance to your email')
            );
            $this->_redirectUrl(Mage::getUrl('*/*/share'));
            return;
        }

        if (!filter_var($amount, FILTER_VALIDATE_FLOAT)) {
            Mage::getSingleton('core/session')->addError(
                Mage::helper('trugiftcard')->__($amount . ' is not a valid number')
            );
            $this->_redirectUrl(Mage::getUrl('*/*/share'));
            return;
        }

        if (!filter_var($day_of_expiration, FILTER_VALIDATE_INT) && $day_of_expiration != 0) {
            Mage::getSingleton('core/session')->addError(
                Mage::helper('trugiftcard')->__($day_of_expiration . ' is not a valid Integer number')
            );
            $this->_redirectUrl(Mage::getUrl('*/*/share'));
            return;
        } else if ($day_of_expiration < 0 || $day_of_expiration > 31) {
            Mage::getSingleton('core/session')->addError(
                Mage::helper('trugiftcard')->__('The # days of expiration is not smaller than 0 and greater than 31 ')
            );
            $this->_redirectUrl(Mage::getUrl('*/*/share'));
            return;
        }
        if ($day_of_expiration == 0)
            $expiration_date = null;
        else
            $expiration_date = Mage::helper('trugiftcard/transaction')->addDaysToDate(now(), $day_of_expiration);

        $account = Mage::getModel('trugiftcard/customer')->load($customer->getId(), 'customer_id');
        if ($account->getTrugiftcardCredit() < 0 || $amount > $account->getTrugiftcardCredit()) {
            Mage::getSingleton('core/session')->addError(
                Mage::helper('trugiftcard')->__('Sorry, your balance is less than what you want to share. Please enter a new amount.')
            );
            $this->_redirectUrl(Mage::getUrl('*/*/share'));
            return;
        }

        $customer_receiver = Mage::getModel("customer/customer");
        $customer_receiver->setWebsiteId(Mage::app()->getStore()->getWebsiteId());
        $customer_receiver->loadByEmail($email);
        $is_exist = false;
        if ($customer_receiver->getId())
            $is_exist = true;

        $status = Magestore_TruGiftCard_Model_Status::STATUS_TRANSACTION_PENDING;

        $transactionSave = Mage::getModel('core/resource_transaction');
        $connection = Mage::getSingleton('core/resource')->getConnection('core_write');
        try {
            $connection->beginTransaction();

            $truGiftCardAccount = Mage::helper('trugiftcard/account')->updateCredit($customer->getId(), -$amount);

            $recipient_transaction_id = null;
            $recipient_transaction = null;
            /* create transaction for recipient first */
            if ($is_exist) {
                $receiverAccount = Mage::helper('trugiftcard/account')->updateCredit($customer_receiver->getId(), $amount);
                $params = array(
                    'credit' => $amount,
                    'title' => '',
                    'receiver_email' => $customer->getEmail(),
                    'receiver_customer_id' => $customer->getId(),
                    'expiration_date' => $expiration_date,
                );
                if ($receiverAccount != null) {
                    $recipient_transaction = $transaction_helper->createTransaction(
                        $receiverAccount,
                        $params,
                        Magestore_TruGiftCard_Model_Type::TYPE_TRANSACTION_RECEIVE_FROM_SHARING,  // type
                        $status
                    );

                    if ($recipient_transaction != null && $recipient_transaction->getId())
                        $recipient_transaction_id = $recipient_transaction->getId();
                    else
                        throw new Exception(
                            Mage::helper('trugiftcard')->__('Cannot create a transaction for recipient.')
                        );
                }
            } else {
                $params = array(
                    'credit' => $amount,
                    'title' => '',
                    'customer_email' => $email,
                    'receiver_email' => $customer->getEmail(),
                    'receiver_customer_id' => $customer->getId(),
                    'expiration_date' => $expiration_date,
                );

                $recipient_transaction = $transaction_helper->createNonTransaction(
                    $customer,
                    $params,
                    Magestore_TruGiftCard_Model_Type::TYPE_TRANSACTION_RECEIVE_FROM_SHARING,  // type
                    $status
                );

                if ($recipient_transaction != null && $recipient_transaction->getId())
                    $recipient_transaction_id = $recipient_transaction->getId();
                else
                    throw new Exception(
                        Mage::helper('trugiftcard')->__('Cannot create a transaction for recipient.')
                    );
            }
            /* END create transaction for recipient first */

            /* CREATE transaction for sender */
            $params = array(
                'credit' => -$amount,
                'title' => '',
                'receiver_email' => $email,
                'receiver_customer_id' => $is_exist ? $customer_receiver->getId() : '',
                'expiration_date' => $expiration_date,
                'recipient_transaction_id' => $recipient_transaction_id,
            );

            if ($truGiftCardAccount != null) {
                $share_transaction = $transaction_helper->createTransaction(
                    $truGiftCardAccount,
                    $params,
                    Magestore_TruGiftCard_Model_Type::TYPE_TRANSACTION_SHARING,  // type
                    $status,
                    $expiration_date
                );

                if ($share_transaction != null && $share_transaction->getId()) {
                    $recipient_transaction->setData('recipient_transaction_id', $share_transaction->getId())->save();
                } else {
                    throw new Exception(
                        Mage::helper('trugiftcard')->__('Cannot create a transaction for sender.')
                    );
                }
            }
            /* END transaction for sender */

            $transaction_helper->sendEmailWhenSharingTruGiftCard(
                $customer->getId(),
                $amount,
                $is_exist,
                $email,
                $message,
                $status,
                $expiration_date
            );

            $transactionSave->save();
            $connection->commit();

        } catch (Exception $e) {
            $connection->rollback();
            Mage::getSingleton('core/session')->addError(
                Mage::helper('trugiftcard')->__($e->getMessage())
            );
        }

        $money = Mage::helper('core')->currency($amount, true, false);
        Mage::getSingleton('core/session')->addSuccess(
            Mage::helper('trugiftcard')->__('Congrats! You have just sent <b>' . $money . '</b> to <b>' . $email . '</b> successfully!')
        );
        $this->_redirectUrl(Mage::getUrl('*/*/share'));
    }
}
