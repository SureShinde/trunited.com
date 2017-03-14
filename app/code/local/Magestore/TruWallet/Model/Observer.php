<?php

class Magestore_TruWallet_Model_Observer
{
    public function customerRegisterSuccess($observer)
    {
        $customer_reg = $observer->getCustomer();
        $customer = Mage::getModel('customer/customer')->load($customer_reg->getId());
        if (!$customer->getId())
            return $this;

        Mage::helper('truwallet/transaction')->checkCreditFromSharing($customer);

        return $this;
    }

    public function checkExpiryDate()
    {
        Mage::helper('truwallet/transaction')->checkExpiryDateTransaction();
    }

    public function paypal_prepare_line_items($observer)
    {
        $session = Mage::getSingleton('checkout/session');
        if (version_compare(Mage::getVersion(), '1.4.2', '>=')) {
            $paypalCart = $observer->getEvent()->getPaypalCart();
            if ($paypalCart) {
                $salesEntity = $paypalCart->getSalesEntity();
                $truWalletDiscount = $salesEntity->getTruwalletDiscount();
                if (!$truWalletDiscount)
                    $truWalletDiscount = $session->getBaseTruwalletAmount();
                if ($truWalletDiscount)
                    $paypalCart->updateTotal(Mage_Paypal_Model_Cart::TOTAL_DISCOUNT, abs((float)$truWalletDiscount), Mage::helper('truwallet')->__('truWallet Balance'));
            }
        } else {
            $salesEntity = $observer->getSalesEntity();
            $additional = $observer->getAdditional();
            if ($salesEntity && $additional) {
                $items = $additional->getItems();
                $items[] = new Varien_Object(array(
                    'name' => Mage::helper('truwallet')->__('truWallet Balance'),
                    'qty' => 1,
                    'amount' => -(abs((float)$salesEntity->getTruwalletDiscount())),
                ));
                $additional->setItems($items);
            }
        }
    }

