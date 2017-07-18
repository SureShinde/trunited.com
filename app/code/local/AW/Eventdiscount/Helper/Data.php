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
class AW_Eventdiscount_Helper_Data extends Mage_Core_Helper_Abstract
{
    public static function customerGroupsToArray()
    {
        $customerGroups = Mage::getResourceModel('customer/group_collection')
            ->addFieldToFilter('customer_group_id', array('gt' => 0))
            ->load()->toOptionArray();
        return $customerGroups;
    }

    public function isEditAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('eventdiscount/timer/new');
    }

    public function isViewAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('eventdiscount/timer/list');
    }

    public function getExtDisabled()
    {
        return Mage::getStoreConfig('advanced/modules_disable_output/AW_Eventdiscount');
    }

    public function getCustomerGroupId()
    {
        $_customerGroupId = 0;
        if (Mage::getSingleton('customer/session')->isLoggedIn()) {
            $_customerGroupId = Mage::getSingleton('customer/session')->getCustomerGroupId();
        }
        return $_customerGroupId;
    }

    public static function isNewRules()
    {
        if ((version_compare(Mage::getVersion(), '1.7.0.0', '>=')
                && Mage::helper('awall/versions')->getPlatform() == AW_ALL_Helper_Versions::CE_PLATFORM) ||
            (version_compare(Mage::getVersion(), '1.12.0.0', '>=')
                && Mage::helper('awall/versions')->getPlatform() == AW_ALL_Helper_Versions::EE_PLATFORM)
        ) {
            return true;
        }
        return false;
    }

    public function saveTimerProduct($timer_id, $product_ids)
    {
        $collection = $this->getTimerProductCollection($timer_id);

        $transactionSave = Mage::getModel('core/resource_transaction');
        $connection = Mage::getSingleton('core/resource')->getConnection('core_write');
        try {
            $connection->beginTransaction();

            if (sizeof($collection) > 0 && sizeof($product_ids) > 0) {
                foreach ($collection as $timer_product) {
                    $timer_product->delete();
                }
            }

            if (sizeof($product_ids) > 0) {
                foreach ($product_ids as $pid) {
                    $model = Mage::getModel('aweventdiscount/product');
                    $_data = array(
                        'timer_id' => $timer_id,
                        'product_id' => $pid
                    );
                    $model->setData($_data);
                    $transactionSave->addObject($model);
                }
                $transactionSave->save();
            }

            $connection->commit();
        } catch (Exception $e) {
            $connection->rollback();
        }
    }

    public function getTimerProductCollection($timer_id)
    {
        $collection = Mage::getModel('aweventdiscount/product')->getCollection()
            ->addFieldToFilter('timer_id', $timer_id)
            ->setOrder('timer_product_id', 'desc');

        return $collection;
    }

    public function getProductByTimerId($timer_id)
    {
        return $this->getTimerProductCollection($timer_id)->getColumnValues('product_id');
    }

    public function removeProductByTimerId($timer_id)
    {
        $collection = $this->getTimerProductCollection($timer_id);

        if (sizeof($collection) > 0) {
            foreach ($collection as $product) {
                $product->delete();
            }
        }
    }

    public function getTypeNotifyConfiguration($store = null)
    {
        return Mage::getStoreConfig('awall/eventdiscount/type_notify', $store);
    }

    public function getCMSPage($store = null)
    {
        return Mage::getStoreConfig('awall/eventdiscount/cms', $store);
    }

    public function checkProductCondition($timer_id, $items)
    {
        if (sizeof($items) <= 0 || !isset($timer_id))
            return false;

        $collection = Mage::getModel('aweventdiscount/product')->getCollection()
            ->addFieldToSelect('product_id')
            ->addFieldToFilter('timer_id', $timer_id);

        $products = $collection->getColumnValues('product_id');

        if (sizeof($collection) <= 0 || sizeof($products) <= 0)
            return false;

        $flag = false;
        foreach ($items as $item) {
            if (in_array($item->getProductId(), $products)) {
                $flag = true;
                break;
            }
        }

        return $flag;
    }

    public function saveCookie($accountCode, $expiredTime, $toTop = false)
    {
        $cookie = Mage::getSingleton('core/cookie');
        if ($expiredTime)
            $cookie->setLifeTime(intval($expiredTime) * 86400);

        $data = explode('-', $accountCode);
        if (sizeof($data) == 2) {
            $affiliate = Mage::getModel('affiliateplus/account')->load($accountCode);
            if (isset($affiliate) && $affiliate->getId()) {
                $cookie->set("promotion_code", $accountCode);
                $cookie->set("account_code", $data[1]);
                if (Mage::getSingleton('customer/session')->isLoggedIn())
                    $cookie->set("customer_id", Mage::getSingleton('customer/session')->getCustomer()->getId());
                else
                    $cookie->set("customer_id", -1);
            }
        }

        if ($toTop) {
            $cookie->set($accountCode, date('Y-m-d'));
        }
    }

    public function checkPromotionCookieAfterRegistering($customer)
    {
        $cookie = Mage::getSingleton('core/cookie');

        $this->runTrigger($customer);

        $customer_id = $customer->getId();

        $collection = Mage::getModel('aweventdiscount/trigger')->getCollection()
            ->addFieldToFilter('customer_id', $customer_id)
            ->addFieldToFilter('trigger_status', AW_Eventdiscount_Model_Source_Trigger_Status::IN_PROGGRESS)
            ->addFieldToFilter('trigger_event', AW_Eventdiscount_Model_Event::PROMOTION)
            ->setOrder('id', 'desc');

        if (sizeof($collection) > 0 && $cookie->get('promotion_code') && $cookie->get('account_code') && $cookie->get('customer_id') == -1) {
            $cookie->set("customer_id", $customer_id);
            $affiliate = Mage::getModel('affiliateplus/account')->loadByIdentifyCode($cookie->get('account_code'));
            if (isset($affiliate) && $affiliate->getId()) {
                try {
                    $transactionSave = Mage::getModel('core/resource_transaction');
                    foreach ($collection as $trigger) {
                        $trigger->setData('cookie', 1);
                        $trigger->setData('updated_at', now());
                        $trigger->setData('referrer_id', $affiliate->getCustomerId());
                        $transactionSave->addObject($trigger);
                    }
                    $transactionSave->save();
                } catch (Exception $ex) {
                }
            }
        }
    }

    public function runTrigger($customer)
    {
        $eventModel = Mage::getModel('aweventdiscount/event');
        $newEvent = new Varien_Object();
        $newEvent->setData('customer', $customer);
        $newEvent->setData('store_id', Mage::app()->getStore()->getId());
        $newEvent->setData('event_type', AW_Eventdiscount_Model_Event::PROMOTION);
        $newEvent->setData('quote', new Varien_Object());
        $eventModel->collectTimersByEvent($newEvent);
        $eventModel->filterByTrigger($newEvent);
        Mage::dispatchEvent('aweventdiscount_event_promotion', $newEvent->toArray());
        $eventModel->activateTriggers($newEvent);
    }

    public function checkShowPromotion($customer_id)
    {
        $collection = $collection = Mage::getModel('aweventdiscount/trigger')->getCollection()
            ->addFieldToFilter('customer_id', $customer_id)
            ->addFieldToFilter('trigger_status', AW_Eventdiscount_Model_Source_Trigger_Status::IN_PROGGRESS)
            ->addFieldToFilter('trigger_event', AW_Eventdiscount_Model_Event::PROMOTION)
            ->addFieldToFilter('cookie', 1)
            ->setOrder('id', 'desc');

        if (sizeof($collection) > 0)
            return true;
        else
            return false;
    }

    public function checkFinishPromotion($trigger, $order)
    {
        $gift_card_rewards = Mage::getModel('aweventdiscount/giftcard')->getCollection()
            ->addFieldToSelect('*')
            ->addFieldToFilter('timer_id', $trigger->getData('timer_id'));

        if (!isset($gift_card_rewards) || sizeof($gift_card_rewards) == 0)
            return;

        $customer = Mage::getModel('customer/customer')->load($trigger->getCustomerId());
        if(!$customer->getId())
            return;

        $product_timers = Mage::getModel('aweventdiscount/product')->getCollection()
            ->addFieldToFilter('timer_id', $trigger->getTimerId())
            ;

        if(sizeof($product_timers) == 0)
            return;

        $products = $product_timers->getColumnValues('product_id');

        $items = $order->getAllItems();
        $total = 0;
        foreach ($items as $item) {
            $product = Mage::getModel('catalog/product')->load($item->getProductId());
            if(!Mage::helper('trubox')->isInExclusionList($product) && in_array($product->getId(), $products))
            {
                $total += $item->getPrice() * $item->getQtyOrdered();
            }
        }

        $gift_card_rewards->getSelect()->where('amount_from >= '.$total.' or amount_to < '.$total.' or (amount_from <= '.$total.' and amount_to >= '.$total.')');
        $gift_card_rewards->getSelect()->order('amount_to', 'desc');
        $gift_card_rewards->getSelect()->limit(1);

        if(sizeof($gift_card_rewards) > 0)
        {
            $obj = $gift_card_rewards->getData();

            $data = array(
                'title' => Mage::helper('trugiftcard')->__('Received the rewards from order #<a href="' . Mage::getUrl('sales/order/view', array('order_id' => $order->getId())) . '">' . $order->getIncrementId() . '</a>'),
                'order_id' => $order->getEntityId(),
                'credit'   => $obj[0]['reward_new_customer']
            );
            $truGiftCardAccount = Mage::helper('trugiftcard/account')->updateCredit($trigger->getCustomerId(), $obj[0]['reward_new_customer']);
            Mage::helper('trugiftcard/transaction')->createTransaction(
                $truGiftCardAccount,
                $data,
                Magestore_TruGiftCard_Model_Type::TYPE_TRANSACTION_RECEIVE_REWARD_FROM_PROMOTION,
                Magestore_TruGiftCard_Model_Status::STATUS_TRANSACTION_COMPLETED
            );

            $_data = array(
                'title' => Mage::helper('trugiftcard')->__('Received the rewards when referred the customer ('.$customer->getEmail().') with order #<a href="' . Mage::getUrl('sales/order/view', array('order_id' => $order->getId())) . '">' . $order->getIncrementId() . '</a>'),
                'order_id' => $order->getEntityId(),
                'credit'   => $obj[0]['reward_referrer']
            );
            $truGiftCardAccountReferrer = Mage::helper('trugiftcard/account')->updateCredit($trigger->getReferrerId(), $obj[0]['reward_referrer']);
            Mage::helper('trugiftcard/transaction')->createTransaction(
                $truGiftCardAccountReferrer,
                $_data,
                Magestore_TruGiftCard_Model_Type::TYPE_TRANSACTION_RECEIVE_REWARD_FROM_REFERRED_PROMOTION,
                Magestore_TruGiftCard_Model_Status::STATUS_TRANSACTION_COMPLETED
            );


        }
    }
}