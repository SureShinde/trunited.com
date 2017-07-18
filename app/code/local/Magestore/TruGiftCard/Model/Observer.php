<?php

class Magestore_TruGiftCard_Model_Observer
{
    public function customerRegisterSuccess($observer)
    {
        $customer_reg = $observer->getCustomer();
        $customer = Mage::getModel('customer/customer')->load($customer_reg->getId());
        if (!$customer->getId())
            return $this;

        Mage::helper('trugiftcard/transaction')->checkCreditFromSharing($customer);

        return $this;
    }

    public function checkExpiryDate()
    {
        Mage::helper('trugiftcard/transaction')->checkExpiryDateTransaction();
    }

    public function orderSaveAfter($observer)
    {

        $order = $observer['order'];
        if ($order->getCustomerIsGuest() || !$order->getCustomerId()) {
            return $this;
        }

        // Add earning point for customer
        $truGiftCard_order_status = Mage::helper('trugiftcard')->getTruGiftCardOrderStatus();
        $items = $order->getAllItems();
        $is_only_virtual = 0;
        foreach ($items as $item) {
            $product = Mage::getModel('catalog/product')->load($item->getProductId());
            if ($product->getTypeId() != 'virtual') {
                $is_only_virtual++;
            }
        }
        if ($order->getState() == $truGiftCard_order_status || $order->getStatus() == $truGiftCard_order_status
            || (strcasecmp($order->getStatus(), 'complete') == 0 && $is_only_virtual == 0)
        ) {
            Mage::helper('trugiftcard/transaction')->addTruGiftCardFromProduct($order);
        }

    }

    public function predispatchCheckoutCartAdd(Varien_Event_Observer $observer)
    {
        $session = Mage::getSingleton('checkout/session');
        if ($observer->getEvent()->getControllerAction()->getFullActionName() == 'checkout_cart_add') {
            $productId = Mage::app()->getRequest()->getParam('product');
            $product = Mage::getModel('catalog/product')->load($productId);

            if (Mage::helper('custompromotions')->truGiftCardInCart() && strcasecmp($product->getSku(), Mage::helper('trugiftcard')->getTruGiftCardSku()) != 0) {
                Mage::app()->getResponse()->setRedirect(Mage::app()->getRequest()->getServer('HTTP_REFERER'));
                $session->addError(
                    Mage::helper('checkout')->__('Sorry, you can\'t add this product to cart while has Trunited Gift Card product')
                );
                $observer->getControllerAction()->setFlag("", Mage_Core_Controller_Varien_Action::FLAG_NO_DISPATCH, true);
            } else if (strcasecmp($product->getSku(), Mage::helper('trugiftcard')->getTruGiftCardSku()) == 0) {
                $oQuote = $session->getQuote();
                $oCart = $oQuote->getAllItems();
                if (!empty($oCart)) {
                    foreach ($oCart as $oItem) {
                        if (strcasecmp($oItem->getProduct()->getSku(), Mage::helper('trugiftcard')->getTruGiftCardSku()) != 0) {
                            $oQuote->removeItem($oItem->getId())->save();
                        }
                    }

                    $session->setBaseTrugiftcardCreditAmount(0);
                    $session->setUseTrugiftcardCredit(false);
                }
            }
        }
    }

    public function paymentMethodIsActive(Varien_Event_Observer $observer)
    {
        $methodInstance = $observer->getMethodInstance();
        $result = $observer->getResult();
        if ($methodInstance->getCode() == Mage::helper('trugiftcard')->getTruGiftCardPayment()) {
            if(Mage::helper('custompromotions')->truGiftCardInCart())
                $result->isAvailable = true;
            else
                $result->isAvailable = false;
        }
    }

    public function orderCancelAfter($observer)
    {
        $order = $observer->getOrder();
        $order_id = $order->getEntityId();

        $transactions = Mage::getModel('trugiftcard/transaction')->getCollection()
            ->addFieldToFilter('order_id', $order_id)
            ->addFieldToFilter('status', Magestore_TruGiftCard_Model_Status::STATUS_TRANSACTION_COMPLETED)
            ->setOrder('transaction_id', 'desc')
            ;

        if(sizeof($transactions) > 0)
        {
            foreach ($transactions as $transaction) {
                $amount_credit = $transaction->getData('changed_credit');
                Mage::helper('trugiftcard/account')->updateCredit($transaction->getCustomerId(), -$amount_credit);
                $transaction->setData('status', Magestore_TruWallet_Model_Status::STATUS_TRANSACTION_CANCELLED);
                $transaction->setData('updated_time', now());
                $transaction->save();
            }
        }
    }

    public function creditmemoSaveAfter(Varien_Event_Observer $observer)
    {
        $creditmemo = $observer->getEvent()->getCreditmemo();
        $order_id = $creditmemo->getOrderId();

        $transactions = Mage::getModel('trugiftcard/transaction')->getCollection()
            ->addFieldToFilter('order_id', $order_id)
            ->addFieldToFilter('status', Magestore_TruGiftCard_Model_Status::STATUS_TRANSACTION_COMPLETED)
            ->setOrder('transaction_id', 'desc')
        ;

        if(sizeof($transactions) > 0)
        {
            foreach ($transactions as $transaction) {
                $amount_credit = $transaction->getData('changed_credit');
                Mage::helper('trugiftcard/account')->updateCredit($transaction->getCustomerId(), -$amount_credit);
                $transaction->setData('status', Magestore_TruWallet_Model_Status::STATUS_TRANSACTION_CANCELLED);
                $transaction->setData('updated_time', now());
                $transaction->save();
            }
        }
    }
}