    public function salesOrderLoadAfter($observer)
    {
        $order = $observer['order'];
        if ($order->getTruwalletDiscount() < 0.0001 || Mage::app()->getStore()->roundPrice($order->getGrandTotal()) > 0
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
        $amount = $session->getBaseTruwalletCreditAmount();
        if ($amount > 0) {
            $truWalletAccount = Mage::helper('truwallet/account')->updateCredit($customer_id, -$amount);
            $data = array(
                'title' => Mage::helper('truwallet')->__('Checkout by truWallet balance for order #<a href="' . Mage::getUrl('sales/order/view', array('order_id' => $order->getId())) . '">' . $order->getIncrementId() . '</a>'),
                'order_id' => $order->getEntityId(),
                'credit' => -$amount,
            );
            Mage::helper('truwallet/transaction')->createTransaction(
                $truWalletAccount,
                $data,
                Magestore_TruWallet_Model_Type::TYPE_TRANSACTION_CHECKOUT_ORDER,
                Magestore_TruWallet_Model_Status::STATUS_TRANSACTION_COMPLETED
            );
        }
        if ($session->getUseCustomerCredit()) {
            $session->setBaseTruwalletCreditAmount(null)
                ->setUseTruwalletCredit(false);
        } else {
            $session->setBaseTruwalletCreditAmount(null);
        }

        /* Calculate points from eCheck payment */
        $payment_reward = Mage::helper('truwallet')->getTruWalletPayment();
        if ($payment_reward != '' && strcasecmp($order->getPayment()->getMethod(), $payment_reward) == 0) {
            $reward_points = Mage::helper('truwallet/payment')->calculatePoints($order);

            if ($reward_points > 0) {
                $customer = Mage::getModel('customer/customer')->load($customer_id);

                $receiveObject = new Varien_Object();
                $receiveObject->setData('product_credit', 0);
                $receiveObject->setData('point_amount', $reward_points);
                $receiveObject->setData('customer_exist', true);
                $receiveObject->setData('order_id', $order->getIncrementId());

                Mage::helper('rewardpoints/action')
                    ->addTransaction(
                        'earning_payment', $customer, $receiveObject
                    );
            }

        }

        /* END Calculate points from eCheck payment */

        Mage::getSingleton('checkout/session')->clear();
        Mage::getSingleton('checkout/cart')->truncate()->save();
    }

    public function orderSaveAfter($observer)
    {

        $order = $observer['order'];
        if ($order->getCustomerIsGuest() || !$order->getCustomerId()) {
            return $this;
        }

        // Add earning point for customer
        $truWallet_order_status = Mage::helper('truwallet')->getTruWalletOrderStatus();
        $items = $order->getAllItems();
        $is_only_virtual = 0;
        foreach ($items as $item) {
            $product = Mage::getModel('catalog/product')->load($item->getProductId());
            if ($product->getTypeId() != 'virtual') {
                $is_only_virtual++;
            }
        }
        if ($order->getState() == $truWallet_order_status || $order->getStatus() == $truWallet_order_status
            || (strcasecmp($order->getStatus(), 'complete') == 0 && $is_only_virtual == 0)
        ) {
            Mage::helper('truwallet/transaction')->addTruWalletFromProduct($order);
        }

    }

    public function orderCancelAfter($observer)
    {
        $order = $observer->getOrder();
        $customer_id = $order->getCustomerId();
        $order_id = $order->getEntityId();
        $installer = Mage::getModel('core/resource_setup');
        $read = Mage::getSingleton('core/resource')->getConnection('core_read');

        if ((float)(string)$order->getBaseTruwalletDiscount() > 0) {
            $amount_credit = (float)(string)$order->getBaseTruwalletDiscount();
            $query = 'SELECT SUM(  `truwallet_discount` ) as `total_truwallet_invoiced` ,
                       SUM(  `base_truwallet_discount` ) as `total_base_truwallet_invoiced`
                       FROM  `' . $installer->getTable('sales/invoice') . '`
                       WHERE  `order_id` = ' . $order_id;
            $data = $read->fetchAll($query);

            $total_base_truwallet_invoiced = (float)$data[0]['total_base_truwallet_invoiced'];
            $amount_credit -= $total_base_truwallet_invoiced;

            $truWalletAccount = Mage::helper('truwallet/account')->updateCredit($customer_id, $amount_credit);
            $data = array(
                'title' => Mage::helper('truwallet')->__('Cancel order #<a href="' . Mage::getUrl('sales/order/view', array('order_id' => $order->getId())) . '">' . $order->getIncrementId() . '</a>'),
                'order_id' => $order->getEntityId(),
                'credit' => $amount_credit,
            );
            Mage::helper('truwallet/transaction')->createTransaction(
                $truWalletAccount,
                $data,
                Magestore_TruWallet_Model_Type::TYPE_TRANSACTION_CANCEL_ORDER,
                Magestore_TruWallet_Model_Status::STATUS_TRANSACTION_COMPLETED
            );
            return true;
        }
    }

    public function salesOrderCreditmemoRegisterBefore($observer)
    {
        $request = $observer['request'];
        if ($request->getActionName() == "updateQty") return $this;

        $creditmemo = $observer['creditmemo'];

        $input = $request->getParam('creditmemo');
        $order = $creditmemo->getOrder();

        // Refund point to customer (that he used to spend)
        if (isset($input['refund_truWallet']) && $input['refund_truWallet'] > 0) {
            $refundTruWallet = $input['refund_truWallet'];
            $maxPoint = $order->getData('truwallet_discount');
            $refundBalances = min($refundTruWallet, $maxPoint);
            $creditmemo->setTruwalletEarn(max($refundBalances, 0));
        }

        //Brian allow creditmemo when creditmemo total equal zero
        if (Mage::app()->getStore()->roundPrice($creditmemo->getGrandTotal()) <= 0) {
            $creditmemo->setAllowZeroGrandTotal(true);
        }

        return $this;
    }

    public function creditmemoSaveAfter(Varien_Event_Observer $observer)
    {
        //declare variables
        $creditmemo = $observer->getEvent()->getCreditmemo();
        $order_id = $creditmemo->getOrderId();
        $order = Mage::getSingleton('sales/order');
        $order->load($order_id);
        $amount_credit = $creditmemo->getTruwalletEarn();
        $customer_id = $creditmemo->getCustomerId();

        $memo_items = $creditmemo->getAllItems();
        if (sizeof($memo_items) > 0) {
            if ($creditmemo->getTruwalletEarn() > 0) {
                $truWalletAccount = Mage::helper('truwallet/account')->updateCredit($customer_id, $amount_credit);
                $data = array(
                    'title' => Mage::helper('truwallet')->__('Refund order #<a href="' . Mage::getUrl('sales/order/view', array('order_id' => $order->getId())) . '">' . $order->getIncrementId() . '</a>'),
                    'order_id' => $order->getEntityId(),
                    'credit' => $amount_credit,
                );
                Mage::helper('truwallet/transaction')->createTransaction(
                    $truWalletAccount,
                    $data,
                    Magestore_TruWallet_Model_Type::TYPE_TRANSACTION_REFUND_ORDER,
                    Magestore_TruWallet_Model_Status::STATUS_TRANSACTION_COMPLETED
                );
            }
        }
    }

    public function checkAutoApplyCredits(Varien_Event_Observer $observer)
    {
        $controller_name = Mage::app()->getRequest()->getControllerName();
        $action_name = Mage::app()->getRequest()->getActionName();
        $router_name = Mage::app()->getRequest()->getRouteName();
        $module_name = Mage::app()->getRequest()->getModuleName();

        if (Mage::helper('core')->isModuleOutputEnabled('Magestore_Onestepcheckout')) {
            if (strcasecmp($module_name, 'onestepcheckout') == 0 && strcasecmp($action_name, 'index') == 0
                && strcasecmp($controller_name, 'index') == 0 && strcasecmp($router_name, 'onestepcheckout') == 0
            ) {
                Mage::getSingleton('checkout/session')->setCancelCredit(false);
            }
        } else {
            if (strcasecmp($router_name, 'checkout') == 0 && strcasecmp($action_name, 'index') == 0
                && strcasecmp($controller_name, 'onepage') == 0
            ) {
                Mage::getSingleton('checkout/session')->setCancelCredit(false);
            }
        }

    }

    public function predispatchCheckoutCartAdd(Varien_Event_Observer $observer)
    {
        if ($observer->getEvent()->getControllerAction()->getFullActionName() == 'checkout_cart_add') {
            $productId = Mage::app()->getRequest()->getParam('product');
            $product = Mage::getModel('catalog/product')->load($productId);

            if (Mage::helper('custompromotions')->truWalletInCart() && strcasecmp($product->getSku(), Mage::helper('truwallet')->getTruWalletSku()) != 0) {
                Mage::app()->getResponse()->setRedirect(Mage::app()->getRequest()->getServer('HTTP_REFERER'));
                Mage::getSingleton('checkout/session')->addError(
                    Mage::helper('checkout')->__('Sorry, you can\'t add this product to cart while has truWallet Gift Card product')
                );
                $observer->getControllerAction()->setFlag("", Mage_Core_Controller_Varien_Action::FLAG_NO_DISPATCH, true);
            } else if (strcasecmp($product->getSku(), Mage::helper('truwallet')->getTruWalletSku()) == 0) {
                $oCheckout = Mage::getSingleton('checkout/session');
                $oQuote = $oCheckout->getQuote();
                $oCart = $oQuote->getAllItems();
                if (!empty($oCart)) {
                    foreach ($oCart as $oItem) {
                        if (strcasecmp($oItem->getProduct()->getSku(), Mage::helper('truwallet')->getTruWalletSku()) != 0) {
                            $oQuote->removeItem($oItem->getId())->save();
                        }
                    }
                }
            }
        }
    }

    public function paymentMethodIsActive(Varien_Event_Observer $observer)
    {
        $methodInstance = $observer->getMethodInstance();
        $result = $observer->getResult();
        if ($methodInstance->getCode() == Mage::helper('truwallet')->getTruWalletPayment()) {
            if(Mage::helper('custompromotions')->truWalletInCart())
                $result->isAvailable = true;
            else
                $result->isAvailable = false;
        }
    }
}
