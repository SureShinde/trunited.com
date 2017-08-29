<?php
/**
*
* Do not edit or add to this file if you wish to upgrade the module to newer
* versions in the future. If you wish to customize the module for your
* needs please contact us to https://www.milople.com/magento-extensions/contacts/
*
* @category     Ecommerce
* @package      Indies_Recurringandrentalpayments
* @copyright    Copyright (c) 2015 Milople Technologies Pvt. Ltd. All Rights Reserved.
* @url          https://www.milople.com/magento-extensions/recurring-and-subscription-payments.html
*
* Milople was known as Indies Services earlier.
*
**/

class Indies_Recurringandrentalpayments_CustomerController extends Mage_Core_Controller_Front_Action
{

    /*
     *
     * check that current subscription belong current customer
     *
     */

    public function _check()
    {
        if ($id = $this->getRequest()->getParam('id')) {
            $model = Mage::getModel('recurringandrentalpayments/subscription')->load($id);
            if (Mage::getModel('customer/session')->getId() != $model->getCustomerId()) {
                Mage::getSingleton('core/session')->addError(Mage::helper('recurringandrentalpayments')->__('This subscription isn\'t belong this customer!'));
                return false;
            }
            else
                return true;
        }
    }

    public function indexAction()
    {

        if (!Mage::getSingleton('customer/session')->getCustomerId()) {
            Mage::getSingleton('customer/session')->authenticate($this);
            return;
        }

        $this->loadLayout();

        $this->_initLayoutMessages('customer/session');
        $this->_initLayoutMessages('catalog/session');

        if ($navigationBlock = $this->getLayout()->getBlock('customer_account_navigation')) {
            $navigationBlock->setActive('recurringandrentalpayments/customer');
        }
        if ($block = $this->getLayout()->getBlock('recurringandrentalpayments_subscription_list')) {
            $block->setRefererUrl($this->_getRefererUrl());
        }

        $this->renderLayout();
    }

    public function historyAction()
    {
        if (!Mage::getSingleton('customer/session')->getCustomerId()) {
            Mage::getSingleton('customer/session')->authenticate($this);
            return;
        }

        $this->loadLayout();
        $this->_initLayoutMessages('customer/session');
        $this->_initLayoutMessages('catalog/session');

        $Subscription = Mage::getModel('recurringandrentalpayments/subscription')->load($this->getRequest()->getParam('id'));
        if ($block = $this->getLayout()->getBlock('recurringandrentalpayments_subscription_payments_history')) {
            $block
                    ->setRefererUrl($this->_getRefererUrl())
                    ->setSubscription($Subscription);
        }
        if ($block = $this->getLayout()->getBlock('recurringandrentalpayments_subscription_payments_pending')) {
            $block
                    ->setRefererUrl($this->_getRefererUrl())
                    ->setSubscription($Subscription);
        }

        if ($navigationBlock = $this->getLayout()->getBlock('customer_account_navigation')) {
            $navigationBlock->setActive('recurringandrentalpayments/customer');
        }

        $this->renderLayout();
    }

    public function viewAction()
    {
        if (!Mage::getSingleton('customer/session')->getCustomerId()) {
            Mage::getSingleton('customer/session')->authenticate($this);
            return;
        }

        $this->loadLayout();
        $this->_initLayoutMessages('customer/session');
        $this->_initLayoutMessages('catalog/session');
        ;
        if ($block = $this->getLayout()->getBlock('recurringandrentalpayments_subscription_summary')) {
            $block
                    ->setRefererUrl($this->_getRefererUrl())
                    ->setSubscription(Mage::getModel('recurringandrentalpayments/subscription')->load($this->getRequest()->getParam('id')));
        }

        if ($navigationBlock = $this->getLayout()->getBlock('customer_account_navigation')) {
            $navigationBlock->setActive('recurringandrentalpayments/customer');
        }

        $this->renderLayout();
    }

    public function changeAction()
    {
        if (!Mage::getSingleton('customer/session')->getCustomerId()) {
            Mage::getSingleton('customer/session')->authenticate($this);
            return;
        }
        $subscription = Mage::getModel('recurringandrentalpayments/subscription')->load($this->getRequest()->getParam('id'));
        if (
            is_null($subscription->getCustomerId()) ||
            Mage::getSingleton('customer/session')->getCustomerId() != $subscription->getCustomerId()
        ) {
            $this->_redirect('subscriptions/customer');
            return;
        }
        
        $this->loadLayout();
        $this->_initLayoutMessages('customer/session');
        $this->_initLayoutMessages('catalog/session');

        if ($block = $this->getLayout()->getBlock('recurringandrentalpayments_subscription_edit')) {
            $block
                    ->setRefererUrl($this->_getRefererUrl())
                    ->setSubscription(Mage::getModel('recurringandrentalpayments/subscription')->load($this->getRequest()->getParam('id')))
                    ->setSection($this->getRequest()->getParam('section'));
        }
        $this->renderLayout();
    }

