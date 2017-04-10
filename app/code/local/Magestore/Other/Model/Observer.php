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

}
