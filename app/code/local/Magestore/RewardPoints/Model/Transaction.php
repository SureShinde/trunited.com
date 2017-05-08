<?php

/**
 * Magestore
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the Magestore.com license that is
 * available through the world-wide-web at this URL:
 * http://www.magestore.com/license-agreement.html
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category    Magestore
 * @package     Magestore_RewardPoints
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * Rewardpoints Transaction Information Model
 * 
 * @category    Magestore
 * @package     Magestore_RewardPoints
 * @author      Magestore Developer
 */
class Magestore_RewardPoints_Model_Transaction extends Mage_Core_Model_Abstract {

    const STATUS_PENDING = 1;
    const STATUS_ON_HOLD = 2;
    const STATUS_COMPLETED = 3;
    const STATUS_CANCELED = 4;
    const STATUS_EXPIRED = 5;
    const ACTION_TYPE_BOTH = 0;
    const ACTION_TYPE_EARN = 1;
    const ACTION_TYPE_SPEND = 2;
    const ACTION_TYPE_TRANSFER = 3;
    const ACTION_TYPE_SHARE = 4;
    const ACTION_TYPE_RECEIVE_FROM_SHARING = 5;
    const ACTION_TYPE_RECEIVE_FROM_ORDER_OF_NEW_CUSTOMER = 6;
    const ACTION_TYPE_RECEIVE_FROM_REGISTER_NEW_CUSTOMER = 7;
    const ACTION_TYPE_RECEIVE_FROM_PURCHASE_TRUWALLET_PRODUCT = 8;
    const ACTION_TYPE_RECEIVE_FROM_ECHECK_PAYMENT = 9;
    const XML_PATH_MAX_BALANCE = 'rewardpoints/earning/max_balance';
    const XML_PATH_EMAIL_ENABLE = 'rewardpoints/email/enable';
    const XML_PATH_EMAIL_SENDER = 'rewardpoints/email/sender';
    const XML_PATH_EMAIL_UPDATE_BALANCE_TPL = 'rewardpoints/email/update_balance';
    const XML_PATH_EMAIL_BEFORE_EXPIRE_TPL = 'rewardpoints/email/before_expire_transaction';
    const XML_PATH_EMAIL_EXPIRE_DAYS = 'rewardpoints/email/before_expire_days';
    const XML_PATH_EMAIL_SHARE_EMAIL_CUSTOMER = 'rewardpoints/email/share_email_customer';
    const XML_PATH_EMAIL_SHARE_EMAIL_NON_CUSTOMER = 'rewardpoints/email/share_email_non_customer';
    const XML_PATH_EMAIL_SHARE_EMAIL_EXPIRY_DATE = 'rewardpoints/email/share_email_expiry_date';

    /**
     * Redefine event Prefix, event object
     * 
     * @var string
     */
    protected $_eventPrefix = 'rewardpoints_transaction';
    protected $_eventObject = 'rewardpoints_transaction';

    public function _construct() {
        parent::_construct();
        $this->_init('rewardpoints/transaction');
    }

    protected function _beforeSave() {
        $this->setData('updated_time', now());
        return parent::_beforeSave();
    }

    /**
     * get transaction status as hash array
     * 
     * @return array
     */
    public function getStatusHash() {
        return array(
            self::STATUS_PENDING => Mage::helper('rewardpoints')->__('Pending'),
            self::STATUS_ON_HOLD => Mage::helper('rewardpoints')->__('On Hold'),
            self::STATUS_COMPLETED => Mage::helper('rewardpoints')->__('Complete'),
            self::STATUS_CANCELED => Mage::helper('rewardpoints')->__('Canceled'),
            self::STATUS_EXPIRED => Mage::helper('rewardpoints')->__('Expired'),
        );
    }

    /**
     * get transaction status as hash array
     * 
     * @return array
     */
    public function getStatusArray() {
        $options = array();
        foreach ($this->getStatusHash() as $value => $label) {
            $options[] = array(
                'value' => $value,
                'label' => $label,
            );
        }
        return $options;
    }