    /**
     * Saves address
     * @return
     */
    public function saveAction()
    {
	    try {
            $model = Mage::getModel('recurringandrentalpayments/order_section')
                    ->setSubscription(Mage::getSingleton('recurringandrentalpayments/subscription')->load($this->getRequest()->getParam('id')))
                    ->setType($this->getRequest()->getParam('section'))
                    ->setDataToSave($this->getRequest()->getPost())
                    ->save();
            Mage::getSingleton('customer/session')->addSuccess(Mage::helper('recurringandrentalpayments')->__('Subscription has been successfully saved'));

            $this->_redirect('recurringandrentalpayments/customer/view', array('id' => $this->getRequest()->getParam('id')));
        } catch (Exception $e) {
            echo $e->getMessage();
            die();
            Mage::getSingleton('customer/session')->addError($e->getMessage());
            $this->_redirectReferer();
        }
    }

    /**
     * Customer cancels subscription
     * @return
     */
    public function cancelAction()
    {
        try {
            $model = Mage::getModel('recurringandrentalpayments/subscription')->load($this->getRequest()->getParam('id'));
            if ($model->getId() && Mage::getModel('customer/session')->getId() == $model->getCustomerId()) {
                $model->cancel()->save();
				
				if(Mage::getStoreConfig(Indies_Recurringandrentalpayments_Helper_Config::XML_PATH_ORDER_STATUS_CANCLE)){
				$alert= Mage::getModel('recurringandrentalpayments/alert_event');
				$alert->send($model,Mage::getStoreConfig(Indies_Recurringandrentalpayments_Helper_Config::XML_PATH_ORDER_STATUS_CANCLE_TEMPLATE),1);
				}
            } else {
                throw new Indies_Recurringandrentalpayments_Exception("Subscription not found!");
            }
            Mage::getSingleton('core/session')->addSuccess('You have successfully canceled subscription');
            $this->_redirectReferer();
        } catch (Exception $e) {
            echo $e->getMessage();
            die();
            Mage::getSingleton('core/session')->addError($e->getMessage());
            $this->_redirectReferer();
        }
    }

    /**
     * Customer suspends subscription
     * @return
     */
    public function suspendAction()
    {
        if (!$this->_check())
            return $this->_redirect('/');

        try {
            $model = Mage::getModel('recurringandrentalpayments/subscription')->load($this->getRequest()->getParam('id'));
            if ($model->getId()) {
                $model->setStatus(Indies_Recurringandrentalpayments_Model_Subscription::STATUS_SUSPENDED_BY_CUSTOMER)->save();
            	if(Mage::getStoreConfig(Indies_Recurringandrentalpayments_Helper_Config::XML_PATH_ORDER_STATUS_SUSPEND)){
				$alert= Mage::getModel('recurringandrentalpayments/alert_event');
				$alert->send($model,Mage::getStoreConfig(Indies_Recurringandrentalpayments_Helper_Config::XML_PATH_ORDER_STATUS_SUSPEND_TEMPLATE),
			 1,
			Mage::getStoreConfig(Indies_Recurringandrentalpayments_Helper_Config::XML_PATH_ORDER_STATUS_CHANGE_SENDER),
			Mage::getStoreConfig(Indies_Recurringandrentalpayments_Helper_Config::XML_PATH_ORDER_STATUS_CHANGE_CC_TO)
							);
				}
			} else {
                throw new Indies_Recurringandrentalpayments_Exception("Subscription not found!");
            }
            Mage::getSingleton('core/session')->addSuccess('You have successfully suspended subscription');
            $this->_redirectReferer();
        } catch (Exception $e) {
            echo $e->getMessage();
            die();
            Mage::getSingleton('core/session')->addError($e->getMessage());
            $this->_redirectReferer();
        }
    }

    /**
     * Customer re-activates subscription
     * @return
     */
    public function activateAction()
    {
        if (!$this->_check())
            return $this->_redirect('/');

        try {
            $model = Mage::getModel('recurringandrentalpayments/subscription')->load($this->getRequest()->getParam('id'));
            if ($model->getId() && ($model->getStatus() == Indies_Recurringandrentalpayments_Model_Subscription::STATUS_SUSPENDED_BY_CUSTOMER)) {
                $model->setStatus(Indies_Recurringandrentalpayments_Model_Subscription::STATUS_ENABLED)->save();
				if(Mage::getStoreConfig(Indies_Recurringandrentalpayments_Helper_Config::XML_PATH_ORDER_STATUS_ACTIVE)){
				$alert= Mage::getModel('recurringandrentalpayments/alert_event');
				$alert->send($model,Mage::getStoreConfig(Indies_Recurringandrentalpayments_Helper_Config::XML_PATH_ORDER_STATUS_ACTIVE_TEMPLATE),
			 1,
			Mage::getStoreConfig(Indies_Recurringandrentalpayments_Helper_Config::XML_PATH_ORDER_STATUS_CHANGE_SENDER),
			Mage::getStoreConfig(Indies_Recurringandrentalpayments_Helper_Config::XML_PATH_ORDER_STATUS_CHANGE_CC_TO)
							);
				}

            } else {
                throw new Indies_Recurringandrentalpayments_Exception("Activation of subscription is no allowed");
            }
            Mage::getSingleton('customer/session')->addSuccess('You have successfully activated subscription');
            $this->_redirectReferer();
        } catch (Exception $e) {
            echo $e->getMessage();
            die();
            Mage::getSingleton('customer/session')->addError($e->getMessage());
            $this->_redirectReferer();
        }
    }

