<?php

class Magestore_Other_Model_Observer
{
    public function predispatchCheckoutCartAdd(Varien_Event_Observer $observer)
    {
        $session = Mage::getSingleton('checkout/session');

        if ($observer->getEvent()->getControllerAction()->getFullActionName() == 'checkout_cart_add') {
            $productId = Mage::app()->getRequest()->getParam('product');
            $product = Mage::getModel('catalog/product')->load($productId);

            if (Mage::helper('other')->enableDropShip() && Mage::helper('other')->isInDropShipList($product)) {
                $session->setBaseTruwalletCreditAmount(0);
                $session->setUseTruwalletCredit(false);
                $session->setData('delivery_type', null);
            }
        }
    }

    public function preDispatchLoginPost(Varien_Event_Observer $observer)
    {
        $session = Mage::getSingleton('customer/session');
        if ($observer->getEvent()->getControllerAction()->getFullActionName() == 'customer_account_loginPost') {
            $login = Mage::app()->getRequest()->getParam('login');
            if (!empty($login['username']) && !empty($login['password'])) {
                $collection = Mage::getModel('customer/customer')->getCollection()
                    ->addAttributeToSelect('entity_id')
                    ->addAttributeToSelect('email')
                    ->addAttributeToSelect('is_active')
                    ->addAttributeToFilter('email', array('like' => '%'.$login['username'].'%'))
                    ;
                if(sizeof($collection) > 0)
                {
                    $is_error_message = false;
                    foreach ($collection as $customer) {
                        if(strcasecmp($customer->getEmail(), $login['username']) == 0 && $customer->getData('is_active') == Magestore_Other_Model_Status::STATUS_CUSTOMER_INACTIVE)
                        {
                            $is_error_message = true;
                            break;
                        }
                    }
                    if($is_error_message){
                        Mage::getSingleton('core/session')->addError(
                            Mage::helper('other')->getErrorMessageCustomer()
                        );
                        $session->setId(null)
                            ->getCookie()->delete('customer');
                        $session->logout();
                        Mage::app()->getFrontController()->getResponse()->setRedirect(Mage::getUrl('customer/account/login'));
                        Mage::app()->getResponse()->sendResponse();
                    }
                }
            } else {
                $session->addError(Mage::helper('other')->__('Login and password are required.'));
            }
        }
    }

    public function customerLogin(Varien_Event_Observer $observer)
    {
        $customer = $observer->getEvent()->getCustomer();
        $session = Mage::getSingleton('customer/session');
        if($customer->getData('is_active') == Magestore_Other_Model_Status::STATUS_CUSTOMER_INACTIVE){
            Mage::getSingleton('core/session')->addError(
                Mage::helper('other')->getErrorMessageCustomer()
            );
            $session->setId(null)
                ->getCookie()->delete('customer');
            $session->logout();
            Mage::app()->getFrontController()->getResponse()->setRedirect(Mage::getUrl('customer/account/login'));
            Mage::app()->getResponse()->sendResponse();
            return;
        }
    }

}
