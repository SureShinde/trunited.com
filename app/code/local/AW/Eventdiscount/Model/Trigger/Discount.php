<?php
/**
 * aheadWorks Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://ecommerce.aheadworks.com/AW-LICENSE.txt
 *
 * =================================================================
 *                 MAGENTO EDITION USAGE NOTICE
 * =================================================================
 * This software is designed to work with Magento community edition and
 * its use on an edition other than specified is prohibited. aheadWorks does not
 * provide extension support in case of incorrect edition use.
 * =================================================================
 *
 * @category   AW
 * @package    AW_Eventdiscount
 * @version    1.0.5
 * @copyright  Copyright (c) 2010-2012 aheadWorks Co. (http://www.aheadworks.com)
 * @license    http://ecommerce.aheadworks.com/AW-LICENSE.txt
 */

class AW_Eventdiscount_Model_Trigger_Discount extends AW_Eventdiscount_Model_Trigger
{
    public function setDiscount($observer)
    {
        $session = Mage::getSingleton('customer/session');
        if (!Mage::getModel('customer/session')->isLoggedIn()) {
            return $this;
        }
        $triggers = $this->getActiveTriggers();

        $actions = array();
        foreach ($triggers as $trigger) {
            foreach (unserialize($trigger->getData('action')) as $action) {
                array_push($actions, $action);
            }
        }

        if (empty($actions)) {
            $session->unsetData('event_discount');
            return $this;
        }


        $quote = $observer->getEvent()->getQuote();

        //add one by one discounts
        $numberPromo = 1;

        foreach ($actions as &$action) {

            if ($action['type'] !== AW_Eventdiscount_Model_Source_Action::FIXED
                && $action['type'] !== AW_Eventdiscount_Model_Source_Action::PERCENT
            ) {
                continue;
            }

            $baseDiscount = $this->calculateDiscount($action['timer_id'], $action['type'], $action['action'], $quote);
            $awardPoint = $this->calculatePoint($action['timer_id'], $quote);

            if ($baseDiscount == 0) {
//                continue;
            }

            $action['action'] = $baseDiscount;
            $discount = Mage::app()->getStore()->convertPrice($baseDiscount);
            $formattedPrice = Mage::helper('core')->currency($baseDiscount, true, false);
            $discountDescription = Mage::helper('eventdiscount')->__('Discount Event')
                . (count($actions) > 1 ? '&nbsp;#' . $numberPromo++ : '')
                . '&nbsp;-&nbsp;' . $formattedPrice
            ;

            $result = $quote->getBaseSubtotalWithDiscount() - $baseDiscount;
            if ($result < 0) {
                $baseDiscount = $quote->getBaseSubtotalWithDiscount();
                $discount = Mage::app()->getStore()->convertPrice($baseDiscount);
            }
            $result = $quote->getBaseSubtotal() - $baseDiscount;
            if ($result < 0) {
                $baseDiscount = $quote->getBaseSubtotal();
                $discount = Mage::app()->getStore()->convertPrice($baseDiscount);
            }
            $quote
                ->setGrandTotal($quote->getGrandTotal() - $discount)
                ->setBaseGrandTotal($quote->getBaseGrandTotal() - $baseDiscount)
                ->setSubtotalWithDiscount($quote->getSubtotalWithDiscount() - $discount)
                ->setBaseSubtotalWithDiscount($quote->getBaseSubtotalWithDiscount() - $baseDiscount)
                ->save()
            ;
            $canAddItems = $quote->isVirtual() ? ('billing') : ('shipping');

            $timer = Mage::getModel('aweventdiscount/timer')->load($action['timer_id']);

            foreach ($quote->getAllAddresses() as $address) {
                $address
                    ->setSubtotal(0)
                    ->setBaseSubtotal(0)
                    ->setGrandTotal(0)
                    ->setBaseGrandTotal(0)
                ;

                if ($address->getAddressType() == $canAddItems) {
                    $address
                        ->setSubtotal((float)$quote->getSubtotal())
                        ->setBaseSubtotal((float)$quote->getBaseSubtotal())
                        ->setSubtotalWithDiscount((float)$quote->getSubtotalWithDiscount())
                        ->setBaseSubtotalWithDiscount((float)$quote->getBaseSubtotalWithDiscount())
                        ->setGrandTotal((float)$quote->getGrandTotal())
                        ->setBaseGrandTotal((float)$quote->getBaseGrandTotal())
                        ->setDiscountAmount($address->getDiscountAmount() - $discount)
                    ;
                    if ($address->getDiscountDescription()) {
                        $discountDescription = $address->getDiscountDescription() . ', ' . $discountDescription;
                    }

                    if($awardPoint['point'] > 0)
                    {
                        $session->setData('event_discount', array(
                            'amount' => $awardPoint['amount'],
                            'type' => $awardPoint['type'],
                            'text' => $timer->getData('text_promotion'),
                        ));
                        $address->setRewardpointsEarn($awardPoint['point']);
                    }


                    $address
                        ->setDiscountDescription($discountDescription)
                        ->setBaseDiscountAmount($address->getBaseDiscountAmount() - $baseDiscount)
                        ->save()
                    ;
                }
            }

            $total = $quote->getSubtotal();
            foreach ($quote->getAllItems() as $item) {
                //We apply discount amount based on the ratio between the GrandTotal and the RowTotal
                $ratio = $item->getRowTotal() / $total;
                $ratioDiscount = $discount * $ratio;
                $ratioBaseDiscount = $baseDiscount * $ratio;

                $item
                    ->setDiscountAmount($item->getDiscountAmount() + $ratioDiscount)
                    ->setBaseDiscountAmount($item->getBaseDiscountAmount() + $ratioBaseDiscount)
                    ->save()
                ;
            }
        }
        $session->setData('eventdiscounts', $actions);
        return $this;
    }