    public function prolongAction()
    {
        if (!$this->_check())
            return $this->_redirect('/');

        $model = Mage::getModel('recurringandrentalpayments/subscription')->load($this->getRequest()->getParam('id'));
      
	    $orderId = $model->getOrder()->getId();
        $order = $model->getOrder();

        $cart = Mage::getSingleton('checkout/cart');
        $cartTruncated = false;
        /* @var $cart Mage_Checkout_Model_Cart */

        $items = $order->getItemsCollection();
        foreach ($items as $item) {
            try {
                $flag = false;
                foreach ($model->getItems() as $subscriptionItem)
                    if ($item->getId() == $subscriptionItem->getOrderItem()->getId()) {
                        $flag = true;
                        break;
                    }
                if (!$flag) continue;

                $iBR = $item->getProductOptionByCode('info_buyRequest');

                $newDate = new Zend_Date($iBR['indies_recurringandrentalpayments_subscription_start'], Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT));

                if ($newDate->compare(new Zend_Date) == -1 || ($model->getStatus() == Indies_Recurringandrentalpayments_Model_Subscription::STATUS_CANCELED)) {
                    $newDate = new Zend_Date;
                }
                $iBR['indies_recurringandrentalpayments_subscription_start'] = $newDate->toString(Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT));

				$iBR['prolong_for_id'] = $model->getId();
				$item->setProductOptions(array('info_buyRequest' => $iBR));

                $cart->addOrderItem($item);

            } catch (Mage_Core_Exception $e) {
                if (Mage::getSingleton('checkout/session')->getUseNotice(true)) {
                    Mage::getSingleton('checkout/session')->addNotice($e->getMessage());
                }
                else {
                    Mage::getSingleton('checkout/session')->addError($e->getMessage());
                }
                $this->_redirectReferer();
            } catch (Exception $e) {
             		Mage::getSingleton('checkout/session')->addException($e,Mage::helper('checkout')->__('Can not add item to shopping cart'));
                $this->_redirect('checkout/cart');
            }
        }

        $cart->save();
        $this->_redirect('checkout/cart');

    }

    public function reorderAction()
    {
        $model = Mage::getModel('recurringandrentalpayments/subscription')->load($this->getRequest()->getParam('id'));
        $orderId = $model->getOrder()->getId();
        $order = $model->getOrder();

        $cart = Mage::getSingleton('checkout/cart');
        $cartTruncated = false;
        /* @var $cart Mage_Checkout_Model_Cart */

        $items = $order->getItemsCollection();
        foreach ($items as $item) {
            try {
                $iBR = $item->getProductOptionByCode('info_buyRequest');

                if ($date = $model->getLastPaidDate()) {

                } else {
                    $date = $model->getDateStart();
                }

                $newDate = $model->getNextSubscriptionEventDate($date);

                if ($newDate->compare(new Zend_Date) == -1 || ($model->getStatus() == Indies_Recurringandrentalpayments_Model_Subscription::STATUS_CANCELED)) {
                    $newDate = new Zend_Date;
                }
                $iBR['indies_recurringandrentalpayments_subscription_start'] = $newDate->toString(Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT));
                $iBR['indies_recurringandrentalpayments_order_id'] = $orderId;
                $item->setProductOptions(array('info_buyRequest' => $iBR));

                $cart->addOrderItem($item);
                $model->setStatus(Indies_Recurringandrentalpayments_Model_Subscription::STATUS_CANCELED)->save();
            } catch (Mage_Core_Exception $e) {
                if (Mage::getSingleton('checkout/session')->getUseNotice(true)) {
                    Mage::getSingleton('checkout/session')->addNotice($e->getMessage());
                }
                else {
                    Mage::getSingleton('checkout/session')->addError($e->getMessage());
                }
                $this->_redirectReferer();
            } catch (Exception $e) {
                Mage::getSingleton('checkout/session')->addException($e,
                                              			Mage::helper('checkout')->__('Can not add item to shopping cart')
                );
                $this->_redirect('checkout/cart');
            }
        }

        $cart->save();
        $this->_redirect('checkout/cart');

    }
}