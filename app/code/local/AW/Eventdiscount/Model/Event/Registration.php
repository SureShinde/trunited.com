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

class AW_Eventdiscount_Model_Event_Registration extends AW_Eventdiscount_Model_Event
{
    public function processEvent($event)
    {
        $newEvent= new Varien_Object();
        $newEvent->setData('customer', $event->getCustomer());
        $newEvent->setData('store_id', Mage::app()->getStore()->getId());
        $newEvent->setData('event_type', AW_Eventdiscount_Model_Event::REGISTRATION);
        $newEvent->setQuote(new Varien_Object());
        $this->collectTimersByEvent($newEvent);
        $this->filterByTrigger($newEvent);
        Mage::dispatchEvent('aweventdiscount_event_registration', $newEvent->toArray());
        $this->activateTriggers($newEvent);
    }

    public function customerSaveAfter($observer)
    {
        // Compatibility with Magento versions before 1.6.x
        $version = Mage::getConfig()->getModuleConfig("Mage_Customer")->version;
        if (version_compare( $version, '1.6.0.0', '<')) {
            /** @var Mage_Customer_Model_Customer $customer */
            $customer = $observer->getEvent()->getCustomer();
            if ($customer->isObjectNew() && !Mage::registry('aw_ed_current_customer')) {
                Mage::register('aw_ed_current_customer', $customer->getId());
                $event = new Varien_Event(array('customer' => $customer));
                $event->setName('customer_register_success');
                $this->processEvent($event);
            }
        }
    }

    public function redirectToCMS($observer)
    {
        $url = Mage::getUrl('promotion-registration');
        Mage::getSingleton('customer/session')->setBeforeAuthUrl($url);
        return;
    }
}