    /**
     * get action model of current transaction
     * 
     * @return Magestore_RewardPoints_Model_Action_Interface
     */
    public function getActionInstance() {
        return Mage::helper('rewardpoints/action')->getActionModel($this->getAction());
    }

    /**
     * get transaction title as HTML
     * 
     * @return string
     */
    public function getTitleHtml() {
        if ($this->hasData('title') && $this->getData('title') != '') {
            return $this->getData('title');
        }
        try {
            $this->setData('title_html', $this->getActionInstance()->getActionLabel());
        } catch (Exception $e) {
            Mage::logException($e);
            $this->setData('title_html', $this->getTitle());
        }
        return $this->getData('title_html');
    }

    /**
     * get Reward Account linked to this transaction
     * 
     * @return Magestore_RewardPoints_Model_Customer
     */
    public function getRewardAccount() {
        if (!$this->hasData('reward_account')) {
            $this->setData('reward_account', Mage::getModel('rewardpoints/customer')->load($this->getRewardId())
            );
        }
        return $this->getData('reward_account');
    }

    /**
     * @param array $data
     * @return $this
     * @throws Exception
     */
    public function createTransaction($data = array()) {
        $this->addData($data);
        if (!$this->getPointAmount()) {
            // Don't create transaction without point amount
            return $this;
        }
        if ($this->getCustomer()) {
            $rewardAccount = Mage::helper('rewardpoints/customer')->getAccountByCustomer($this->getCustomer());
        } else {
            $rewardAccount = Mage::helper('rewardpoints/customer')->getAccountByCustomerId($this->getCustomerId());
        }
        if (!$rewardAccount->getId()) {
            $rewardAccount->setCustomerId($this->getCustomerId())
                    ->setData('point_balance', 0)
                    ->setData('holding_balance', 0)
                    ->setData('spent_balance', 0)
                    ->setData('is_notification', 1)
                    ->setData('expire_notification', 1)
                    ->save();
        }
        if ($rewardAccount->getPointBalance() + $this->getPointAmount() < 0) {
            //Hai.Tran 18/11/2013 fix refund when balance < refund points
            if ($this->getData('creditmemo_holding') && $rewardAccount->getHoldingBalance() + $this->getPointAmount() >= 0) {

            } else {
                if ($this->getData('creditmemo_transaction'))
                    Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Account balance of Customer is not enough to take points back.'));
                throw new Exception(
                Mage::helper('rewardpoints')->__('Account balance is not enough to create this transaction.')
                );
            }
        }

        $this->setData('reward_id', $rewardAccount->getId())
                ->setData('point_used', 0);

        // Always complete reduce transaction when created
        if ($this->getPointAmount() < 0) {
            if (!$this->getData('status')) {
                $this->setData('status', self::STATUS_COMPLETED);
            }
        } else {
            $this->setData('real_point', $this->getPointAmount());
        }
        // If not set status, set it to Pending
        if (!$this->getData('status')) {
            $this->setData('status', self::STATUS_PENDING);
        }
        // Holding transaction, add holding balance
        if ($this->getData('status') == self::STATUS_ON_HOLD) {
            $rewardAccount->setHoldingBalance($rewardAccount->getHoldingBalance() + $this->getPointAmount());
        }
        // Transaction is spending, add spent balance
        if ($this->getData('action_type') == self::ACTION_TYPE_SPEND) {
            $rewardAccount->setSpentBalance($rewardAccount->getSpentBalance() - $this->getPointAmount());
        }

        // Completed when create transaction
        if ($this->getData('status') == self::STATUS_COMPLETED) {
            $maxBalance = (int) Mage::getStoreConfig(self::XML_PATH_MAX_BALANCE, $this->getStoreId());
            if ($maxBalance > 0 && $this->getPointAmount() > 0 && $rewardAccount->getPointBalance() + $this->getPointAmount() > $maxBalance
            ) {
                if ($maxBalance > $rewardAccount->getPointBalance()) {
                    $this->setPointAmount($maxBalance - $rewardAccount->getPointBalance());
                    $this->setRealPoint($maxBalance - $rewardAccount->getPointBalance());
                    $rewardAccount->setPointBalance($maxBalance);
                    $rewardAccount->save();
                    $this->save();
                    $this->sendUpdateBalanceEmail($rewardAccount);
                } else {
                    return $this;
                }
            } else {
                if ($this->getAction() == 'spending_creditmemo') {
                    $rewardAccount->setProductCredit($rewardAccount->getProductCredit() + $this->getPointAmount());
                    $rewardAccount->save();
                    $this->save();
                    $this->sendUpdateBalanceEmail($rewardAccount);
                } else {
                    $rewardAccount->setPointBalance($rewardAccount->getPointBalance() + $this->getPointAmount());
                    $rewardAccount->save();
                    $this->save();
                    $this->sendUpdateBalanceEmail($rewardAccount);
                }
            }
        } else {
            if ($this->getPointAmount() < 0 && $this->getData('status') == self::STATUS_ON_HOLD && $this->getData('action_type') == self::ACTION_TYPE_EARN
            ) {
                $isHoldingStatus = true;
                $this->setData('status', self::STATUS_COMPLETED);
                // Update real points and point used for holding transaction (earning) depend on account/ order
                $this->_getResource()->updateRealPointHolding($this);
            }

            if($this->getAction() == 'admin' && $this->getIsOnHold())
            {
                $this->setData('hold_point', $this->getPointAmount());
            }
            $rewardAccount->save();
            $this->save();
        }

        // Save transactions and customer to Database
        if ($this->getPointAmount() < 0 && empty($isHoldingStatus)) {
            if ($this->getData('action_type') == self::ACTION_TYPE_EARN) {
                // Update real points for transaction depend on account/ order
                $this->_getResource()->updateRealPoint($this);
            }
            // Update other transactions (point_used) depend on Account
            $this->_getResource()->updatePointUsed($this);
        }

        // Dispatch Event when create an action
        Mage::dispatchEvent($this->_eventPrefix . '_created_' . $this->getData('action')
                , $this->_getEventData()
        );

        return $this;
    }

