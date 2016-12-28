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
 * RewardPoints Index Controller
 * 
 * @category    Magestore
 * @package     Magestore_RewardPoints
 * @author      Magestore Developer
 */
class Magestore_RewardPoints_IndexController extends Mage_Core_Controller_Front_Action {

    /**
     * check customer is logged in
     */
    public function preDispatch() {
        parent::preDispatch();
        if (!$this->getRequest()->isDispatched()) {
            return;
        }
        if (!Mage::helper('rewardpoints')->isEnable()) {
            $this->_redirect('customer/account');
            $this->setFlag('', Mage_Core_Controller_Varien_Action::FLAG_NO_DISPATCH, true);
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

    public function redirectLoginAction() {
        if (!Mage::helper('customer')->isLoggedIn()) {
            $url = base64_decode($this->getRequest()->getParam('redirect'));
            if (strpos($url, 'checkout/onepage')) {
                $url = Mage::getUrl('checkout/onepage/index');
            }
            //Mage::getSingleton('customer/session')->setBeforeAuthUrl($url);
            Mage::getSingleton('customer/session')->setAfterAuthUrl($url);
        }
        $this->_redirect('customer/account/login');
    }

    /**
     * index action
     */
    public function indexAction() {
        $this->loadLayout();
        $this->_title(Mage::helper('rewardpoints')->__('My Reward Points'));
        $this->renderLayout();
    }

    /**
     * index action
     */
    public function shareTruWalletAction() {
        $this->loadLayout();
        $this->_title(Mage::helper('rewardpoints')->__('Share TruWallet Money'));
        $this->renderLayout();
    }


    /**
     * transaction action
     */
    public function transactionsAction() {
        $this->loadLayout();
        $this->_title(Mage::helper('rewardpoints')->__('Point Transactions'));
        $this->renderLayout();
    }

    /**
     * policy action
     */
    public function policyAction() {
        $this->loadLayout();
        $page = Mage::getSingleton('cms/page');
        if ($page && $page->getId()) {
            $this->_title($page->getContentHeading());
        } else {
            $this->_title(Mage::helper('rewardpoints')->__('Reward Policy'));
        }
        $this->renderLayout();
    }

    /**
     * setting action
     */
    public function settingsAction() {
        $this->loadLayout();
        $this->_title(Mage::helper('rewardpoints')->__('Reward Points Settings'));
        $this->renderLayout();
    }

    /**
     * setting post action
     */
    public function settingsPostAction() {
        if ($this->getRequest()->isPost() && Mage::getSingleton('customer/session')->isLoggedIn()
        ) {
            $customerId = Mage::getSingleton('customer/session')->getCustomerId();
            $rewardAccount = Mage::getModel('rewardpoints/customer')->load($customerId, 'customer_id');
            if (!$rewardAccount->getId()) {
                $rewardAccount->setCustomerId($customerId)
                        ->setData('point_balance', 0)
                        ->setData('holding_balance', 0)
                        ->setData('spent_balance', 0);
            }
            $rewardAccount->setIsNotification((boolean) $this->getRequest()->getPost('is_notification'))
                    ->setExpireNotification((boolean) $this->getRequest()->getPost('expire_notification'));
            try {
                $rewardAccount->save();
                Mage::getSingleton('core/session')->addSuccess(Mage::helper('rewardpoints')->__('Your settings has been updated successfully.'));
            } catch (Exception $e) {
                Mage::getSingleton('core/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/settings');
    }

    public function updateDbAction(){
        $setup = new Mage_Core_Model_Resource_Setup();
        $installer = $setup;
        $installer->startSetup();
        $installer->run("
            ALTER TABLE {$setup->getTable('rewardpoints/customer')} MODIFY `product_credit` DECIMAL(10,2) NOT NULL default 0;
            ALTER TABLE {$setup->getTable('rewardpoints/transaction')} MODIFY `product_credit` DECIMAL(10,2) NOT NULL default 0;
        ");
        $installer->endSetup();
        echo "success";
    }

    public function sendTruWalletAction()
    {
        $amount = $this->getRequest()->getParam('share_amount');
        $email = filter_var($this->getRequest()->getParam('share_email'), FILTER_SANITIZE_EMAIL);
        $customer = Mage::getModel('customer/customer')->load(Mage::getSingleton('customer/session')->getCustomerId());

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            Mage::getSingleton('core/session')->addError(
                Mage::helper('rewardpoints')->__($email.' is not a valid email address')
            );
            $this->_redirectUrl(Mage::getUrl('*/*/shareTruWallet'));
            return;
        }

        if(!filter_var($amount, FILTER_VALIDATE_FLOAT)){
            Mage::getSingleton('core/session')->addError(
                Mage::helper('rewardpoints')->__($amount.' is not a valid number')
            );
            $this->_redirectUrl(Mage::getUrl('*/*/shareTruWallet'));
            return;
        }

        $account = Mage::getModel('rewardpoints/customer')->load($customer->getId(), 'customer_id');
        if($account->getProductCredit() < 0 || $amount > $account->getProductCredit()){
            Mage::getSingleton('core/session')->addError(
                Mage::helper('rewardpoints')->__('Sorry, your balance is less than what you want to share. Please enter a new amount.')
            );
            $this->_redirectUrl(Mage::getUrl('*/*/shareTruWallet'));
            return;
        }

        $customer_receiver = Mage::getModel("customer/customer");
        $customer_receiver->setWebsiteId(Mage::app()->getStore()->getWebsiteId());
        $customer_receiver->loadByEmail($email);
        $is_exist = false;
        if($customer_receiver->getId())
            $is_exist = true;

        $shareObject = new Varien_Object();
        $shareObject->setData('product_credit', -$amount);
        $shareObject->setData('point_amount', 0);
        $shareObject->setData('customer_exist', $is_exist);
        $shareObject->setData('email', $email);
        $shareObject->setData('is_send', true);

        Mage::helper('rewardpoints/action')
            ->addTransaction(
                'share_credit', $customer, $shareObject
            );

        if($customer_receiver->getId()){
            $receiveObject = new Varien_Object();
            $receiveObject->setData('product_credit', $amount);
            $receiveObject->setData('point_amount', 0);
            $receiveObject->setData('customer_exist', $is_exist);
            $receiveObject->setData('email', $customer->getEmail());
            Mage::helper('rewardpoints/action')
                ->addTransaction(
                    'receive_credit', $customer_receiver, $receiveObject
                );
        }

        $money = Mage::helper('core')->currency($amount, true, false);
        Mage::getSingleton('core/session')->addSuccess(
            Mage::helper('rewardpoints')->__('Congrats! You have just sent <b>'.$money.'</b> to <b>'.$email.'</b> successfully!')
        );
        $this->_redirectUrl(Mage::getUrl('*/*/shareTruWallet'));
    }

    public function registerAction()
    {
		$email = $this->getRequest()->getParam('email');
		Mage::getSingleton('core/session')->setEmailRefer($email);
    }
	
	public function updateTransactionAction()
    {
        $orders = Mage::getModel('sales/order')->getCollection()
            ->addAttributeToFilter('status','pending')
            ->setOrder('entity_id','desc')
            ;

        if(sizeof($orders) > 0)
        {
            foreach($orders as $order)
            {
                $customer = Mage::getModel('customer/customer')->load($order->getCustomerId());
                if (!$customer->getId()) {
                    continue;
                }
                Mage::helper('rewardpoints/action')->addTransaction(
                    'earning_invoice', $customer, $order
                );
            }
			echo 'success';
        } else {
			echo 'None order';
		}
    }
}
