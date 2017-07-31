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

class AW_Eventdiscount_Model_Event_Login extends AW_Eventdiscount_Model_Event
{
    public function processEvent($event)
    {
        $newEvent= new Varien_Object();
        $newEvent->setData('customer', $event->getCustomer());
        $newEvent->setData('store_id', Mage::app()->getStore()->getId());
        $newEvent->setData('event_type', AW_Eventdiscount_Model_Event::LOGIN);
        $newEvent->setData('quote', new Varien_Object());
        $this->collectTimersByEvent($newEvent);
        $this->filterByTrigger($newEvent);
        Mage::dispatchEvent('aweventdiscount_event_login', $newEvent->toArray());

        $collection = Mage::getModel('aweventdiscount/trigger')->getCollection()
            ->addFieldToFilter('customer_id', $event->getCustomer()->getId())
            ->addFieldToFilter('trigger_status', AW_Eventdiscount_Model_Source_Trigger_Status::IN_PROGGRESS)
            ->addFieldToFilter('trigger_event', array('in' => array(AW_Eventdiscount_Model_Event::PROMOTION, AW_Eventdiscount_Model_Event::REGISTRATION)))
        ;

        if($collection != null && sizeof($collection) > 0)
        {

        } else {
            $this->activateTriggers($newEvent);
        }
    }

    public function redirectToCMS($observer)
    {
        $customer_id = $observer->getCustomer()->getId();
        $collection = Mage::getModel('aweventdiscount/trigger')->getCollection()
            ->addFieldToFilter('customer_id', $customer_id)
            ->addFieldToFilter('trigger_status', AW_Eventdiscount_Model_Source_Trigger_Status::IN_PROGGRESS)
            ->addFieldToFilter('trigger_event', AW_Eventdiscount_Model_Event::LOGIN)
            ->setOrder('id', 'desc')
            ->getFirstItem()
        ;

        zend_debug::dump($collection != null && sizeof($collection->getData()) > 0);
        if($collection != null && sizeof($collection->getData()) > 0)
        {
            $timer = Mage::getModel('aweventdiscount/timer')->load($collection->getTimerId());
            zend_debug::dump($timer->debug());
            if($timer->getId())
            {
                Mage::getSingleton('customer/session')->setBeforeAuthUrl(Mage::getUrl($timer->getData('cms_page')));
            }
            return;
        }
        zend_debug::dump(Mage::getSingleton('customer/session')->getBeforeAuthUrl());
        exit;
    }
}