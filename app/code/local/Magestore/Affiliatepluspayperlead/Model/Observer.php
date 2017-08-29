<?php

class Magestore_Affiliatepluspayperlead_Model_Observer {

    protected function _getConfigHelper() {
        return Mage::helper('affiliatepluspayperlead/config');
    }

    public function addProgramWelcome($observer) {
        if (!$this->_getConfigHelper()->getPayperleadConfig('enable')) {
            return;
        }
        $programListObj = $observer->getProgramListObject();
        $programList = $programListObj->getProgramList();
        $payperleadProgram = new Varien_Object(array(
            'code' => 'payperlead',
            'name' => Mage::helper('affiliatepluspayperlead')->__('Pay-per-lead Program'),
            'commission_type' => $this->_getConfigHelper()->getPayperleadConfig('commission_type'),
            'account_signup_commission' => $this->_getConfigHelper()->getPayperleadConfig('signup_account_commission'),
            'customer_subcribe_commission' => $this->_getConfigHelper()->getPayperleadConfig('subscribe_newsletter_commission'),
            'commission_text' => Mage::helper('affiliatepluspayperlead')->__('Commission per each account signed up: %s.', Mage::helper('core')->currency($this->_getConfigHelper()->getPayperleadConfig('signup_account_commission'))) . '<br/>' . Mage::helper('affiliatepluspayperlead')->__('Commission per each new subscription: %s.', Mage::helper('core')->currency($this->_getConfigHelper()->getPayperleadConfig('subscribe_newsletter_commission'))),
            'discount_type' => '',
            'discount' => '',
        ));
        $programList['pay-per-lead'] = $payperleadProgram;
        $programListObj->setProgramList($programList);
    }

    public function controllerActionPredispatch($observer) {
        $cookie = $observer->getCookie();
    }

    public function affiliatepluspayperlead_customer_save_before($observer) {
        $customer = $observer->getCustomer();
        if (!$customer->getId()) {
            Mage::getSingleton('customer/session')->setIsRegister(true);
        }
    }

    public function affiliatepluspayperlead_customer_save_after($observer) {
        if (!$this->_getConfigHelper()->getPayperleadConfig('enable')) {
            return;
        }
        $customer = $observer->getCustomer();
        $affiliateInfo = Mage::helper('affiliateplus/cookie')->getAffiliateInfo();
        foreach ($affiliateInfo as $info)
            if ($info['account']) {
                $account = $info['account'];
                break;
            }
        $commission = $this->_getConfigHelper()->getPayperleadConfig('signup_account_commission');
        $add_commission_twice = $this->_getConfigHelper()->getPayperleadConfig('add_commission_twice');

        $lead = Mage::getModel('affiliatepluspayperlead/lead')->loadByAccountAndCustomer($customer->getEmail(), '4');
        if (isset($account) && $account->getId()) {
            if (Mage::getSingleton('customer/session')->getIsRegister()) {
                if (!$lead->getId()) {
                    $old_customer_id = $account->getCustomerId();
                    $old_customer = Mage::getModel('customer/customer')->load($old_customer_id);
                    $lead->setAccountId($account->getId());
                    $lead->setData('account_name', $account->getName());
                    $lead->setData('account_email', $account->getEmail());
                    $lead->setData('customer_id', $customer->getId());
                    $lead->setData('customer_email', $customer->getEmail());
                    $lead->setData('type', 4);
                    $lead->setData('commission', $commission);
                    $lead->setData('created_time', now());
                    if (!$add_commission_twice) {
                        $temp_lead = Mage::getModel('affiliatepluspayperlead/lead')->loadByAccountAndCustomer($customer->getEmail(), '5');
                        if ($temp_lead->getId())
                            $lead->setData('status', 3);
                        else
                            $lead->setData('status', 2);
                    }else {
                        $lead->setData('status', 2);
                    }
                    $lead->setData('store_id', Mage::app()->getStore()->getId());
                    $lead->save();
                }
            }
        }
        if ($lead->getId()) {
            if ((!$customer->isConfirmationRequired()) || (!$customer->getConfirmation())) {
                if ($lead->getStatus() == 2) {
                    $lead->setData('status', 1);
                    // Changed By Adam 03/10/2014 to fix the issue of X2 commission
                    $account = Mage::getModel('affiliateplus/account')->setStoreId(Mage::app()->getStore()->getId())->load($lead->getAccountId());
                    /* edit by blanka */
                    // $account->setStoreId(Mage::app()->getStore()->getId());
                    /* end edit */
                    $account->setBalance(floatval($account->getBalance()) + floatval($commission));
                    $account->save();
                    $lead->save();
                }
            }
        }
    }

