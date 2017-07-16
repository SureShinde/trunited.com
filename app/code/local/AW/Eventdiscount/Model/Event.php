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

class AW_Eventdiscount_Model_Event extends Mage_Core_Model_Abstract
{
    const ORDER = 'order';
    const LOGIN = 'login';
    const CARTUPDATE = 'cartupdate';
    const REGISTRATION = 'registration';
    const PROMOTION = 'promotion';

    public $timersCollection = null;

    protected function _getAddress($quote)
    {
        if ($quote instanceof Mage_Sales_Model_Quote_Address_Item) {
            $address = $quote->getAddress();
        } elseif ($quote instanceof Mage_Sales_Model_Quote) {
            if ($quote->isVirtual()) {
                $address = $quote->getBillingAddress();
            } else {
                $address = $quote->getShippingAddress();
            }
        } elseif ($quote->getQuote()->isVirtual()) {
            $address = $quote->getQuote()->getBillingAddress();
        } else {
            $address = $quote->getQuote()->getShippingAddress();
        }
        return $address;
    }

    public function eventsToArray()
    {
        return array(
            self::ORDER => Mage::helper('eventdiscount')->__('Place order'),
            self::CARTUPDATE => Mage::helper('eventdiscount')->__('Cart update'),
            self::LOGIN => Mage::helper('eventdiscount')->__('Log in'),
            self::REGISTRATION => Mage::helper('eventdiscount')->__('New registration'),
            self::PROMOTION => Mage::helper('eventdiscount')->__('Promotion Url'),
        );
    }

    /**
     * @param $event
     * @return object
     * First sorting step
     */
    public function collectTimersByEvent($event)
    {
        $collection = Mage::getModel('aweventdiscount/timer')->getCollection();
        //store filter
        $collection->addStoreIdFilter($event->getStoreId(), true);
        //customer group filter
        $collection->addCustomerGroupFilter($event->getCustomer()->getGroupId());
        // status filter. default enable
        $collection->addStatusFilter();
        //event type filter
        $collection->addEventTypeFilter($event->getEventType());
        //date filter
        $now = Mage::getModel('core/date')->gmtDate();
        $collection->addActiveFromFilter($now);
        $collection->addActiveToFilter($now);
        //add other filters
        return $this->timersCollection = $collection;
    }

    /**
     * @param $event
     * @return array
     * Second filtration step
     */
    public function filterByTrigger($event)
    {
        /** @var $triggerModel  AW_Eventdiscount_Model_Resource_Trigger_Collection*/
        $triggerModel = Mage::getModel('aweventdiscount/trigger');
        $inProgress = array();
        foreach ($triggerModel->getCollection() as $item) {
            if ($item->getTriggerStatus() != AW_Eventdiscount_Model_Source_Trigger_Status::IN_PROGGRESS) {
                continue;
            }
            if (($item->getCreatedAtTimestamp() + $item->getDuration()) < gmdate('U')) {
                $item->setData('trigger_status', AW_Eventdiscount_Model_Source_Trigger_Status::MISSED);
                $item->save();
                $timerModel = Mage::getModel('aweventdiscount/timer')->load($item->getTimerId());
                Mage::helper('awcore/logger')->log(
                    $timerModel,
                    'Timer missed: ' . $timerModel->getTimerName(),
                    null,
                    'Customer id: ' .  $event->getCustomer()->getId()
                );
                continue;
            }
            $inProgress[] = $item->getTimerId();
        }

        $filtered = array();
        foreach ($this->timersCollection as $item) {
            $_collectionSize = $triggerModel->getCollection()
                ->joinWithTimer()
                ->addStoreIdFilter($item->getId())->getSize()
            ;
            if ((intval($item->getLimit()) > 0) && (intval($item->getLimit()) <= intval($_collectionSize))) {
                Mage::helper('awcore/logger')->log(
                    $triggerModel,
                    'Limit record: ' . $item->getLimit(),
                    null,
                    'Total record(s): ' . $triggerModel->getCollection()->getSize()
                );
                continue;
            }

            if ((intval($item->getLimitPerCustomer()) > 0)
                && (intval($item->getLimitPerCustomer()) <= intval($triggerModel->getCollection()
                ->addTimerIdFilter($item->getId())
                ->addCustomerIdFilter($event->getCustomer()->getId())
                ->getSize()))
            ) {
                Mage::helper('awcore/logger')->log(
                    $triggerModel,
                    'Limit per customer' . $item->getLimitPerCustomer(),
                    null,
                    'Total Customer record(s): ' . $triggerModel->getCollection()
                        ->addCustomerIdFilter($event->customer->getEntityId())->getSize()
                );
                continue;
            }

            $_collectionSize = $triggerModel->getCollection()
                ->checkActiveTimer($item->getId(), $event->getCustomer()->getId())
                ->getSize()
            ;
            if ($_collectionSize > 0) {
                Mage::helper('awcore/logger')->log(
                    $triggerModel,
                    'Duplicate trigger',
                    null,
                    'Event: Register Customer, Timer Id: ' . $item->getId() . ', Customer Id: '
                    . $event->customer->getEntityId()
                );
                continue;
            }
            $filtered[] = $item;
        }
        $this->timersCollection = $filtered;
        return $this->timersCollection = $filtered;
    }

    /**
     * @param $event
     * @return array|bool
     * Optional (event type related) sorting step
     */
    public function checkQuote($event)
    {
       if (is_null($this->timersCollection)) return false;
        $filtered = array();
        foreach ($this->timersCollection as $item) {
            $condModel = Mage::getModel('aweventdiscount/timer')->load($item->getId());
            $conditionsResult = $condModel->validate($event->getData('quote'));
            if ($conditionsResult) {
                $filtered[] = $item;
            }
        }
        return $this->timersCollection = $filtered;
    }

    /**
     * Activate triggers by timers
     * @param $event
     *
     * @return array
     */
    public function activateTriggers($event)
    {
        $activated = array();
        foreach ($this->timersCollection as $item) {
            if (!is_null($event->getData('quote_hash'))) {
                $collection = Mage::getModel('aweventdiscount/trigger')->getCollection()
                    ->addFieldToFilter('quote_hash', array('eq' => $event->getData('quote_hash')))
                    ->addFieldToFilter('customer_id', $event->getCustomer()->getId())
                    ->addFieldToFilter('timer_id', $item->getId());
                if ($collection->getSize() > 0)
                    break;
            }
            array_push($activated, Mage::getModel('aweventdiscount/trigger')->activate($item, $event));
        }
        return $activated;
    }
}