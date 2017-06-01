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

class AW_Eventdiscount_Model_Event_Cartupdate extends AW_Eventdiscount_Model_Event
{
    protected function _calculateHach($quote)
    {
        $cartHach = '';
        foreach ($quote->getAllVisibleItems() as $item) {
            $cartHach .= 'sku{' . $item->getSku() . '}qty{' . $item->getQty() . '}' . PHP_EOL;
        }
        return md5($cartHach);
    }

    public function processEvent($observer)
    {
        if (!Mage::getSingleton('customer/session')->isLoggedIn()) {
            return;
        }
        if (Mage::app()->getFrontController()->getRequest()->getActionName() === 'saveOrder') {
            return;
        }
        $event = $observer->getEvent();
        $quote = $event->getItem()->getQuote();
        $newEvent = new Varien_Object();
        $newEvent->setData('customer', Mage::getSingleton('customer/session')->getCustomer());
        $newEvent->setData('store_id', Mage::app()->getStore()->getId());
        $newEvent->setData('event_type', AW_Eventdiscount_Model_Event::CARTUPDATE);
        $address = $this->_getAddress($event->getItem());
        $quote1 = clone $quote;
        $newEvent->setData('quote', $address);
        $this->collectTimersByEvent($newEvent);
        $quoteHash = $this->_calculateHach($quote1);
        if (is_null($oldQuote = Mage::getSingleton('customer/session')->getEventDiscountQuote())) {
            Mage::getSingleton('customer/session')->setEventDiscountQuote($quoteHash);
        } else {
            if ($this->_calculateHach($quote1) !== $oldQuote) {
                Mage::getSingleton('customer/session')->setEventDiscountQuote($quoteHash);
            }
        }
        $ignoreIds = array();
        $newEvent->setData('quote_hash', $quoteHash);
        if ($this->checkQuote($newEvent)) {
            $this->filterByTrigger($newEvent);
            $ignoreIds = $this->activateTriggers($newEvent);
        }
        $active = Mage::getModel('aweventdiscount/trigger')
            ->getActiveTriggers()
            ->addEventFilter(AW_Eventdiscount_Model_Event::CARTUPDATE)
        ;
        foreach ($active as $trigger) {
            $trigger->setData('quote_hash', $quoteHash)->save();
        }
        $this->killTriggers($newEvent, $ignoreIds);
    }

    public function killTriggers($event, $ignoreIds)
    {
        $triggerCollection = Mage::getModel('aweventdiscount/trigger')->getCollection();
        $triggerCollection->addCustomerIdFilter($event->getCustomer()->getId());
        $triggerCollection->addStatusFilter();
        $triggerCollection->addTimeLimitFilter();
        $triggerCollection->addEventFilter(AW_Eventdiscount_Model_Event::CARTUPDATE);
        $timerModel = Mage::getModel('aweventdiscount/timer');
        foreach ($triggerCollection as $item) {
            $timerModel->load($item->getTimerId());
            if ((count($ignoreIds) > 0) && (in_array($item->getId(), $ignoreIds))) continue;
            if (!$timerModel->validate($event->getQuote())
                || !Mage::getSingleton('checkout/session')->getQuote()->hasItems()
            ) {
                $item->setTriggerStatus(AW_Eventdiscount_Model_Source_Trigger_Status::MISSED);
                $item->save();
            }
        }
    }
}