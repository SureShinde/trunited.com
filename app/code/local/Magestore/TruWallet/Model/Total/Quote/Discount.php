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
class Magestore_TruWallet_Model_Total_Quote_Discount extends Mage_Sales_Model_Quote_Address_Total_Abstract {

    /**
     * Magestore_TruWallet_Model_Total_Quote_Discount constructor.
     */
    public function __construct() {
        $this->setCode('truwallet_after_tax');
    }

    /**
     * @param Mage_Sales_Model_Quote_Address $address
     * @return $this
     */
    public function collect(Mage_Sales_Model_Quote_Address $address) {
        parent::collect($address);
        $quote = $address->getQuote();
        $items = $address->getAllItems();
        $session = Mage::getSingleton('checkout/session');
        
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

        $helper = Mage::helper('truwallet');
        
        $creditAmountEntered = $session->getBaseTruwalletCreditAmount();
        if(!$creditAmountEntered)
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

        $account = Mage::helper('truwallet/account')->getCurrentAccount();
        $truwalletBalance = $account->getTruwalletCredit();
        
        $baseTruWalletDiscount = min($creditAmountEntered, $baseDiscountTotal, $truwalletBalance);
        $truwalletDiscount = Mage::getModel('truwallet/customer')
                ->getConvertedFromBaseTruwalletCredit($baseTruWalletDiscount);
        
        if ($baseTruWalletDiscount < $baseItemsPrice)
            $rate = $baseTruWalletDiscount / $baseItemsPrice;
        else {
            $rate = 1;
            $baseTruWalletForShipping = $baseTruWalletDiscount - $baseItemsPrice;
        }
        //update session
        $session->setBaseTruwalletCreditAmount($baseTruWalletDiscount);
        
        //update address
        $address->setGrandTotal($address->getGrandTotal() - $truwalletDiscount);
        $address->setBaseGrandTotal($address->getBaseGrandTotal() - $baseTruWalletDiscount);
        $address->setTruwalletDiscount($truwalletDiscount);
        $address->setBaseTruwalletDiscount($baseTruWalletDiscount);
        
        //distribute discount
        $this->_prepareDiscountCreditForAmount($address, $rate, $baseTruWalletForShipping);
        return $this;
    }

    /**
     * @param Mage_Sales_Model_Quote_Address $address
     * @return $this
     */
    public function fetch(Mage_Sales_Model_Quote_Address $address) {
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
                'title' => Mage::helper('truwallet')->__('truWallet Balance'),
                'value' => -Mage::helper('core')->currency($customer_credit_discount,false,false)
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
    public function _prepareDiscountCreditForAmount(Mage_Sales_Model_Quote_Address $address, $rate, $baseTruWalletForShipping) {
        // Update discount for each item
        $helper = Mage::helper('truwallet');
        foreach ($address->getAllItems() as $item) {
            if ($item->getParentItemId())
                continue;
            if ($item->getHasChildren() && $item->isChildrenCalculated()) {
                foreach ($item->getChildren() as $child) {
                    if(!$child->isDeleted()) {
                        $baseItemPrice = $child->getBaseRowTotal() + $child->getBaseTaxAmount() - $child->getBaseDiscountAmount() - $child->getMagestoreBaseDiscount();
                        $itemBaseDiscount = $baseItemPrice * $rate;
                        $itemDiscount = Mage::app()->getStore()->convertPrice($itemBaseDiscount);
                        $child->setMagestoreBaseDiscount($child->getMagestoreBaseDiscount() + $itemBaseDiscount);
                        $child->setBaseTruwalletDiscount($itemBaseDiscount)
                                ->setTruwalletDiscount($itemDiscount);
                    }
                }
            } else if ($item->getProduct()) {
                if(!$item->isDeleted()) {
                    $baseItemPrice = $item->getBaseRowTotal() + $item->getBaseTaxAmount() - $item->getBaseDiscountAmount() - $item->getMagestoreBaseDiscount();
                    $itemBaseDiscount = $baseItemPrice * $rate;
                    $itemDiscount = Mage::app()->getStore()->convertPrice($itemBaseDiscount);
                    $item->setMagestoreBaseDiscount($item->getMagestoreBaseDiscount() + $itemBaseDiscount);
                    $item->setBaseTruwalletDiscount($itemBaseDiscount)
                            ->setTruwalletDiscount($itemDiscount);
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
        }
        return $this;
    }
}
