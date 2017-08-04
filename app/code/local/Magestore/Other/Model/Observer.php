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

    public function customerLogin($observer)
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
