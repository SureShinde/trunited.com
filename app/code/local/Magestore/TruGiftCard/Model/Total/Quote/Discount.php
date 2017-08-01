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
 * TruGiftCard Model
 *
 * @category    Magestore
 * @package     Magestore_TruGiftCard
 * @author      Magestore Developer
 */
class Magestore_TruGiftCard_Model_Total_Quote_Discount extends Mage_Sales_Model_Quote_Address_Total_Abstract
{

    /**
     * Magestore_TruGiftCard_Model_Total_Quote_Discount constructor.
     */
    public function __construct()
    {
        $this->setCode('trugiftcard_after_tax');
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

        if (Mage::getStoreConfig('trugiftcard/spend/tax', $quote->getStoreId()) == '0') {
            return $this;
        }
        if (!$quote->isVirtual() && $address->getAddressType() == 'billing') {
            return $this;
        }
        if ($quote->isVirtual() && $address->getAddressType() == 'shipping') {
            return $this;
        }

        $helper = Mage::helper('trugiftcard');

        if ($this->checkIsAdmin()) {
            $account = Mage::helper('trugiftcard/account')->loadByCustomerId($admin_session->getOrderCustomerId());
            $account_truWallet = Mage::helper('truwallet/account')->loadByCustomerId($admin_session->getOrderCustomerId());
            $creditAmountEntered = $account->getTrugiftcardCredit();
        } else {
            $creditAmountEntered = $session->getBaseTrugiftcardCreditAmount();
            $account = Mage::helper('trugiftcard/account')->getCurrentAccount();
            $account_truWallet = Mage::helper('truwallet/account')->getCurrentAccount();
        }

        if($account_truWallet != null && $account_truWallet->getId() && !Mage::helper('custompromotions')->truWalletInCart() &&
            (!Mage::helper('custompromotions')->truGiftCardInCart() && Mage::helper('trugiftcard')->getSpendConfig('use_truwallet')))
            $truWallet_discount = $session->getBaseTruwalletCreditAmount() == 0 ? $account_truWallet->getTruwalletCredit() : $session->getBaseTruwalletCreditAmount();
        else
            $truWallet_discount = 0;

        if (!$creditAmountEntered)
            return $this;

        $baseDiscountTotal = 0;
        $baseTruGiftCardForShipping = 0;

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

        $trugiftcardBalance = $account->getTrugiftcardCredit();

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

        $baseDiscountTotalExclusionTruWallet = $baseDiscountTotal - $truWallet_discount;
        if($baseDiscountTotalExclusionTruWallet <= 0)
            return $this;

        if($baseDiscountTotalExclusionTruWallet < floatval($trugiftcardBalance))
        {
            if($creditAmountEntered > $baseDiscountTotalExclusionTruWallet)
                $baseTruGiftCardDiscount = $baseDiscountTotalExclusionTruWallet;
            else
                $baseTruGiftCardDiscount = $creditAmountEntered;
        } else {
            if(floatval($trugiftcardBalance) < $creditAmountEntered)
                $baseTruGiftCardDiscount = floatval($trugiftcardBalance);
            else
                $baseTruGiftCardDiscount = $creditAmountEntered;
        }

        if ($this->checkIsAdmin() && strcasecmp(Mage::helper('trubox')->getShippingMethod(), 'flatrate_flatrate') == 0) {
            $baseTruGiftCardDiscount += Mage::helper('trubox')->getShippingAmount();
        }

        $trugiftcardDiscount = Mage::getModel('trugiftcard/customer')
            ->getConvertedFromBaseTrugiftcardCredit($baseTruGiftCardDiscount);

        if ($baseTruGiftCardDiscount < $baseItemsPrice)
            $rate = $baseTruGiftCardDiscount / $baseItemsPrice;
        else {
            $rate = 1;
            $baseTruGiftCardForShipping = $baseTruGiftCardDiscount - $baseItemsPrice;
        }

        //update session
        $session->setBaseTrugiftcardCreditAmount($baseTruGiftCardDiscount);

        //update address
        $address->setGrandTotal($address->getGrandTotal() - $trugiftcardDiscount);
        $address->setBaseGrandTotal($address->getBaseGrandTotal() - $baseTruGiftCardDiscount);

        $address->setTrugiftcardDiscount($trugiftcardDiscount);
        $address->setBaseTrugiftcardDiscount($baseTruGiftCardDiscount);

        //distribute discount
        $this->_prepareDiscountCreditForAmount($address, $rate, $baseTruGiftCardForShipping);
        return $this;
    }

    /**
     * @param Mage_Sales_Model_Quote_Address $address
     * @return $this
     */
    public function fetch(Mage_Sales_Model_Quote_Address $address)
    {
        $quote = $address->getQuote();
        if (Mage::getStoreConfig('trugiftcard/spend/tax', $quote->getStoreId()) == 0) {
            return $this;
        }
        if (!$quote->isVirtual() && $address->getData('address_type') == 'billing')
            return $this;
        $session = Mage::getSingleton('checkout/session');
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
     * @param $baseTruGiftCardForShipping
     * @return $this
     */
    public function _prepareDiscountCreditForAmount(Mage_Sales_Model_Quote_Address $address, $rate, $baseTruGiftCardForShipping)
    {
        // Update discount for each item
        $helper = Mage::helper('trugiftcard');
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
                        $child->setBaseTrugiftcardDiscount($itemBaseDiscount)
                            ->setTrugiftcardDiscount($itemDiscount);
                    }
                }
            } else if ($item->getProduct()) {
                if (!$item->isDeleted()) {
                    $baseItemPrice = $item->getBaseRowTotal() + $item->getBaseTaxAmount() - $item->getBaseDiscountAmount() - $item->getMagestoreBaseDiscount();
                    $itemBaseDiscount = $baseItemPrice * $rate;
                    $itemDiscount = Mage::app()->getStore()->convertPrice($itemBaseDiscount);
                    $item->setMagestoreBaseDiscount($item->getMagestoreBaseDiscount() + $itemBaseDiscount);
                    $item->setBaseTrugiftcardDiscount($itemBaseDiscount)
                        ->setTrugiftcardDiscount($itemDiscount);
                }
            }
        }
        if ($helper->getSpendConfig('shipping') && $baseTruGiftCardForShipping) {
            $baseShippingPrice = $address->getBaseShippingAmount() + $address->getBaseShippingTaxAmount() - $address->getBaseShippingDiscountAmount() - $address->getMagestoreBaseDiscountForShipping();
            $baseShippingDiscount = min($baseShippingPrice, $baseTruGiftCardForShipping);
            $shippingDiscount = Mage::app()->getStore()->convertPrice($baseShippingDiscount);
            $address->setMagestoreBaseDiscountForShipping($address->getMagestoreBaseDiscountForShipping() + $baseShippingDiscount);
            $address->setBaseTrugiftcardDiscountForShipping($baseShippingDiscount);
            $address->setTrugiftcardDiscountForShipping($shippingDiscount);
        }
        return $this;
    }
}