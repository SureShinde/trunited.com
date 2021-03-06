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
 * Rewardpoints Spend for Order by Point Model
 *
 * @category    Magestore
 * @package     Magestore_RewardPoints
 * @author      Magestore Developer
 */
class Magestore_RewardPoints_Model_Total_Quote_Earning{
// extends Mage_Sales_Model_Quote_Address_Total_Abstract {

    /**
     * Change collect total to Event to ensure earning is last runned total
     *
     * @param type $observer
     */
    public function salesQuoteCollectTotalsAfter($observer) {
        $quote = $observer['quote'];
        foreach ($quote->getAllAddresses() as $address) {
            if (!$quote->isVirtual() && $address->getAddressType() == 'billing') {
                continue;
            }
            if ($quote->isVirtual() && $address->getAddressType() == 'shipping') {
                continue;
            }
            $this->collect($address, $quote);
        }
    }

    public function checkIsAdmin()
    {
        return Mage::app()->getStore()->isAdmin();
    }

    /**
     * collect reward points that customer earned (per each item and address) total
     *
     * @param Mage_Sales_Model_Quote_Address $address
     * @param Mage_Sales_Model_Quote $quote
     * @return Magestore_RewardPoints_Model_Total_Quote_Point
     */
    public function collect($address, $quote) {
        $admin_session = Mage::getSingleton('adminhtml/session');

        if (!Mage::helper('rewardpoints')->isEnable($quote->getStoreId())) {
            return $this;
        }


        if (Mage::helper('rewardpoints/calculation_spending')->getTotalPointSpent() && !Mage::getStoreConfigFlag('rewardpoints/earning/earn_when_spend', Mage::app()->getStore()->getId())) {
            $address->setRewardpointsEarn(0);
            return $this;
        }


        // get points that customer can earned by Rates
        if ($quote->isVirtual()) {
            $address = $quote->getBillingAddress();
        } else {
            $address = $quote->getShippingAddress();
        }

        Mage::dispatchEvent('rewardpoints_collect_earning_total_points_before', array(
            'address' => $address,
        ));
        if(!$address->getRewardpointsEarn()){
            $baseGrandTotal = $quote->getBaseGrandTotal();
            if (!Mage::getStoreConfigFlag(Magestore_RewardPoints_Helper_Calculation_Earning::XML_PATH_EARNING_BY_SHIPPING, $quote->getStoreId())) {
                $baseGrandTotal -= $address->getBaseShippingAmount();
                if (Mage::getStoreConfigFlag(Magestore_RewardPoints_Helper_Calculation_Earning::XML_PATH_EARNING_BY_TAX, $quote->getStoreId())) {
                    $baseGrandTotal -= $address->getBaseShippingTaxAmount();
                }
            }
            if (!Mage::getStoreConfigFlag(Magestore_RewardPoints_Helper_Calculation_Earning::XML_PATH_EARNING_BY_TAX, $quote->getStoreId())) {
                $baseGrandTotal -= $address->getBaseTaxAmount();
            }
            $baseGrandTotal = max(0, $baseGrandTotal);
            $earningPoints = Mage::helper('rewardpoints/calculation_earning')->getRateEarningPoints(
                $baseGrandTotal, $quote->getStoreId()
            );
            if ($earningPoints > 0) {
                $address->setRewardpointsEarn($earningPoints);
            }

            // Update earning point for each items
            $this->_updateEarningPoints($address);
        }

//        zend_debug::dump('BEFORE: '.$address->getRewardpointsEarn());
        Mage::dispatchEvent('rewardpoints_collect_earning_total_points_after', array(
            'address' => $address,
        ));
//        zend_debug::dump('AFTER: '.$address->getRewardpointsEarn());

        //Shopping Cart Earning Rule Points
        $shoppingCartRulePoints = Mage::helper('rewardpointsrule/calculation_earning')
            ->getShoppingCartPoints($quote);

        //TruBox Bonus Points
        $bonusPoints = 0;
        $bonusPickup = 0;

        $earningPoints = $address->getRewardpointsEarn();
        $customer_id = $admin_session->getOrderCustomerId();

        if(Mage::getStoreConfig('onestepcheckout/giftwrap/enable_bonuspoints', Mage::app()->getStore()->getId())){
            if((Mage::getSingleton('checkout/session')->getData('delivery_type') == 1) && (!$quote->isVirtual())
                || ($this->checkIsAdmin() && isset($customer_id) && $customer_id > 0)){
                $bonusPoints = ceil(0.1*($earningPoints-$shoppingCartRulePoints));
            }
        }

        //Coupon Bonus Points
        $checkCouponCode = Mage::getModel('salesrule/rule')->load(11)->getCouponCode();
        if($quote->getCouponCode() == $checkCouponCode){
            $bonusPoints += 5;
        }

        if(Mage::helper('storepickup')->getDataConfig('bonus_enable')){
            $bonus_type = Mage::helper('storepickup')->getDataConfig('bonus_type');
            $bonus_amount = Mage::helper('storepickup')->getDataConfig('bonus_amount');
            if($bonus_type == Magestore_Storepickup_Model_Source_Bonus::BONUS_TYPE_FIXED)
                $bonusPickup += $bonus_amount;
            else if($bonus_type == Magestore_Storepickup_Model_Source_Bonus::BONUS_TYPE_PERCENT) {
                $bonusPickup += ceil(($bonus_amount * ($earningPoints-$shoppingCartRulePoints)) / 100);
            }
        }

        $shipping_method = $quote->getShippingAddress()->getShippingMethod();
        if(strcasecmp($shipping_method, 'storepickup_storepickup') == 0 && Mage::helper('storepickup')->echoAllStoreCheckoutToJson()){
            $bonusPoints = 0;
            $address->setRewardpointsBonus(0);
            $address->setRewardpointsPickup($bonusPickup);
        } else {
            $address->setRewardpointsBonus($bonusPoints + $shoppingCartRulePoints);
            $address->setRewardpointsPickup(0);
            $bonusPickup = 0;
        }

        $address->setRewardpointsEarn($earningPoints + $bonusPoints + $bonusPickup);
        Mage::log('BONUS ' . $address->getRewardpointsBonus().' - PICKUP: '.$address->getRewardpointsPickup().' - EARN: '.$address->getRewardpointsEarn(), null, 'bonus.log');
        return $this;
    }