    public function createTransactionProductCredit($data = array()) {
        $this->addData($data);
        if (!$this->getProductCredit() && $this->getPointAmount() > 0) {
            // Don't create transaction without product credit amount
            return $this;
        }
        if ($this->getCustomer()) {
            $rewardAccount = Mage::helper('rewardpoints/customer')->getAccountByCustomer($this->getCustomer());
        } else {
            $rewardAccount = Mage::helper('rewardpoints/customer')->getAccountByCustomerId($this->getCustomerId());
        }
        if (!$rewardAccount->getId()) {
            $rewardAccount->setCustomerId($this->getCustomerId())
                ->setData('point_balance', 0)
                ->setData('holding_balance', 0)
                ->setData('spent_balance', 0)
                ->setData('is_notification', 1)
                ->setData('expire_notification', 1)
                ->save();
        }

//        if ($rewardAccount->getPointBalance() + $this->getPointAmount() < 0) {
//            //Hai.Tran 18/11/2013 fix refund when balance < refund points
//            if ($this->getData('creditmemo_holding') && $rewardAccount->getHoldingBalance() + $this->getPointAmount() >= 0) {
//
//            } else {
//                if ($this->getData('creditmemo_transaction'))
//                    Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Account balance of Customer is not enough to take points back.'));
//                throw new Exception(
//                    Mage::helper('rewardpoints')->__('Account balance is not enough to create this transaction.')
//                );
//            }
//        }
//
        $this->setData('reward_id', $rewardAccount->getId())
            ->setData('point_used', 0);

        // Always complete reduce transaction when created
        if ($this->getPointAmount() < 0) {
            if (!$this->getData('status')) {
                $this->setData('status', self::STATUS_COMPLETED);
            }
        } else {
            $this->setData('real_point', $this->getPointAmount());
        }
        // If not set status, set it to Pending
        if (!$this->getData('status')) {
            $this->setData('status', self::STATUS_PENDING);
        }
        // Holding transaction, add holding balance
        if ($this->getData('status') == self::STATUS_ON_HOLD) {
            $rewardAccount->setHoldingBalance($rewardAccount->getHoldingBalance() + $this->getPointAmount());
        }
        // Transaction is spending, add spent balance
        if ($this->getData('action_type') == self::ACTION_TYPE_SPEND) {
            $rewardAccount->setSpentBalance($rewardAccount->getSpentBalance() - $this->getPointAmount());
        }

        $this->setData('receiver_email',$data['email']);
        if(!$data['customer_exist']){
            $this->setData('status', self::STATUS_PENDING);
            $rewardAccount->setProductCredit($rewardAccount->getProductCredit() + $this->getProductCredit()+ $this->getPointAmount());
            $this->setProductCredit($this->getProductCredit()+ $this->getPointAmount());
            $this->setPointAmount(0);
            $rewardAccount->save();
            $this->save();
            $this->sendUpdateBalanceEmail($rewardAccount);
        }

        // Completed when create transaction
        if ($this->getData('status') == self::STATUS_COMPLETED) {
            $maxBalance = (int) Mage::getStoreConfig(self::XML_PATH_MAX_BALANCE, $this->getStoreId());
            if ($maxBalance > 0 && $this->getPointAmount() > 0 && $rewardAccount->getProductCredit() + $this->getPointAmount() > $maxBalance
            ) {
                if ($maxBalance > $rewardAccount->getPointBalance()) {
                    $this->setProductCredit($maxBalance - $rewardAccount->getProductCredit());
                    $this->setRealPoint($maxBalance - $rewardAccount->getProductCredit());
                    $rewardAccount->setProductCredit($maxBalance);
                    $rewardAccount->save();
                    $this->save();
                    $this->sendUpdateBalanceEmail($rewardAccount);
                } else {
                    return $this;
                }
            } else {
                $dataPost = Mage::app()->getRequest()->getPost();
                if (!isset($dataPost['rewardpoints']['product_credit'])) {
                    $rewardAccount->setProductCredit($rewardAccount->getProductCredit() + $this->getProductCredit()+ $this->getPointAmount());
                    $this->setProductCredit($this->getProductCredit()+ $this->getPointAmount());
                    $this->setPointAmount(0);
                    $rewardAccount->save();
                    $this->save();
                    $this->sendUpdateBalanceEmail($rewardAccount);
                }
            }
        } else {
            if ($this->getPointAmount() < 0 && $this->getData('status') == self::STATUS_ON_HOLD && $this->getData('action_type') == self::ACTION_TYPE_EARN
            ) {
                $isHoldingStatus = true;
                $this->setData('status', self::STATUS_COMPLETED);
                // Update real points and point used for holding transaction (earning) depend on account/ order
                $this->_getResource()->updateRealPointHolding($this);
            }
            $rewardAccount->save();
            $this->save();
        }

        if($data['is_send']){
            $this->sendEmailWhenSharingTruWallet($rewardAccount, $this->getProductCredit(),$data['customer_exist'], $data['email'], $data['message']);
        }

        if (!Mage::registry(('product_credit'))) {
            if (isset($dataPost['rewardpoints']['product_credit'])) {
                $rewardAccount->setProductCredit($rewardAccount->getProductCredit() + $this->getProductCredit());
                $rewardAccount->save();
                $this->setProductCredit($this->getProductCredit());
                $this->setRealPoint($this->getProductCredit());
                $this->save();
            }
            Mage::register('product_credit', true);
        }

        // Save transactions and customer to Database
        if ($this->getProductCredit() < 0 && empty($isHoldingStatus)) {
            if ($this->getData('action_type') == self::ACTION_TYPE_EARN) {
                // Update real points for transaction depend on account/ order
                $this->_getResource()->updateRealPoint($this);
            }
            // Update other transactions (point_used) depend on Account
            $this->_getResource()->updatePointUsed($this);
        }

        // Dispatch Event when create an action
        Mage::dispatchEvent($this->_eventPrefix . '_created_' . $this->getData('action')
            , $this->_getEventData()
        );

        return $this;
    }

