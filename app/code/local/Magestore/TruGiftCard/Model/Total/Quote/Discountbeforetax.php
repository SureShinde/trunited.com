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
class Magestore_TruGiftCard_Model_Total_Quote_Discountbeforetax extends Mage_Sales_Model_Quote_Address_Total_Abstract {

    protected $_hiddentBaseDiscount = 0;
    protected $_hiddentDiscount = 0;

    /**
     * Magestore_TruGiftCard_Model_Total_Quote_Discountbeforetax constructor.
     */
    public function __construct() {
        $this->setCode('trugiftcard_before_tax');
    }

    /**
     * @param Mage_Sales_Model_Quote_Address $address
     * @return $this
     */
    public function collect(Mage_Sales_Model_Quote_Address $address) {
        parent::collect($address);
        $session = Mage::getSingleton('checkout/session');
        $quote = $address->getQuote();
        if (Mage::getStoreConfig('trugiftcard/spend/tax', $quote->getStoreId()) == '1') {
            return $this;
        }
        $items = $quote->getAllItems();
        if (!$quote->isVirtual() && $address->getAddressType() == 'billing') {
            return $this;
        }
        if ($quote->isVirtual() && $address->getAddressType() == 'shipping') {
            return $this;
        }
        
        $helper = Mage::helper('trugiftcard');

        $creditAmountEntered = $session->getBaseCustomerCreditAmount();
        if($creditAmountEntered < 0.0001)
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
                        if (Mage::helper('tax')->priceIncludesTax())
                            $itemDiscount = $child->getRowTotalInclTax() - $child->getBaseDiscountAmount() - $child->getMagestoreBaseDiscount();
                        else
                            $itemDiscount = $child->getBaseRowTotal() - $child->getBaseDiscountAmount() -$child->getMagestoreBaseDiscount();
                        $baseDiscountTotal += $itemDiscount;
                    }
                }
            } else if ($item->getProduct()) {
                if (!$item->isDeleted()) {
                    if (Mage::helper('tax')->priceIncludesTax())
                        $itemDiscount = $item->getRowTotalInclTax() - $item->getBaseDiscountAmount() - $item->getMagestoreBaseDiscount();
                    else
                        $itemDiscount = $item->getBaseRowTotal() - $item->getBaseDiscountAmount() - $item->getMagestoreBaseDiscount();
                    $baseDiscountTotal += $itemDiscount;
                }
            }
        }
        $baseItemsPrice = $baseDiscountTotal;
        if ($helper->getSpendConfig('shipping')) {
            if (Mage::helper('tax')->shippingPriceIncludesTax())
                $shippingDiscount = $address->getShippingInclTax() - $address->getBaseShippingDiscountAmount() - $address->getMagestoreBaseDiscountForShipping();
            else
                $shippingDiscount = $address->getBaseShippingAmount() - $address->getBaseShippingDiscountAmount() - $address->getMagestoreBaseDiscountForShipping();
            $baseDiscountTotal += $shippingDiscount;
        }
        $account = Mage::helper('trugiftcard/account')->getCurrentAccount();
        $trugiftcardBalance = $account->getTrugiftcardCredit();
        $baseTrugiftcardDiscount = min($creditAmountEntered, $baseDiscountTotal, $trugiftcardBalance);
        $trugiftcardDiscount = Mage::getModel('trugiftcard/customer')
                ->getConvertedFromBaseCustomerCredit($baseTrugiftcardDiscount);
        
        if ($baseTrugiftcardDiscount < $baseItemsPrice)
            $rate = $baseTrugiftcardDiscount / $baseItemsPrice;
        else {
            $rate = 1;
            $baseTruGiftCardForShipping = $baseTrugiftcardDiscount - $baseItemsPrice;
        }
        //distribute discount
        $this->_prepareDiscountCreditForAmount($address, $rate, $baseTruGiftCardForShipping);
        
        //update session
        $session->setBaseCustomerCreditAmount($baseTrugiftcardDiscount);
        
        //update address
        $address->setBaseTrugiftcardHiddenTax($this->_hiddentBaseDiscount);
        $address->setTruGiftCardHiddenTax($this->_hiddentDiscount);

        $address->setGrandTotal($address->getGrandTotal() - $trugiftcardDiscount + $this->_hiddentBaseDiscount);
        $address->setBaseGrandTotal($address->getBaseGrandTotal() - $baseTrugiftcardDiscount + $this->_hiddentDiscount);
        $address->setTrugiftcardDiscount($trugiftcardDiscount);
        $address->setBaseTrugiftcardDiscount($baseTrugiftcardDiscount);
        
        return $this;
    }


    /**
     * @param Mage_Sales_Model_Quote_Address $address
     * @return $this
     */
    public function fetch(Mage_Sales_Model_Quote_Address $address) {
        $quote = $address->getQuote();
        if (Mage::getStoreConfig('trugiftcard/spend/tax', $quote->getStoreId()) == 1) {
            return $this;
        }
        if (!$quote->isVirtual() && $address->getData('address_type') == 'billing')
            return $this;
        $session = Mage::getSingleton('checkout/session');
        $customer_credit_discount = $address->getTrugiftcardDiscount();
        if ($session->getBaseCustomerCreditAmount())
            $customer_credit_discount = $session->getBaseCustomerCreditAmount();
        if ($customer_credit_discount > 0) {
            $address->addTotal(array(
                'code' => $this->getCode(),
                'title' => Mage::helper('trugiftcard')->getSpendConfig('discount_label'),
                'value' => -$customer_credit_discount,
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
    public function _prepareDiscountCreditForAmount(Mage_Sales_Model_Quote_Address $address, $rate, $baseTruGiftCardForShipping) {
        // Update discount for each item
        $helper = Mage::helper('trugiftcard');
        $store = Mage::app()->getStore();
        foreach ($address->getAllItems() as $item) {
            if ($item->getParentItemId())
                continue;
            if ($item->getHasChildren() && $item->isChildrenCalculated()) {
                foreach ($item->getChildren() as $child) {
                    if(!$child->isDeleted()) {
                        if (Mage::helper('tax')->priceIncludesTax())
                            $baseItemPrice = $child->getRowTotalInclTax() - $child->getBaseDiscountAmount() - $child->getMagestoreBaseDiscount();
                        else
                            $baseItemPrice = $child->getBaseRowTotal() - $child->getBaseDiscountAmount() - $child->getMagestoreBaseDiscount();
                        $itemBaseDiscount = $baseItemPrice * $rate;
                        $itemDiscount = Mage::app()->getStore()->convertPrice($itemBaseDiscount);
                        $child->setMagestoreBaseDiscount($child->getMagestoreBaseDiscount() + $itemBaseDiscount);
                        $child->setBaseTrugiftcardDiscount($itemBaseDiscount)
                                ->setTrugiftcardDiscount($itemDiscount);
                        
                        $baseTaxableAmount = $child->getBaseTaxableAmount();
                        $taxableAmount = $child->getTaxableAmount();
                        $child->setBaseTaxableAmount($baseTaxableAmount - $itemBaseDiscount);
                        $child->setTaxableAmount($taxableAmount - $itemDiscount);
                        
                        if(Mage::helper('tax')->priceIncludesTax()) {
                            $taxRate = $this->getItemRateOnQuote($address, $child->getProduct(), $store);
                            $baseHiddenTax = $this->round($this->calTax($baseTaxableAmount, $taxRate) - $this->calTax($child->getBaseTaxableAmount(), $taxRate));
                            $hiddenTax = $this->round($this->calTax($taxableAmount, $taxRate) - $this->calTax($child->getTaxableAmount(), $taxRate));
                            
                            $this->_hiddentBaseDiscount += $baseHiddenTax;
                            $this->_hiddentDiscount += $hiddenTax;
                            
                            $child->setBaseTrugiftcardHiddenTax($child->getBaseTrugiftcardHiddenTax() + $baseHiddenTax);
                            $child->setTruGiftCardHiddenTax($child->getTruGiftCardHiddenTax() + $hiddenTax);
                        }
                    }
                }
            } else if ($item->getProduct()) {
                if(!$item->isDeleted()) {
                    if (Mage::helper('tax')->priceIncludesTax())
                        $baseItemPrice = $item->getRowTotalInclTax() - $item->getBaseDiscountAmount() - $item->getMagestoreBaseDiscount();
                    else
                        $baseItemPrice = $item->getBaseRowTotal() - $item->getBaseDiscountAmount() - $item->getMagestoreBaseDiscount();
                    $itemBaseDiscount = $baseItemPrice * $rate;
                    $itemDiscount = Mage::app()->getStore()->convertPrice($itemBaseDiscount);
                    $item->setMagestoreBaseDiscount($item->getMagestoreBaseDiscount() + $itemBaseDiscount);
                    $item->setBaseTrugiftcardDiscount($itemBaseDiscount)
                            ->setTrugiftcardDiscount($itemDiscount);
                    
                    $baseTaxableAmount = $item->getBaseTaxableAmount();
                    $taxableAmount = $item->getTaxableAmount();
                    $item->setBaseTaxableAmount($baseTaxableAmount - $itemBaseDiscount);
                    $item->setTaxableAmount($taxableAmount - $itemDiscount);
                    
                    if(Mage::helper('tax')->priceIncludesTax()) {
                        $taxRate = $this->getItemRateOnQuote($address, $item->getProduct(), $store);
                        $baseHiddenTax = $this->round($this->calTax($baseTaxableAmount, $taxRate) - $this->calTax($item->getBaseTaxableAmount(), $taxRate));
                        $hiddenTax = $this->round($this->calTax($taxableAmount, $taxRate) - $this->calTax($item->getTaxableAmount(), $taxRate));
                        
                        $this->_hiddentBaseDiscount += $baseHiddenTax;
                        $this->_hiddentDiscount += $hiddenTax;
                        $item->setBaseTrugiftcardHiddenTax($item->getBaseTrugiftcardHiddenTax() + $baseHiddenTax);
                        $item->setTruGiftCardHiddenTax($item->getTruGiftCardHiddenTax() + $hiddenTax);
                    }
                }
            }
        }
        if ($helper->getSpendConfig('shipping') && $baseTruGiftCardForShipping) {
            if (Mage::helper('tax')->shippingPriceIncludesTax())
                $baseShippingPrice = $address->getShippingInclTax() - $address->getBaseShippingDiscountAmount() - $address->getMagestoreBaseDiscountForShipping();
            else
                $baseShippingPrice = $address->getBaseShippingAmount() - $address->getBaseShippingDiscountAmount() - $address->getMagestoreBaseDiscountForShipping();
            $baseShippingDiscount = min($baseTruGiftCardForShipping, $baseShippingPrice);
            $shippingDiscount = Mage::app()->getStore()->convertPrice($baseShippingDiscount);
            $address->setMagestoreBaseDiscountForShipping($address->getMagestoreBaseDiscountForShipping() + $baseShippingDiscount);
            $address->setBaseTrugiftcardDiscountForShipping($baseShippingDiscount);
            $address->setTrugiftcardDiscountForShipping($shippingDiscount);
            
            $baseTaxableShippingAmount = $address->getBaseShippingTaxable();
            $taxableShippingAmount = $address->getShippingTaxable();
            
            $address->setBaseShippingTaxable($baseTaxableShippingAmount - $address->getBaseTrugiftcardDiscountForShipping());
            $address->setShippingTaxable($taxableShippingAmount - $address->getTrugiftcardDiscountForShipping());
            
            if(Mage::helper('tax')->shippingPriceIncludesTax()) {
                $taxShippingRate = $this->getShipingTaxRate($address, $store);
                $this->_hiddentBaseDiscount += $this->round($this->calTax($baseTaxableShippingAmount, $taxShippingRate) - $this->calTax($address->getBaseShippingTaxable(), $taxShippingRate));
                $this->_hiddentDiscount += $this->round($this->calTax($taxableShippingAmount, $taxShippingRate) - $this->calTax($address->getShippingTaxable(), $taxShippingRate));
                //update address
                $address->setBaseTrugiftcardShippingHiddenTax($this->round($this->calTax($baseTaxableShippingAmount, $taxShippingRate) - $this->calTax($address->getBaseShippingTaxable(), $taxShippingRate)));
                $address->setTrugiftcardShippingHiddenTax($this->round($this->calTax($taxableShippingAmount, $taxShippingRate) - $this->calTax($address->getShippingTaxable(), $taxShippingRate)));
            }
        }
        return $this;
    }

    //get Rate
    /**
     * @param $address
     * @param $product
     * @param $store
     * @return int
     */
    public function getItemRateOnQuote($address, $product, $store) {
        $taxClassId = $product->getTaxClassId();
        if ($taxClassId) {
            $request = Mage::getSingleton('tax/calculation')->getRateRequest(
                    $address, $address->getQuote()->getBillingAddress(), $address->getQuote()->getCustomerTaxClassId(), $store
            );
            $rate = Mage::getSingleton('tax/calculation')
                    ->getRate($request->setProductClassId($taxClassId));
            return $rate;
        }
        return 0;
    }

    /**
     * @param $price
     * @param $rate
     * @return mixed
     */
    public function calTax($price, $rate) {
        return Mage::getSingleton('tax/calculation')->calcTaxAmount($price, $rate, true, false);
    }

    /**
     * @param $address
     * @param $store
     * @return mixed
     */
    public function getShipingTaxRate($address, $store) {
        $request = Mage::getSingleton('tax/calculation')->getRateRequest(
                $address, $address->getQuote()->getBillingAddress(), $address->getQuote()->getCustomerTaxClassId(), $store
        );
        $request->setProductClassId(Mage::getSingleton('tax/config')->getShippingTaxClass($store));
        $rate = Mage::getSingleton('tax/calculation')->getRate($request);
        return $rate;
    }

    /**
     * @param $price
     * @return mixed
     */
    public function round($price) {
        return Mage::getSingleton('tax/calculation')->round($price);
    }
}