    public function calculateDiscount($timer_id, $type, $amount, $quote)
    {
        $discount = 0;
        $product_collection = Mage::helper('eventdiscount')->getTimerProductCollection($timer_id);

        if(sizeof($product_collection) > 0)
        {
            $product_ids = $product_collection->getColumnValues('product_id');

            foreach ($quote->getAllItems() as $item) {
                if(in_array($item->getProduct()->getId(), $product_ids))
                {
                    if($type === AW_Eventdiscount_Model_Source_Action::FIXED)
                        $discount += $amount * $item->getQty();
                    else
                        $discount += ($amount / 100) * $item->getQty() * $item->getPrice();
                }
            }
        }

        return $discount;
    }

    public function calculatePoint($timer_id, $quote)
    {
        $point = 0;
        $product_collection = Mage::helper('eventdiscount')->getTimerProductCollection($timer_id);
        $timer = Mage::getModel('aweventdiscount/timer')->load($timer_id);

        if(sizeof($product_collection) > 0)
        {
            $product_ids = $product_collection->getColumnValues('product_id');

            foreach ($quote->getAllItems() as $item) {
                if(in_array($item->getProduct()->getId(), $product_ids))
                {
                    $product_point = Mage::helper('rewardpointsrule/calculation_earning')->getCatalogItemEarningPoints($item);
                    if($timer->getPointType() == AW_Eventdiscount_Model_Source_Action::AWARD_POINT_FIXED)
                        $point += $timer->getPointAmount() * $item->getQty();
                    else if($timer->getPointType() == AW_Eventdiscount_Model_Source_Action::AWARD_POINT_PERCENT)
                        $point += ($timer->getPointAmount() / 100) * $item->getQty() * $product_point;
                }
            }
        }

        return array(
            'point' => $point,
            'type'  => $timer->getPointType(),
            'amount'  => $timer->getPointAmount(),
        );
    }
}