    /**
     * Hold Transaction
     * 
     * @return Magestore_RewardPoints_Model_Transaction
     */
    public function holdTransaction() {
        return $this;
    }

    /**
     * @param $account
     * @param $amount
     * @param $type
     * @param $email
     * @param $message
     * @return $this
     */
    public function sendEmailWhenSharingTruWallet($account, $amount, $type, $email, $message)
    {
        if (!Mage::getStoreConfigFlag(self::XML_PATH_EMAIL_ENABLE, $this->getStoreId())) {
            return $this;
        }

        $store = Mage::app()->getStore($this->getStoreId());
        $translate = Mage::getSingleton('core/translate');
        $translate->setTranslateInline(false);
        $customer = Mage::getModel('customer/customer')->load($account->getCustomerId());

        $name = Mage::helper('rewardpoints')->__('There');
        $current_credit = 0;
        $link = '';
        if($type) {
            $email_path = Mage::getStoreConfig(self::XML_PATH_EMAIL_SHARE_EMAIL_CUSTOMER, $store);
            $customer_receiver = Mage::getModel("customer/customer");
            $customer_receiver->setWebsiteId(Mage::app()->getStore()->getWebsiteId());
            $customer_receiver->loadByEmail($email);
            if($customer_receiver->getId()){
                $name = $customer_receiver->getName();
                $rewardAccount = Mage::helper('rewardpoints/customer')->getAccountByCustomerId($customer_receiver->getId());
                if($rewardAccount->getId())
                    $current_credit = $rewardAccount->getProductCredit();
            }

        } else {
            $email_path =  Mage::getStoreConfig(self::XML_PATH_EMAIL_SHARE_EMAIL_NON_CUSTOMER, $store);
			$_sender = Mage::getModel('customer/customer')->load($account->getCustomerId());
            $link = Mage::getUrl('rewardpoints/customer/register',array('email'=>$_sender->getEmail()));
        }


        $data = array(
            'store' => $store,
            'customer_name' => $name,
            'amount' => Mage::helper('core')->currency(abs($amount), true, false),
            'sender_email' => $customer->getEmail(),
            'title' => $this->getTitle(),
            'point_balance' => Mage::helper('rewardpoints/point')->format($current_credit, $store),
            'status' => $this->getStatusLabel(),
			'register_link' => $link,
			'message' => $message,
        );

        Mage::getModel('core/email_template')
            ->setDesignConfig(array(
                'area' => 'frontend',
                'store' => Mage::app()->getStore()->getId()
            ))->sendTransactional(
                $email_path,
                Mage::getStoreConfig(self::XML_PATH_EMAIL_SENDER, Mage::app()->getStore()->getId()),
                $email,
                $name,
                $data
            );

        $translate->setTranslateInline(true);
//        zend_debug::dump($sent);exit;
        return $this;
    }