    public function newsletter_subscriber_save_after($observer) {
        if (!$this->_getConfigHelper()->getPayperleadConfig('enable')) {
            return;
        }
        $subscriber = $observer->getSubscriber();
        $is_deduct_commission = $this->_getConfigHelper()->getPayperleadConfig('deduct_commission_customer_unsubscribes');
        $add_commission_twice = $this->_getConfigHelper()->getPayperleadConfig('add_commission_twice');
        $isConfirmNeed = (Mage::getStoreConfig(Mage_Newsletter_Model_Subscriber::XML_PATH_CONFIRMATION_FLAG) == 1) ? true : false;
        $affiliateInfo = Mage::helper('affiliateplus/cookie')->getAffiliateInfo();
        $lead = Mage::getModel('affiliatepluspayperlead/lead')->loadByAccountAndCustomer($subscriber->getSubscriberEmail(), '5');
        $commission = $this->_getConfigHelper()->getPayperleadConfig('subscribe_newsletter_commission');

        foreach ($affiliateInfo as $info)
            if ($info['account']) {
                $account = $info['account'];
                break;
            }
        if (isset($account) && $account->getId()) {
            if ($subscriber->isObjectNew()) {
                if (!$lead->getId()) {
                    // $account->save();
                    $lead->setAccountId($account->getId());
                    $lead->setData('account_name', $account->getName());
                    $lead->setData('account_email', $account->getEmail());
                    $lead->setData('customer_id', $subscriber->getCustomerId());
                    $lead->setData('customer_email', $subscriber->getSubscriberEmail());
                    $lead->setData('type', 5);
                    $lead->setData('commission', $commission);
                    $lead->setData('created_time', now());
                    if (!$add_commission_twice) {
                        $temp_lead = Mage::getModel('affiliatepluspayperlead/lead')->loadByAccountAndCustomer($subscriber->getSubscriberEmail(), '4');
                        if ($temp_lead->getId())
                            $lead->setData('status', 3);
                        else
                            $lead->setData('status', 2);
                    }else {
                        $lead->setData('status', 2);
                    }
                    $lead->setData('store_id', Mage::app()->getStore()->getId());
                    $lead->save();
                }
            }
        }

        if ($lead->getId()) {
            if ((!$isConfirmNeed) || ($subscriber->getStatus() == Mage_Newsletter_Model_Subscriber::STATUS_SUBSCRIBED)) {
                if ($lead->getStatus() == 2) {
                    $lead->setData('status', 1);
                    $lead->save();
                    // if(!$account){
                    // Changed By Adam 03/10/2014: add ->setStoreId(Mage::app()->getStore()->getId())
                    $account = Mage::getModel('affiliateplus/account')->setStoreId(Mage::app()->getStore()->getId())->load($lead->getAccountId());
                    // }
                    $account->setBalance(floatval($account->getBalance()) + floatval($commission));
                    $account->save();
                }
            }
            if ($subscriber->getStatus() == Mage_Newsletter_Model_Subscriber::STATUS_UNSUBSCRIBED) {
                if ($lead->getStatus() == 1) {
                    $lead->setData('status', 3);
                    $lead->save();
                    if ($is_deduct_commission) {
                        // if(!$account){
                        // Changed By Adam 03/10/2014: add ->setStoreId(Mage::app()->getStore()->getId())
                        $account = Mage::getModel('affiliateplus/account')->setStoreId(Mage::app()->getStore()->getId())->load($lead->getAccountId());
                        // }
                        $account->setBalance(floatval($account->getBalance()) - floatval($commission));
                        $account->save();
                    }
                }
            }
        }
    }