    /**
     * update earning points for address items
     *
     * @param Mage_Sales_Model_Quote_Address $address
     * @return Magestore_RewardPoints_Model_Total_Quote_Earning
     */
    protected function _updateEarningPoints($address) {
        $items = $address->getAllItems();
        $earningPoints = $address->getRewardpointsEarn();
        if (!count($items) || $earningPoints <= 0) {
            return $this;
        }

        // Calculate total item prices
        $baseItemsPrice = 0;
        $totalItemsQty = 0;
        $isBaseOnQty = false;
        foreach ($items as $item) {
            if ($item->getParentItemId())
                continue;
            if ($item->getHasChildren() && $item->isChildrenCalculated()) {
                foreach ($item->getChildren() as $child) {
                    $baseItemsPrice += $item->getQty() * ($child->getQty() * $child->getBasePriceInclTax()) - $child->getBaseDiscountAmount() - $child->getMagestoreBaseDiscount();
                    $totalItemsQty += $item->getQty() * $child->getQty();
                }
            } elseif ($item->getProduct()) {
                $baseItemsPrice += $item->getQty() * $item->getBasePriceInclTax() - $item->getBaseDiscountAmount() - $item->getMagestoreBaseDiscount();
                $totalItemsQty += $item->getQty();
            }
        }
        $earnpointsForShipping = Mage::getStoreConfig(
            Magestore_RewardPoints_Helper_Calculation_Earning::XML_PATH_EARNING_BY_SHIPPING, $address->getQuote()->getStoreId()
        );
        if ($earnpointsForShipping) {
            $baseItemsPrice += $address->getBaseShippingAmount() + $address->getBaseShippingTaxAmount() - $address->getMagestoreBaseDiscountForShipping();
        }
        if ($baseItemsPrice < 0.0001) {
            $isBaseOnQty = true;
        }

        // Update for items
        $deltaRound = 0; //Brian
        foreach ($items as $item) {
            if ($item->getParentItemId()) continue;
            if ($item->getHasChildren() && $item->isChildrenCalculated()) {
                foreach ($item->getChildren() as $child) {
                    $baseItemPrice = $item->getQty() * ($child->getQty() * $child->getBasePriceInclTax()) - $child->getBaseDiscountAmount() - $child->getMagestoreBaseDiscount();
                    $itemQty = $item->getQty() * $child->getQty();
                    if ($isBaseOnQty) {
                        $realItemEarning = $itemQty * $earningPoints / $totalItemsQty + $deltaRound;
                    } else {
                        $realItemEarning = $baseItemPrice * $earningPoints / $baseItemsPrice + $deltaRound;
                    }
                    $itemEarning = Mage::helper('rewardpoints/calculator')->round($realItemEarning);
                    $deltaRound = $realItemEarning - $itemEarning;
                    $child->setRewardpointsEarn($itemEarning);
                }
            } elseif ($item->getProduct()) {
                $baseItemPrice = $item->getQty() * $item->getBasePriceInclTax() - $item->getBaseDiscountAmount() - $item->getMagestoreBaseDiscount();
                $itemQty = $item->getQty();
                if ($isBaseOnQty) {
                    $realItemEarning = $itemQty * $earningPoints / $totalItemsQty + $deltaRound;
                } else {
                    $realItemEarning = $baseItemPrice * $earningPoints / $baseItemsPrice + $deltaRound;
                }
                $itemEarning = Mage::helper('rewardpoints/calculator')->round($realItemEarning);
                $deltaRound = $realItemEarning - $itemEarning;
                $item->setRewardpointsEarn($itemEarning);
            }
        }

        return $this;
    }

}