    /**
     * @return $this
     * @throws Exception
     */
    public function completeTransaction() {
        if (!$this->getId() || !$this->getCustomerId() || !$this->getRewardId() || $this->getPointAmount() <= 0 || !in_array($this->getStatus(), array(self::STATUS_PENDING, self::STATUS_ON_HOLD))
        ) {
            throw new Exception(Mage::helper('rewardpoints')->__('Invalid transaction data to complete.'));
        }
        $rewardAccount = $this->getRewardAccount();
        if ($this->getData('status') == self::STATUS_ON_HOLD) {
            $rewardAccount->setHoldingBalance($rewardAccount->getHoldingBalance() - $this->getRealPoint());
        }

        // dispatch event when complete a transaction
        Mage::dispatchEvent($this->_eventPrefix . '_complete_' . $this->getData('action'), $this->_getEventData());

        $this->setStatus(self::STATUS_COMPLETED);

        $maxBalance = (int) Mage::getStoreConfig(self::XML_PATH_MAX_BALANCE, $this->getStoreId());
        if ($maxBalance > 0 && $this->getRealPoint() > 0 && $rewardAccount->getPointBalance() + $this->getRealPoint() > $maxBalance
        ) {
            if ($maxBalance > $rewardAccount->getPointBalance()) {
                $this->setPointAmount($maxBalance - $rewardAccount->getPointBalance() + $this->getPointAmount() - $this->getRealPoint());
                $this->setRealPoint($maxBalance - $rewardAccount->getPointBalance());
                $rewardAccount->setPointBalance($maxBalance);
                $this->sendUpdateBalanceEmail($rewardAccount);
            } else {
                throw new Exception(
                Mage::helper('rewardpoints')->__('Maximum points allowed in account balance is %s.', $maxBalance)
                );
            }
        } else {
            $rewardAccount->setPointBalance($rewardAccount->getPointBalance() + $this->getRealPoint());
            $this->sendUpdateBalanceEmail($rewardAccount);
        }

        // Save reward account and transaction to database
        $rewardAccount->save();

        $this->save();
        return $this;
    }

