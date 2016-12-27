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
 * RewardPoints Observer Model
 *
 * @category    Magestore
 * @package     Magestore_RewardPoints
 * @author      Magestore Developer
 */
class Magestore_Custompromotions_Model_Observer
{
    public function customerRegisterSuccess($observer)
    {
        $customer_reg = $observer->getCustomer();
        /* Save customer phone number */
        $is_verified = Mage::getSingleton('core/session')->getVerify();
        $code = Mage::getSingleton('core/session')->getCodeActive();
        if($is_verified != null && $is_verified){
            $phone = Mage::getSingleton('core/session')->getPhoneActive();
            $customer_reg->setPhoneNumber($phone);
            $customer_reg->save();

            $mobile = Mage::helper('custompromotions/verify')->getVerifyByPhoneCode($phone, $code);
            if($mobile != null)
            {
                $mobile->setCustomerId($customer_reg->getId());
                $mobile->setUpdatedTime(now());
                $mobile->save();
            }
            Mage::getSingleton('core/session')->unsPhoneActive();
            Mage::getSingleton('core/session')->unsCodeActive();
            Mage::getSingleton('core/session')->unsVerify();
        }
        /* End save customer phone number */

        if (!Mage::helper('custompromotions')->isRunPromotions())
            return $this;

        $customer = Mage::getModel('customer/customer')->load($customer_reg->getId());
        if (!$customer->getId())
            return $this;

        Mage::helper('custompromotions')->addReward($customer);

        return $this;
    }

    public function salesOrderPlaceAfter($observer)
    {
        $order = $observer->getOrder();

        if (Mage::helper('custompromotions')->isRunPromotions()) {
            $customer_id = $order->getCustomerId();
            $affiliate = Mage::helper('custompromotions')->getAffiliateFromCustomer($customer_id);
            if ($affiliate != null && $affiliate->getId()) {
                Mage::helper('custompromotions')->addRewardAffiliate($affiliate, $customer_id, $order->getIncrementId());
            }
        }

        $quoteId = $order->getQuoteId();
        $quote = Mage::getModel('sales/quote')->load($quoteId);
        if($quote->getData('checkout_method') == Mage_Sales_Model_Quote::CHECKOUT_METHOD_REGISTER){
			$no_refers_me = Mage::getSingleton('core/session')->getWRMNo();
			$affiliateId = Mage::getSingleton('core/session')->getWRMId();
			$affiliateName = Mage::getSingleton('core/session')->getWRMName();
			$customer = Mage::getModel('customer/customer')->load($order->getCustomerId());
			
			if(isset($no_refers_me) && $no_refers_me == 0){
				if (!$affiliateId) {
					$affiliateId = Mage::getModel('affiliateplus/account')->getCollection()
						->addFieldToFilter('name', $affiliateName)
						->getFirstItem()->getAccountId();
				}
				
				if (isset($affiliateId) && $affiliateId) {
					$collectionTracking = Mage::getModel('affiliateplus/tracking')->getCollection()
						->addFieldToFilter('customer_id', $customer->getId())
						->getFirstItem();
					if (!$collectionTracking->getId()) {
						$collectionTracking = Mage::getModel('affiliateplus/tracking');
						$collectionTracking->setAccountId($affiliateId);
						$collectionTracking->setCustomerId($customer->getId());
						$collectionTracking->setCustomerEmail($customer->getEmail());
						$collectionTracking->setCreatedTime(now());
						try {
							$collectionTracking->save();
						} catch (Exception $e) {
							Mage::log($e->getMessage(), null, 'affiliate.log');
						}
					}
				}
			}
			
			$customer->setData('referred_affiliate_name',$affiliateName);
			$customer->setData('no_refers_me',$no_refers_me);
			$customer->save();
			Mage::getSingleton('core/session')->unsWRMNo();
			Mage::getSingleton('core/session')->unsWRMId();
			Mage::getSingleton('core/session')->unsWRMName();
		}
    }

    public function salesOrderSaveAfter($observer)
    {
        $order = $observer['order'];
        if ($order->getCustomerIsGuest() || !$order->getCustomerId()) {
            return $this;
        }

        // Add earning point for customer
        $truWallet_order_status = Mage::getStoreConfig('custompromotions/product/order_status', Mage::app()->getStore()->getId());

        if ($order->getState() == $truWallet_order_status || $order->getStatus() == $truWallet_order_status) {
            /* process truWallet Gift card Product */
            Mage::helper('custompromotions')->addTruWalletFromProduct($order);
        }
        
        /* Process partially shipment status when order has created shipment */
        $shipstation_enable = Mage::helper('custompromotions/configuration')->getShipStationEnable();
        if($shipstation_enable){
            $is_partially = Mage::getSingleton('core/session')->getOrderShipmentStatus();
            $total_qty_ordered = floor($order->getData('total_qty_ordered'));
            if(isset($is_partially) && $is_partially != null)
            {
                if($order->hasShipments()){
                    $shipmentCollection = $order->getShipmentsCollection();
                    $shipment_qty = 0;
                    foreach ($shipmentCollection as $ship) {
                        $shipment_qty += $ship->getTotalQty();
                    }

                    $new_status = Mage::helper('custompromotions/configuration')->getShipStationOrderStatus();
                    if($new_status == '')
                        $new_status = Mage_Sales_Model_Order::STATE_PROCESSING;

                    if($total_qty_ordered > $shipment_qty){
                        $order->setStatus($new_status);
                    } else if($total_qty_ordered == $shipment_qty){
                        $order->setStatus(Mage_Sales_Model_Order::STATE_PROCESSING);
                    }

                    Mage::getSingleton('core/session')->unsOrderShipmentStatus();
                    $order->save();
                }
            }
        }/* END Process partially shipment status when order has created shipment */

    }

    public function salesOrderShipmentSaveAfter($observer)
    {
        if (Mage::registry('salesOrderShipmentSaveAfterTriggered')) {
            return $this;
        }

        /* @var $shipment Mage_Sales_Model_Order_Shipment */
        $shipment = $observer->getEvent()->getShipment();
        $order = Mage::getModel('sales/order')->load($shipment->getOrderId());

        if ($shipment) {
            if($order->hasInvoices()){
                $total_qty_ordered = floor($order->getData('total_qty_ordered'));
                $invoiceCollection = $order->getInvoiceCollection();
                $invoice_qty = 0;
                foreach ($invoiceCollection as $inv) {
                    $invoice_qty += $inv->getTotalQty();
                }

                if($total_qty_ordered > $invoice_qty){
                    Mage::getSingleton('core/session')->setOrderShipmentStatus(1);
                } else {
                    Mage::getSingleton('core/session')->unsOrderShipmentStatus();
                }
            } else {
                Mage::getSingleton('core/session')->setOrderShipmentStatus(1);
            }
        }
        return $this;
    }

}