    public function affiliateplus_adminhtml_add_account_tab($observer) {
        $form = $observer->getForm();
        $id = $observer->getId();
        if ($id) {
            $form->addTab('lead_section', array(
                'label' => Mage::helper('affiliatepluspayperlead')->__('Lead Details'),
                'title' => Mage::helper('affiliatepluspayperlead')->__('Lead Details'),
                'content' => $form->getLayout()->createBlock('affiliatepluspayperlead/adminhtml_account_edit_tab_lead')->toHtml(),
                'after' => 'form_section'
            ));
        }
    }

    public function customer_delete_after($observer) {
        if (!$this->_getConfigHelper()->getPayperleadConfig('enable')) {
            return;
        }
        $customer = $observer->getCustomer();
        $is_deduct_commission = $this->_getConfigHelper()->getPayperleadConfig('deduct_commission_account_disable');
        $lead = Mage::getModel('affiliatepluspayperlead/lead')->loadByAccountAndCustomer($customer->getEmail(), 4);

        if ($lead->getId() && ($lead->getStatus() == 1)) {

            if ($is_deduct_commission) {
                $lead->setStatus(3)
                        ->save()
                ;
//                                Changed By Adam 10/11/2014: Fix loi refund tien o store khac
                $account = Mage::getModel('affiliateplus/account')->setStoreid($customer->getStoreId())->load($lead->getAccountId());
                $commission = $this->_getConfigHelper()->getPayperleadConfig('signup_account_commission');
                $account->setBalance(floatval($account->getBalance()) - floatval($commission));
                $account->save();
            }
        }
    }

    public function newsletter_subscriber_delete_after($observer) {
        if (!$this->_getConfigHelper()->getPayperleadConfig('enable')) {
            return;
        }
        $subscriber = $observer->getSubscriber();
        $is_deduct_commission = $this->_getConfigHelper()->getPayperleadConfig('deduct_commission_customer_unsubscribes');
        $lead = Mage::getModel('affiliatepluspayperlead/lead')->loadByAccountAndCustomer($subscriber->getSubscriberEmail(), '5');

        $commission = $this->_getConfigHelper()->getPayperleadConfig('subscribe_newsletter_commission');
        if ($lead->getStatus() == 1) {
            if ($is_deduct_commission) {
                $lead->setData('status', 3);
                $lead->save();
                $account = Mage::getModel('affiliateplus/account')->load($lead->getAccountId());
                $account->setBalance(floatval($account->getBalance()) - floatval($commission));
                $account->save();
            }
        }
    }

    public function adminhtml_add_column_transaction_grid($observer) {
        $grid = $observer->getGrid();
        if ($grid->getColumn('type'))
            return $this;
        $grid->addColumn('type', array(
            'header' => Mage::helper('affiliateplus')->__('Type'),
            'width' => '80px',
            'index' => 'type',
            'type' => 'options',
            'options' => Mage::getSingleton('affiliateplus/system_config_source_actiontype')->getOptionList()
        ));
    }

    public function affiliateplus_get_action_types($observer) {
        $types = $observer->getTypes();
        $actions = $types->getActions();
        $actions[] = array('value' => '4', 'label' => Mage::helper('affiliateplus')->__('Sign up for an account'));
        $actions[] = array('value' => '5', 'label' => Mage::helper('affiliateplus')->__('Subscribe to newsletters'));
        $types->setActions($actions);
    }

}