    /**
     * Cancel Transaction, allow for Pending, On Hold and Completed transaction
     * only cancel transaction with amount > 0
     * Cancel mean that similar as we do not have this transaction
     * 
     * @return Magestore_RewardPoints_Model_Transaction
     */
    public function cancelTransaction() {
        if (!$this->getId() || !$this->getCustomerId() || !$this->getRewardId() || $this->getPointAmount() <= 0 || $this->getStatus() > self::STATUS_COMPLETED || !$this->getStatus()
        ) {
            throw new Exception(Mage::helper('rewardpoints')->__('Invalid transaction data to cancel.'));
        }

        // dispatch event when complete a transaction
        Mage::dispatchEvent($this->_eventPrefix . '_cancel_' . $this->getData('action'), $this->_getEventData());

        if ($this->getStatus() != self::STATUS_COMPLETED) {
            if ($this->getData('status') == self::STATUS_ON_HOLD) {
                $rewardAccount = $this->getRewardAccount();
                $rewardAccount->setHoldingBalance($rewardAccount->getHoldingBalance() - $this->getRealPoint());
                $rewardAccount->save();
            }
            $this->setStatus(self::STATUS_CANCELED);
            $this->save();
            return $this;
        }
        $this->setStatus(self::STATUS_CANCELED);
        $rewardAccount = $this->getRewardAccount();
        if ($rewardAccount->getPointBalance() < $this->getRealPoint()) {
            throw new Exception(Mage::helper('rewardpoints')->__('Account balance is not enough to cancel.'));
        }
        $rewardAccount->setPointBalance($rewardAccount->getPointBalance() - $this->getRealPoint());
        $this->sendUpdateBalanceEmail($rewardAccount);

        // Save reward account and transaction to database
        $rewardAccount->save();
        $this->save();

        // Change point used for other transaction
        if ($this->getPointUsed() > 0) {
            $pointAmount = $this->getPointAmount();
            $this->setPointAmount(-$this->getPointUsed());
            $this->_getResource()->updatePointUsed($this);
            $this->setPointAmount($pointAmount);
        }

        return $this;
    }

