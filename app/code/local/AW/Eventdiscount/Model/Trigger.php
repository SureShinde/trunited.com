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

class AW_Eventdiscount_Model_Trigger extends Mage_Core_Model_Abstract
{
    const DATETIME_PHP_FORMAT       = 'Y-m-d H:i:s';

    const ACTION_ENABLE = '1';
    const ACTION_DISABLE = '0';
    const ON_START = '1';
    const ON_END = '0';
    protected $_session;

    public function _construct()
    {
        $this->_init('aweventdiscount/trigger');
    }

    public function getActiveTriggers()
    {
        $collection = $this->getCollection();
        $collection->addCustomerIdFilter(Mage::getSingleton('customer/session')->getCustomer()->getId());
        $collection->addStatusFilter();
        $collection->addTimeLimitFilter();
        return $collection;
    }

    public function getCreatedAtTimestamp()
    {
        $date = $this->getCreatedAt();
        if ($date) {
            return strtotime($date);
        }
        return null;
    }

    public function getActiveToTimestamp()
    {
        $date = $this->getActiveTo();
        if ($date) {
            return strtotime($date);
        }
        return null;
    }

    public function activate($timer,$event)
    {
        $actionCollection = Mage::getModel('aweventdiscount/action')->getCollection();
        $timerModel = Mage::getModel('aweventdiscount/timer')->load($timer->getId());
        $actionCollection->loadByTimerId($timerModel->getId());
        $data = array('timer_id' => $timerModel->getId(),
            'customer_id' => $event->getCustomer()->getId(),
            'created_at' => Mage::getModel('core/date')->gmtDate(),
            'duration' => $timerModel->getDuration(),
            'active_to' => gmdate(self::DATETIME_PHP_FORMAT, gmdate('U') + $timerModel->getDuration()),
            'status' => AW_Eventdiscount_Model_Source_Trigger_Status::IN_PROGGRESS,
            'action' => serialize($actionCollection->getData()),
            'quote_hash' => (!is_null($event->getData('quote_hash'))?$event->getData('quote_hash'):0),
            'trigger_event' => $event->getEventType(),
        );
        $this->addData($data);
        $this->save();
        Mage::helper('awcore/logger')->log(
            $timerModel,
            'Timer activated: ' . $timer->getTimerName(),
            null,
            'For customer with id: ' .  $event->getCustomer()->getId()
        );
        return $this->getId();
    }


}