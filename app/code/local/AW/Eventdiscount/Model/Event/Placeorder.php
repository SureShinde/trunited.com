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

class AW_Eventdiscount_Model_Event_Placeorder extends AW_Eventdiscount_Model_Event
{
    public function finishTriggers($ignoredIds, $order)
    {
        $triggers = Mage::getModel('aweventdiscount/trigger')->getActiveTriggers();
        foreach ($triggers as $trigger) {
            if ((count($ignoredIds) > 0) && (in_array($trigger->getId(), $ignoredIds))) {
                continue;
            }
            $customerSession = Mage::getSingleton('customer/session');
            $customer = $customerSession->getCustomer();
            $triggerAction = unserialize($trigger->getData('action'));
            foreach ($triggerAction as &$action) {
                //Process change group
                if ($action['type'] == AW_Eventdiscount_Model_Source_Action::CHANGE_GROUP) {
                    $customer->setGroupId($action['action'])->save();
                }

                //Process percent discount to fixed
                if ($action['type'] == AW_Eventdiscount_Model_Source_Action::PERCENT) {
                    $sessionActions = $customerSession->getData('eventdiscounts');

                    //Main logics is to find in session action with timer_id = $trigger->timer_id
                    //Check if action[type] is percent and set fixed amount to $trigger
                    foreach ($sessionActions as $sessAct) {
                        if ($sessAct['timer_id'] != $trigger->getTimerId() && $sessAct['type'] != $action['type']) {
                            continue;
                        }
                        $action['action'] = $sessAct['action'];
                    }
                }
            }
            $trigger
                ->setData('action', serialize($triggerAction))
                ->setTriggerStatus(AW_Eventdiscount_Model_Source_Trigger_Status::USED)
                ->save()
            ;

            /* Check if trigger type is promotion */
            if(strcasecmp($trigger->getData('trigger_event'), AW_Eventdiscount_Model_Event::PROMOTION) == 0) {
                Mage::helper('eventdiscount')->checkFinishPromotion($trigger, $order);
            } else if (strcasecmp($trigger->getData('trigger_event'), AW_Eventdiscount_Model_Event::LOGIN) == 0) {
                Mage::helper('eventdiscount')->checkFinishPromotion($trigger, $order, true);
            }
            /* END Check if trigger type is promotion */
        }
    }

    public function processEvent($observer)
    {
        $register = false;
        $quote = $observer->getEvent()->getOrder()->getQuote();
        if (!$quote) {
            $quote = Mage::getSingleton('checkout/session')->getQuote();
        }
        if ($quote && $quote->getData('checkout_method')
            === Mage_Sales_Model_Quote::CHECKOUT_METHOD_REGISTER
        ) {
            $register = true;
        }

        $event = $observer->getEvent();
        $order = $event->getOrder();
        $quote = Mage::getSingleton('checkout/session')->getQuote();
        $customer = Mage::getModel('customer/customer')->load($order->getCustomerId());

        if ($register || Mage::getSingleton('customer/session')->isLoggedIn()) {
            $newEvent = new Varien_Object();
            $newEvent->addData(
                array(
                    'customer'   => $customer,
                    'store_id'   => Mage::app()->getStore()->getId(),
                    'event_type' => AW_Eventdiscount_Model_Event::ORDER,
                    'quote'      => $this->_getAddress($quote)
                )
            );
            $this->collectTimersByEvent($newEvent);
            $ignoredIds = array();

            $this->filterByTrigger($newEvent);
            if ($this->checkQuote($newEvent)) {
               $ignoredIds = $this->activateTriggers($newEvent);
            }

            if(Mage::helper('eventdiscount')->isMetOrder($order)){
                $this->finishTriggers($ignoredIds, $observer->getEvent()->getOrder());
            }

        }
        if ($register) {
            Mage::dispatchEvent('customer_register_success',
                array('account_controller' => Mage::app()->getFrontController(), 'customer' => $customer)
            );
        }

    }
}