    /**
     * Expire Transaction, allow for Pending, On Hold and Completed transaction
     * only expire transaction with amount > 0
     * 
     * @return Magestore_RewardPoints_Model_Transaction
     */
    public function expireTransaction() {
        if (!$this->getId() || !$this->getCustomerId() || !$this->getRewardId() || $this->getPointAmount() <= $this->getPointUsed() || $this->getStatus() > self::STATUS_COMPLETED || !$this->getStatus() || strtotime($this->getExpirationDate()) > time() || !$this->getExpirationDate()
        ) {
            throw new Exception(Mage::helper('rewardpoints')->__('Invalid transaction data to expire.'));
        }

        // dispatch event when complete a transaction
        Mage::dispatchEvent($this->_eventPrefix . '_expire_' . $this->getData('action'), $this->_getEventData());

        if ($this->getStatus() != self::STATUS_COMPLETED) {
            if ($this->getData('status') == self::STATUS_ON_HOLD) {
                $rewardAccount = $this->getRewardAccount();
                $rewardAccount->setHoldingBalance($rewardAccount->getHoldingBalance() - $this->getRealPoint());
                $rewardAccount->save();
            }
            $this->setStatus(self::STATUS_EXPIRED);
            $this->save();
            return $this;
        }

        $this->setStatus(self::STATUS_EXPIRED);
        $rewardAccount = $this->getRewardAccount();
        $rewardAccount->setPointBalance(
                $rewardAccount->getPointBalance() - $this->getPointAmount() + $this->getPointUsed()
        );
        $this->sendUpdateBalanceEmail($rewardAccount);

        // Save reward account and transaction to database
        $rewardAccount->save();
        $this->save();
        return $this;
    }

        /**
     * Expire Credits Transaction, allow for Pending, On Hold and Completed transaction
     * only expire transaction with product credits amount > 0
     *
     * @return Magestore_RewardPoints_Model_Transaction
     */
    public function expireCreditTransaction() {
        if (!$this->getId() || !$this->getCustomerId() || !$this->getRewardId() || $this->getProductCredit() <= $this->getPointUsed() || $this->getStatus() > self::STATUS_COMPLETED || !$this->getStatus() || strtotime($this->getExpirationDateCredit()) > time() || !$this->getExpirationDateCredit()
        ) {
            throw new Exception(Mage::helper('rewardpoints')->__('Invalid transaction data to expire.'));
        }

        // dispatch event when complete a transaction
        Mage::dispatchEvent($this->_eventPrefix . '_expire_' . $this->getData('action'), $this->_getEventData());

        if ($this->getStatus() != self::STATUS_COMPLETED) {
            if ($this->getData('status') == self::STATUS_ON_HOLD) {
                $rewardAccount = $this->getRewardAccount();
                $rewardAccount->setHoldingBalance($rewardAccount->getHoldingBalance() - $this->getRealPoint());
                $rewardAccount->save();
            }
            $this->setStatus(self::STATUS_EXPIRED);
            $this->save();
            return $this;
        }

        $this->setStatus(self::STATUS_EXPIRED);
        $rewardAccount = $this->getRewardAccount();
        $rewardAccount->setProductCredit(
            $rewardAccount->getProductCredit() - $this->getProductCredit() + $this->getPointUsed()
        );
        $this->sendUpdateBalanceEmail($rewardAccount);

        // Save reward account and transaction to database
        $rewardAccount->save();
        $this->save();
        return $this;
    }

    /**
     * get status label of transaction
     * 
     * @return string
     */
    public function getStatusLabel() {
        $statushash = $this->getStatusHash();
        if (isset($statushash[$this->getStatus()])) {
            return $statushash[$this->getStatus()];
        }
        return '';
    }

