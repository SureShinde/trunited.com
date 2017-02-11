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
            || $order->getState() === Mage_Sales_Model_Order::STATE_CLOSED || $order->isCanceled() || $order->canUnhold()) {
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
                'title' => Mage::helper('truwallet')->__('Checkout by truWallet balance for order #<a href="'.Mage::getUrl('sales/order/view',array('order_id'=>$order->getId())).'">'. $order->getIncrementId().'</a>'),
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
    }

    public function orderSaveAfter($observer)
    {

        $order = $observer['order'];
        if ($order->getCustomerIsGuest() || !$order->getCustomerId()) {
            return $this;
        }

        // Add earning point for customer
        $truWallet_order_status = Mage::helper('truwallet')->getTruWalletOrderStatus();

        if ($order->getState() == $truWallet_order_status || $order->getStatus() == $truWallet_order_status) {
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
                'title' => Mage::helper('truwallet')->__('Cancel order #<a href="'.Mage::getUrl('sales/order/view',array('order_id'=>$order->getId())).'">'. $order->getIncrementId().'</a>'),
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

    public function creditmemoSaveAfter(Varien_Event_Observer $observer)
    {
        //declare variables
        $creditmemo = $observer->getEvent()->getCreditmemo();
        $order_id = $creditmemo->getOrderId();
        $order = Mage::getSingleton('sales/order');
        $order->load($order_id);
        $amount_credit = $creditmemo->getTruwalletDiscount();
        $customer_id = $creditmemo->getCustomerId();

        $check_memo = 0;
        $check_order = 0;

        $memo_items = $creditmemo->getAllItems();
        $order_items = $order->getAllItems();
        if(sizeof($memo_items) > 0)
        {
            foreach ($memo_items as $memo)
            {
                if (!$memo->getParentItemId()) {
                    $check_memo += $memo->getQty();
                }
            }

            foreach ($order_items as $_order)
            {
                if (!$_order->getParentItemId()) {
                    $check_order += $_order->getQtyOrdered();
                }
            }

            if($check_memo == $check_order)
            {
                $truWalletAccount = Mage::helper('truwallet/account')->updateCredit($customer_id, $amount_credit);
                $data = array(
                    'title' => Mage::helper('truwallet')->__('Refund order #<a href="'.Mage::getUrl('sales/order/view',array('order_id'=>$order->getId())).'">'. $order->getIncrementId().'</a>'),
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

        if(Mage::helper('core')->isModuleOutputEnabled('Magestore_Onestepcheckout'))
        {
            if(strcasecmp($module_name,'onestepcheckout') == 0 && strcasecmp($action_name,'index') == 0
                && strcasecmp($controller_name,'index') == 0 && strcasecmp($router_name,'onestepcheckout') == 0)
            {
                Mage::getSingleton('checkout/session')->setCancelCredit(false);
            }
        } else {
            if(strcasecmp($router_name,'checkout') == 0 && strcasecmp($action_name,'index') == 0
                && strcasecmp($controller_name,'onepage') == 0)
            {
                Mage::getSingleton('checkout/session')->setCancelCredit(false);
            }
        }

    }
}
