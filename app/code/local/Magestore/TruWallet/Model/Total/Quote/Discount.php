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
 * @package     Magestore_Storecredit
 * @module      Storecredit
 * @author      Magestore Developer
 *
 * @copyright   Copyright (c) 2016 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 *
 */

/**
 * TruWallet Model
 *
 * @category    Magestore
 * @package     Magestore_TruWallet
 * @author      Magestore Developer
 */
class Magestore_TruWallet_Model_Total_Quote_Discount extends Mage_Sales_Model_Quote_Address_Total_Abstract
{

    /**
     * Magestore_TruWallet_Model_Total_Quote_Discount constructor.
     */
    public function __construct()
    {
        $this->setCode('truwallet_after_tax');
    }

    public function checkIsAdmin()
    {
        $admin_session = Mage::getSingleton('adminhtml/session');
        $customer_id = $admin_session->getOrderCustomerId();
        if (isset($customer_id) && $customer_id > 0 && Mage::app()->getStore()->isAdmin())
            return true;
        else
            return false;
    }

    public function isAppliedTGCToOrder($customer_id = null)
    {
        if($customer_id != null)
            $customerId = $customer_id;
        else {
            $admin_session = Mage::getSingleton('adminhtml/session');
            $customerId = $admin_session->getOrderCustomerId();
        }

        if (isset($customerId) && $customerId > 0) {

            $truBox = Mage::getModel('trubox/trubox')->getCollection()
                ->addFieldToFilter('status', 'open')
                ->addFieldToFilter('customer_id', $customerId)
                ->getFirstItem();

            if ($truBox->getId()) {
                return $truBox->getData('use_trugiftcard');
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    /**
     * @param Mage_Sales_Model_Quote_Address $address
     * @return $this
     */
    public function collect(Mage_Sales_Model_Quote_Address $address)
    {
        parent::collect($address);
        $quote = $address->getQuote();
        $items = $address->getAllItems();
        $session = Mage::getSingleton('checkout/session');
        $admin_session = Mage::getSingleton('adminhtml/session');

        if (!count($items))
            return $this;

        if (Mage::getStoreConfig('truwallet/spend/tax', $quote->getStoreId()) == '0') {
            return $this;
        }
        if (!$quote->isVirtual() && $address->getAddressType() == 'billing') {
            return $this;
        }
        if ($quote->isVirtual() && $address->getAddressType() == 'shipping') {
            return $this;
        }

        $no_truWallet = false;
        if(Mage::helper('custompromotions')->truGiftCardInCart() && !Mage::helper('trugiftcard')->getSpendConfig('use_truwallet')){
            $session->setBaseTruwalletCreditAmount(null);
            $no_truWallet = true;
        }

        $helper = Mage::helper('truwallet');

        if ($this->checkIsAdmin()) {
            $account = Mage::helper('truwallet/account')->loadByCustomerId($admin_session->getOrderCustomerId());
            $account_giftCard = Mage::helper('trugiftcard/account')->loadByCustomerId($admin_session->getOrderCustomerId());
            $creditAmountEntered = $account->getTruwalletCredit();
            $creditAmountEntered_giftCard = $account_giftCard->getTrugiftcardCredit();
        } else {
            $creditAmountEntered = $session->getBaseTruwalletCreditAmount();
            $creditAmountEntered_giftCard = $session->getBaseTrugiftcardCreditAmount();
            $account = Mage::helper('truwallet/account')->getCurrentAccount();
            $account_giftCard = Mage::helper('trugiftcard/account')->getCurrentAccount();
        }

        if($account != null && $account->getId() && !Mage::helper('custompromotions')->truWalletInCart() &&
            (!Mage::helper('custompromotions')->truGiftCardInCart() || (Mage::helper('custompromotions')->truGiftCardInCart() && Mage::helper('trugiftcard')->getSpendConfig('use_truwallet'))
                || $this->checkIsAdmin())

        ){

        } else {
            $session->setBaseTruwalletCreditAmount(null);
            $no_truWallet = true;
        }
		
		if(($account == null ||  $account->getTruwalletCredit() == 0) && ($account_giftCard == null || $account_giftCard->getTrugiftcardCredit() == 0))
			return $this;
		
        if (!$creditAmountEntered && !$creditAmountEntered_giftCard)
            return $this;

        $baseDiscountTotal = 0;
        $baseTruWalletForShipping = 0;

        foreach ($address->getAllItems() as $item) {
            if ($item->getParentItemId()) {
                continue;
            }

            if ($item->getHasChildren() && $item->isChildrenCalculated()) {
                foreach ($item->getChildren() as $child) {
                    if (!$child->isDeleted()) {
                        $itemDiscount = $child->getBaseRowTotal() + $child->getBaseTaxAmount() - $child->getBaseDiscountAmount() - $child->getMagestoreBaseDiscount();
                        $baseDiscountTotal += $itemDiscount;
                    }
                }
            } else if ($item->getProduct()) {
                if (!$item->isDeleted()) {
                    $itemDiscount = $item->getBaseRowTotal() + $item->getBaseTaxAmount() - $item->getBaseDiscountAmount() - $item->getMagestoreBaseDiscount();
                    $baseDiscountTotal += $itemDiscount;
                }
            }
        }

        $baseItemsPrice = $baseDiscountTotal;
        if ($helper->getSpendConfig('shipping')) {
            $shippingDiscount = $address->getBaseShippingAmount() + $address->getBaseShippingTaxAmount() - $address->getBaseShippingDiscountAmount() - $address->getMagestoreBaseDiscountForShipping();
            $baseDiscountTotal += $shippingDiscount;
        }

        $truwalletBalance = $no_truWallet ? 0 : $account->getTruwalletCredit();
        $trugiftcardBalance = $account_giftCard->getTrugiftcardCredit();

        /* Fix bug conflict with giftwrap of OSC */
        $wrapTotal = 0;
        if (Mage::helper('core')->isModuleOutputEnabled('Magestore_Onestepcheckout')) {
            $_helper = Mage::helper('onestepcheckout');
            $active = $_helper->enableGiftWrap();
            $deliveryType = $session->getData('delivery_type');
            if ($active && $deliveryType != null) {
                $giftWrapAmount = $_helper->getGiftwrapAmount();
                if ($deliveryType == 2)
                    $wrapTotal = $giftWrapAmount;
            }
        }
        $baseDiscountTotal += $wrapTotal;
        /* END Fix bug conflict with giftwrap of OSC */
        $baseTruGiftCardDiscount = 0;
        $baseTruWalletDiscount = 0;
        if($baseDiscountTotal < floatval($truwalletBalance))
        {
            if(Mage::helper('truwallet')->getEnableChangeBalance()){
                if($creditAmountEntered > $baseDiscountTotal)
                    $baseTruWalletDiscount = $baseDiscountTotal;
                else
                    $baseTruWalletDiscount = $creditAmountEntered;
            } else {
                $baseTruWalletDiscount = $baseDiscountTotal;
            }
        } else {
            if(!Mage::helper('truwallet')->getEnableChangeBalance())
            {
                $baseTruWalletDiscount = floatval($truwalletBalance);
            } else if(floatval($truwalletBalance) < $creditAmountEntered)
                $baseTruWalletDiscount = floatval($truwalletBalance);
            else
                $baseTruWalletDiscount = $creditAmountEntered;
        }

        if($this->checkIsAdmin()){
            if($this->isAppliedTGCToOrder($admin_session->getOrderCustomerId())){
                $baseDiscountTotalExclusionTruWallet = $baseDiscountTotal - $baseTruWalletDiscount;
                if($baseDiscountTotalExclusionTruWallet > 0){
                    if($baseDiscountTotalExclusionTruWallet < floatval($trugiftcardBalance))
                    {
                        if($creditAmountEntered_giftCard > $baseDiscountTotalExclusionTruWallet)
                            $baseTruGiftCardDiscount = $baseDiscountTotalExclusionTruWallet;
                        else
                            $baseTruGiftCardDiscount = $creditAmountEntered_giftCard;
                    } else {
                        if(floatval($trugiftcardBalance) < $creditAmountEntered_giftCard)
                            $baseTruGiftCardDiscount = floatval($trugiftcardBalance);
                        else
                            $baseTruGiftCardDiscount = $creditAmountEntered_giftCard;
                    }
                }
            }
        } else {
            $baseDiscountTotalExclusionTruWallet = $baseDiscountTotal - $baseTruWalletDiscount;
            if($baseDiscountTotalExclusionTruWallet > 0){
                if($baseDiscountTotalExclusionTruWallet < floatval($trugiftcardBalance))
                {
                    if($creditAmountEntered_giftCard > $baseDiscountTotalExclusionTruWallet)
                        $baseTruGiftCardDiscount = $baseDiscountTotalExclusionTruWallet;
                    else
                        $baseTruGiftCardDiscount = $creditAmountEntered_giftCard;
                } else {
                    if(floatval($trugiftcardBalance) < $creditAmountEntered_giftCard)
                        $baseTruGiftCardDiscount = floatval($trugiftcardBalance);
                    else
                        $baseTruGiftCardDiscount = $creditAmountEntered_giftCard;
                }
            }
        }


        $baseDiscount = $baseTruWalletDiscount + $baseTruGiftCardDiscount;

        if ($this->checkIsAdmin() && strcasecmp(Mage::helper('trubox')->getShippingMethod(), 'flatrate_flatrate') == 0) {
            $baseDiscount += Mage::helper('trubox')->getShippingAmount();
        }

        $truwalletDiscount = Mage::getModel('truwallet/customer')
            ->getConvertedFromBaseTruwalletCredit($baseDiscount);

        if ($baseDiscount < $baseItemsPrice)
            $rate = $baseDiscount / $baseItemsPrice;
        else {
            $rate = 1;
            $baseTruWalletForShipping = $baseDiscount - $baseItemsPrice;
        }

        //update session



        $address->setOnestepcheckoutGiftwrapAmount($wrapTotal);
        $address->setGrandTotal($address->getGrandTotal() - $truwalletDiscount + $wrapTotal);
        $address->setBaseGrandTotal($address->getBaseGrandTotal() - $baseDiscount + $wrapTotal);

        if ($this->checkIsAdmin() && strcasecmp(Mage::helper('trubox')->getShippingMethod(), 'flatrate_flatrate') == 0) {
            $shipping_amount = Mage::helper('trubox')->getShippingAmount();
            $remaining_fee = 0;

            if($baseTruWalletDiscount < $shipping_amount) {
                $address->setTruwalletDiscount($baseTruWalletDiscount);
                $address->setBaseTruwalletDiscount($baseTruWalletDiscount);
                $session->setBaseTruwalletCreditAmount($baseTruWalletDiscount);
                $remaining_fee += $shipping_amount;
            } else if($baseTruWalletDiscount >= $shipping_amount) {
                if(($baseTruWalletDiscount + $shipping_amount) > $truwalletBalance){
                    $remaining_fee += ($baseTruWalletDiscount + $shipping_amount) - $truwalletBalance;
                }
                $address->setTruwalletDiscount(($baseTruWalletDiscount + $shipping_amount) > $truwalletBalance ? $truwalletBalance : ($baseTruWalletDiscount + $shipping_amount));
                $address->setBaseTruwalletDiscount(($baseTruWalletDiscount + $shipping_amount) > $truwalletBalance ? $truwalletBalance : ($baseTruWalletDiscount + $shipping_amount));
                $session->setBaseTruwalletCreditAmount(($baseTruWalletDiscount + $shipping_amount) > $truwalletBalance ? $truwalletBalance : ($baseTruWalletDiscount + $shipping_amount));
            }

            if($this->isAppliedTGCToOrder($admin_session->getOrderCustomerId())){
                $session->setBaseTrugiftcardCreditAmount(($baseTruGiftCardDiscount + $remaining_fee) > $trugiftcardBalance ? $trugiftcardBalance : ($baseTruGiftCardDiscount + $remaining_fee));
                $address->setTrugiftcardDiscount(($baseTruGiftCardDiscount + $remaining_fee) > $trugiftcardBalance ? $trugiftcardBalance : ($baseTruGiftCardDiscount + $remaining_fee));
                $address->setBaseTrugiftcardDiscount(($baseTruGiftCardDiscount + $remaining_fee) > $trugiftcardBalance ? $trugiftcardBalance : ($baseTruGiftCardDiscount + $remaining_fee));
            }
        } else {
            $session->setBaseTruwalletCreditAmount($baseTruWalletDiscount);
            $address->setTruwalletDiscount($baseTruWalletDiscount);
            $address->setBaseTruwalletDiscount($baseTruWalletDiscount);

            $session->setBaseTrugiftcardCreditAmount($baseTruGiftCardDiscount);
            $address->setTrugiftcardDiscount($baseTruGiftCardDiscount);
            $address->setBaseTrugiftcardDiscount($baseTruGiftCardDiscount);
        }

        //distribute discount
        $this->_prepareDiscountCreditForAmount($address, $rate, $baseTruWalletForShipping);
        return $this;
    }

    /**
     * @param Mage_Sales_Model_Quote_Address $address
     * @return $this
     */
    public function fetch(Mage_Sales_Model_Quote_Address $address)
    {
        $quote = $address->getQuote();
        if (Mage::getStoreConfig('truwallet/spend/tax', $quote->getStoreId()) == 0) {
            return $this;
        }
        if (!$quote->isVirtual() && $address->getData('address_type') == 'billing')
            return $this;
        $session = Mage::getSingleton('checkout/session');
        $customer_credit_discount = $address->getTruWalletDiscount();
        if ($session->getBaseTruwalletCreditAmount())
            $customer_credit_discount = $session->getBaseTruwalletCreditAmount();
        if ($customer_credit_discount > 0) {
            $address->addTotal(array(
                'code' => $this->getCode(),
                'title' => Mage::helper('truwallet')->getSpendConfig('discount_label').'2',
                'value' => -Mage::helper('core')->currency($customer_credit_discount, false, false)
            ));
        }

        $customer_credit_discount = $address->getTruGiftCardDiscount();
        if ($session->getBaseTrugiftcardCreditAmount())
            $customer_credit_discount = $session->getBaseTrugiftcardCreditAmount();
        if ($customer_credit_discount > 0) {
            $address->addTotal(array(
                'code' => $this->getCode(),
                'title' => Mage::helper('trugiftcard')->getSpendConfig('discount_label'),
                'value' => -Mage::helper('core')->currency($customer_credit_discount, false, false)
            ));
        }

        return $this;
    }

    /**
     * @param Mage_Sales_Model_Quote_Address $address
     * @param $rate
     * @param $baseTruWalletForShipping
     * @return $this
     */
    public function _prepareDiscountCreditForAmount(Mage_Sales_Model_Quote_Address $address, $rate, $baseTruWalletForShipping)
    {
        // Update discount for each item
        $helper = Mage::helper('truwallet');
        foreach ($address->getAllItems() as $item) {
            if ($item->getParentItemId())
                continue;
            if ($item->getHasChildren() && $item->isChildrenCalculated()) {
                foreach ($item->getChildren() as $child) {
                    if (!$child->isDeleted()) {
                        $baseItemPrice = $child->getBaseRowTotal() + $child->getBaseTaxAmount() - $child->getBaseDiscountAmount() - $child->getMagestoreBaseDiscount();
                        $itemBaseDiscount = $baseItemPrice * $rate;
                        $itemDiscount = Mage::app()->getStore()->convertPrice($itemBaseDiscount);
                        $child->setMagestoreBaseDiscount($child->getMagestoreBaseDiscount() + $itemBaseDiscount);
                        $child->setBaseTruwalletDiscount($itemBaseDiscount)
                            ->setTruwalletDiscount($itemDiscount)
                            ->setBaseTrugiftcardDiscount($itemBaseDiscount)
                            ->setTrugiftcardDiscount($itemDiscount);
                    }
                }
            } else if ($item->getProduct()) {
                if (!$item->isDeleted()) {
                    $baseItemPrice = $item->getBaseRowTotal() + $item->getBaseTaxAmount() - $item->getBaseDiscountAmount() - $item->getMagestoreBaseDiscount();
                    $itemBaseDiscount = $baseItemPrice * $rate;
                    $itemDiscount = Mage::app()->getStore()->convertPrice($itemBaseDiscount);
                    $item->setMagestoreBaseDiscount($item->getMagestoreBaseDiscount() + $itemBaseDiscount);
                    $item->setBaseTruwalletDiscount($itemBaseDiscount)
                        ->setTruwalletDiscount($itemDiscount)
                        ->setBaseTrugiftcardDiscount($itemBaseDiscount)
                        ->setTrugiftcardDiscount($itemDiscount);
                }
            }
        }
        if ($helper->getSpendConfig('shipping') && $baseTruWalletForShipping) {
            $baseShippingPrice = $address->getBaseShippingAmount() + $address->getBaseShippingTaxAmount() - $address->getBaseShippingDiscountAmount() - $address->getMagestoreBaseDiscountForShipping();
            $baseShippingDiscount = min($baseShippingPrice, $baseTruWalletForShipping);
            $shippingDiscount = Mage::app()->getStore()->convertPrice($baseShippingDiscount);
            $address->setMagestoreBaseDiscountForShipping($address->getMagestoreBaseDiscountForShipping() + $baseShippingDiscount);
            $address->setBaseTruwalletDiscountForShipping($baseShippingDiscount);
            $address->setTruwalletDiscountForShipping($shippingDiscount);
            $address->setBaseTrugiftcardDiscountForShipping($baseShippingDiscount);
            $address->setTrugiftcardDiscountForShipping($shippingDiscount);
        }
        return $this;
    }
}