    /**
     * send Update Balance to customer
     * 
     * @param Magestore_RewardPoints_Model_Customer $rewardAccount
     * @return Magestore_RewardPoints_Model_Transaction
     */
    public function sendUpdateBalanceEmail($rewardAccount = null) {
		return $this;
        if (!Mage::getStoreConfigFlag(self::XML_PATH_EMAIL_ENABLE, $this->getStoreId())) {
            return $this;
        }
        if (is_null($rewardAccount)) {
            $rewardAccount = $this->getRewardAccount();
        }
        if (!$rewardAccount->getIsNotification()) {
            return $this;
        }
        $customer = $this->getCustomer();
        if (!$customer) {
            $customer = Mage::getModel('customer/customer')->load($rewardAccount->getCustomerId());
        }
        if (!$customer->getId()) {
            return $this;
        }

        $store = Mage::app()->getStore($this->getStoreId());
        $translate = Mage::getSingleton('core/translate');
        $translate->setTranslateInline(false);

        Mage::getModel('core/email_template')
                ->setDesignConfig(array(
                    'area' => 'frontend',
                    'store' => $store->getId()
                ))->sendTransactional(
                Mage::getStoreConfig(self::XML_PATH_EMAIL_UPDATE_BALANCE_TPL, $store),
                Mage::getStoreConfig(self::XML_PATH_EMAIL_SENDER, $store),
                $customer->getEmail(),
                $customer->getName(),
                array(
                    'store' => $store,
                    'customer' => $customer,
                    'title' => $this->getTitle(),
                    'amount' => $this->getPointAmount(),
                    'total' => $rewardAccount->getPointBalance(),
                    'point_amount' => Mage::helper('rewardpoints/point')->format($this->getPointAmount(), $store),
                    'point_balance' => Mage::helper('rewardpoints/point')->format($rewardAccount->getPointBalance(), $store),
                    'status' => $this->getStatusLabel(),
                )
        );

        $translate->setTranslateInline(true);
        return $this;
    }

    /**
     * Send email to customer before transaction is expired
     * 
     * @return Magestore_RewardPoints_Model_Transaction
     */
    public function sendBeforeExpireEmail() {
        if (!Mage::getStoreConfigFlag(self::XML_PATH_EMAIL_ENABLE, $this->getStoreId())) {
            return $this;
        }
        $rewardAccount = $this->getRewardAccount();
        if (!$rewardAccount->getExpireNotification()) {
            return $this;
        }
        $customer = $this->getCustomer();
        if (!$customer) {
            $customer = Mage::getModel('customer/customer')->load($rewardAccount->getCustomerId());
        }
        if (!$customer->getId()) {
            return $this;
        }

        $store = Mage::app()->getStore($this->getStoreId());
        $translate = Mage::getSingleton('core/translate');
        $translate->setTranslateInline(false);

        Mage::getModel('core/email_template')
                ->setDesignConfig(array(
                    'area' => 'frontend',
                    'store' => $store->getId()
                ))->sendTransactional(
                Mage::getStoreConfig(self::XML_PATH_EMAIL_BEFORE_EXPIRE_TPL, $store), Mage::getStoreConfig(self::XML_PATH_EMAIL_SENDER, $store), $customer->getEmail(), $customer->getName(), array(
            'store' => $store,
            'customer' => $customer,
            'title' => $this->getTitle(),
            'amount' => $this->getPointAmount(),
            'spent' => $this->getPointUsed(),
            'total' => $rewardAccount->getPointBalance(),
            'point_amount' => Mage::helper('rewardpoints/point')->format($this->getPointAmount(), $store),
            'point_used' => Mage::helper('rewardpoints/point')->format($this->getPointUsed(), $store),
            'point_balance' => Mage::helper('rewardpoints/point')->format($rewardAccount->getPointBalance(), $store),
            'status' => $this->getStatusLabel(),
            'expirationdays' => round((strtotime($this->getExpirationDate()) - time()) / 86400),
            'expirationdate' => Mage::getModel('core/date')->date('M d, Y H:i:s', $this->getExpirationDate()),
                )
        );

        $translate->setTranslateInline(true);
        return $this;
    }

}
