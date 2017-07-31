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
		if(date('i', time()) == 00)
		{
			Mage::log('Running API at '.date('Y-m-d H:i:s', time()), null, 'check_trugiftcard.log');
			Mage::helper('trugiftcard/transaction')->checkExpiryDateTransaction();
		}
        
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
        $customer_id = $order->getCustomerId();
        $installer = Mage::getModel('core/resource_setup');
        $read = Mage::getSingleton('core/resource')->getConnection('core_read');

        $transactions = Mage::getModel('trugiftcard/transaction')->getCollection()
            ->addFieldToFilter('order_id', $order_id)
            ->addFieldToFilter('status', Magestore_TruGiftCard_Model_Status::STATUS_TRANSACTION_COMPLETED)
            ->addFieldToFilter('action_type', array('in' => array(
                Magestore_TruGiftCard_Model_Type::TYPE_TRANSACTION_RECEIVE_REWARD_FROM_PROMOTION,
                Magestore_TruGiftCard_Model_Type::TYPE_TRANSACTION_RECEIVE_REWARD_FROM_REFERRED_PROMOTION,
            )))
            ->setOrder('transaction_id', 'desc')
            ;

        if(sizeof($transactions) > 0)
        {
            foreach ($transactions as $transaction) {
                $amount_credit = $transaction->getData('changed_credit');
                Mage::helper('trugiftcard/account')->updateCredit($transaction->getCustomerId(), -$amount_credit);
                $transaction->setData('status', Magestore_TruGiftCard_Model_Status::STATUS_TRANSACTION_CANCELLED);
                $transaction->setData('updated_time', now());
                $transaction->save();
            }
        }

        if ((float)(string)$order->getBaseTrugiftcardDiscount() > 0) {
            $amount_credit = (float)(string)$order->getBaseTrugiftcardDiscount();
            $query = 'SELECT SUM(  `trugiftcard_discount` ) as `total_trugiftcard_invoiced` ,
                       SUM(  `base_trugiftcard_discount` ) as `total_base_trugiftcard_invoiced`
                       FROM  `' . $installer->getTable('sales/invoice') . '`
                       WHERE  `order_id` = ' . $order_id;
            $data = $read->fetchAll($query);
            $total_base_trugiftcard_invoiced = (float)$data[0]['total_base_trugiftcard_invoiced'];
            $amount_credit -= $total_base_trugiftcard_invoiced;

            $truWalletAccount = Mage::helper('trugiftcard/account')->updateCredit($customer_id, $amount_credit);
            $data = array(
                'title' => Mage::helper('trugiftcard')->__('Cancel order #<a href="' . Mage::getUrl('sales/order/view', array('order_id' => $order->getId())) . '">' . $order->getIncrementId() . '</a>'),
                'order_id' => $order->getEntityId(),
                'credit' => $amount_credit,
            );
            Mage::helper('trugiftcard/transaction')->createTransaction(
                $truWalletAccount,
                $data,
                Magestore_TruGiftCard_Model_Type::TYPE_TRANSACTION_CANCEL_ORDER,
                Magestore_TruGiftCard_Model_Status::STATUS_TRANSACTION_COMPLETED
            );
            return true;
        }
    }

    public function creditmemoSaveAfter(Varien_Event_Observer $observer)
    {
        $creditmemo = $observer->getEvent()->getCreditmemo();
        $order_id = $creditmemo->getOrderId();

        $transactions = Mage::getModel('trugiftcard/transaction')->getCollection()
            ->addFieldToFilter('order_id', $order_id)
            ->addFieldToFilter('status', Magestore_TruGiftCard_Model_Status::STATUS_TRANSACTION_COMPLETED)
            ->addFieldToFilter('action_type', array('in' => array(
                Magestore_TruGiftCard_Model_Type::TYPE_TRANSACTION_RECEIVE_REWARD_FROM_PROMOTION,
                Magestore_TruGiftCard_Model_Type::TYPE_TRANSACTION_RECEIVE_REWARD_FROM_REFERRED_PROMOTION,
            )))
            ->setOrder('transaction_id', 'desc')
        ;

        if(sizeof($transactions) > 0)
        {
            foreach ($transactions as $transaction) {
                $amount_credit = $transaction->getData('changed_credit');
                Mage::helper('trugiftcard/account')->updateCredit($transaction->getCustomerId(), -$amount_credit);
                $transaction->setData('status', Magestore_TruGiftCard_Model_Status::STATUS_TRANSACTION_CANCELLED);
                $transaction->setData('updated_time', now());
                $transaction->save();
            }
        }

        $order = Mage::getSingleton('sales/order');
        $order->load($order_id);
        $amount_credit = $creditmemo->getTrugiftcardEarn();
        $customer_id = $creditmemo->getCustomerId();

        $memo_items = $creditmemo->getAllItems();
        if (sizeof($memo_items) > 0) {
            if ($creditmemo->getTrugiftcardEarn() > 0) {
                $truWalletAccount = Mage::helper('trugiftcard/account')->updateCredit($customer_id, $amount_credit);
                $data = array(
                    'title' => Mage::helper('trugiftcard')->__('Refund order #<a href="' . Mage::getUrl('sales/order/view', array('order_id' => $order->getId())) . '">' . $order->getIncrementId() . '</a>'),
                    'order_id' => $order->getEntityId(),
                    'credit' => $amount_credit,
                );
                Mage::helper('trugiftcard/transaction')->createTransaction(
                    $truWalletAccount,
                    $data,
                    Magestore_TruGiftCard_Model_Type::TYPE_TRANSACTION_REFUND_ORDER,
                    Magestore_TruGiftCard_Model_Status::STATUS_TRANSACTION_COMPLETED
                );
            }
        }
    }

    public function salesOrderLoadAfter($observer)
    {
        $order = $observer['order'];
        if ($order->getTrugiftcardDiscount() < 0.0001 || Mage::app()->getStore()->roundPrice($order->getGrandTotal()) > 0
            || $order->getState() === Mage_Sales_Model_Order::STATE_CLOSED || $order->isCanceled() || $order->canUnhold()
        ) {
            return $this;
        }
        foreach ($order->getAllItems() as $item) {
            if ($item->getParentItemId())
                continue;
            if ($item->getHasChildren() && $item->isChildrenCalculated()) {
                foreach ($item->getChildren() as $child) {
                    if (($child->getQtyInvoiced() - $child->getQtyRefunded() - $child->getQtyCanceled()) > 0) {
                        $order->setForcedCanCreditmemo(true);
                        return $this;
                    }
                }
            } elseif ($item->getProduct()) {
                if (($item->getQtyInvoiced() - $item->getQtyRefunded() - $item->getQtyCanceled()) > 0) {
                    $order->setForcedCanCreditmemo(true);
                    return $this;
                }
            }
        }
    }

    public function orderPlaceAfter($observer)
    {
        $order = $observer['order'];
        $customer_id = Mage::getSingleton('customer/session')->getCustomerId();
        if (!$customer_id) {
            $customer_id = $order->getCustomer()->getId();
        }
        $session = Mage::getSingleton('checkout/session');
        $amount = $session->getBaseTrugiftcardCreditAmount();
        if ($amount > 0) {
            $truWalletAccount = Mage::helper('trugiftcard/account')->updateCredit($customer_id, -$amount);
            $data = array(
                'title' => Mage::helper('trugiftcard')->__('Checkout by Trunited Gift Card balance for order #<a href="' . Mage::getUrl('sales/order/view', array('order_id' => $order->getId())) . '">' . $order->getIncrementId() . '</a>'),
                'order_id' => $order->getEntityId(),
                'credit' => -$amount,
            );
            Mage::helper('trugiftcard/transaction')->createTransaction(
                $truWalletAccount,
                $data,
                Magestore_TruGiftCard_Model_Type::TYPE_TRANSACTION_CHECKOUT_ORDER,
                Magestore_TruGiftCard_Model_Status::STATUS_TRANSACTION_COMPLETED
            );
        }
        if ($session->getUseCustomerCredit()) {
            $session->setBaseTrugiftcardCreditAmount(null)
                ->setUseTrugiftcardCredit(false);
        } else {
            $session->setBaseTrugiftcardCreditAmount(null);
        }

        Mage::getSingleton('checkout/session')->clear();
        Mage::getSingleton('checkout/cart')->truncate()->save();
    }

    public function salesOrderCreditmemoRegisterBefore($observer)
    {
        $request = $observer['request'];
        if ($request->getActionName() == "updateQty") return $this;

        $creditmemo = $observer['creditmemo'];

        $input = $request->getParam('creditmemo');
        $order = $creditmemo->getOrder();

        // Refund point to customer (that he used to spend)
        if (isset($input['refund_truGiftCard']) && $input['refund_truGiftCard'] > 0) {
            $refundTruGiftCard = $input['refund_truGiftCard'];
            $maxPoint = $order->getData('trugiftcard_discount');
            $refundBalances = min($refundTruGiftCard, $maxPoint);
            $creditmemo->setTrugiftcardEarn(max($refundBalances, 0));
        }

        //Brian allow creditmemo when creditmemo total equal zero
        if (Mage::app()->getStore()->roundPrice($creditmemo->getGrandTotal()) <= 0) {
            $creditmemo->setAllowZeroGrandTotal(true);
        }

        return $this;
    }
}
