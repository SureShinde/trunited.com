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
        if (!Mage::helper('custompromotions')->isRunPromotions())
            return $this;

        $customer_reg = $observer->getCustomer();
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
    }
}
