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

class AW_Eventdiscount_Model_Resource_Timer_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    protected $_joinedWithTrigger = null;

    public function _construct()
    {
        parent::_construct();
        $this->_init('aweventdiscount/timer');
    }

    public function addIdFilter($id)
    {
        $this->getSelect()->where('countdownid = ?', $id);
        return $this;
    }

    /**
     * Filters collection by store ids
     * @param null $stores
     * @param bool $breakOnAllStores
     *
     * @return $this
     */
    public function addStoreIdsFilter($stores = null, $breakOnAllStores = false)
    {
        $_stores = array(Mage::app()->getStore()->getId());
        if (is_string($stores))
            $_stores = explode(',', $stores);
        if (is_array($stores))
            $_stores = $stores;
        if (!in_array('0', $_stores))
            array_push($_stores, '0');
        if ($breakOnAllStores && $_stores == array(0))
            return $this;
        $_sqlString = '(';
        $i = 0;
        foreach ($_stores as $_store) {
            $_sqlString .= sprintf('find_in_set(%s, store_ids)', $this->getConnection()->quote($_store));
            if (++$i < count($_stores))
                $_sqlString .= ' OR ';
        }
        $_sqlString .= ')';
        $this->getSelect()->where($_sqlString);

        return $this;
    }

    public function addStoreIdFilter($id = null)
    {
        if (!is_null($id)) {
            $this->getSelect()->where('find_in_set(?, store_ids) or find_in_set(0, store_ids)', $id);
        }
        return $this;
    }

    public function addCustomerGroupFilter($id = null)
    {
        if (!is_null($id)) {
            $this->getSelect()->where('find_in_set(?, customer_group_ids)', $id);
        }
        return $this;
    }

    public function addEventTypeFilter($eventType = null)
    {
        if (!is_null($eventType)) {
            $this->addFieldToFilter('event', array('eq' => $eventType));
        }
    }

    public function addStatusFilter($status = AW_Eventdiscount_Model_Source_Timer_Status::ENABLED)
    {
        $this->addFieldToFilter('status', array('eq' => $status));
        return $this;
    }

    public function addActiveFromFilter($date = null)
    {
        if ($date === null)
            $date = now(true);
        $this->addFieldToFilter('active_from', array('lteq' => $date));
        return $this;
    }

    public function addActiveToFilter($date = null)
    {
        if ($date === null)
            $date = now(true);
        $this->addFieldToFilter('active_to', array('gteq' => $date));
        return $this;
    }

    public function orderByDateTo($direction)
    {
        $this->getSelect()->order('date_to', $direction);
        return $this;
    }

    /**
     * Covers bug in Magento function
     * @return Varien_Db_Select
     */
    public function getSelectCountSql()
    {
        $this->_renderFilters();
        $countSelect = clone $this->getSelect();
        return $countSelect->reset()->from($this->getSelect(), array())->columns('COUNT(*)');
    }

    protected function timeToString($timeDuration)
    {
        $duration[0] = intval($timeDuration / 3600);
        $duration[1] = intval(($timeDuration - $duration[0] * 3600) / 60);
        $duration[2] = intval($timeDuration - ($duration[0] * 3600 + $duration[1] * 60));
        if ($duration[0] < 10) $duration[0] = '0' . $duration[0];
        if ($duration[1] < 10) $duration[1] = '0' . $duration[1];
        if ($duration[2] < 10) $duration[2] = '0' . $duration[2];
        $result = $duration[0] . ':' . $duration[1] . ':' . $duration[2];
        return $result;
    }

    protected function _afterLoad()
    {
        $countTimer = array();
        $amount = array();
        $triggerModel = Mage::getModel('aweventdiscount/trigger');

        foreach ($triggerModel->getCollection() as $item) {
            if (!array_key_exists($item->getTimerId(), $countTimer)) {
                $countTimer[$item->getTimerId()] = 0;
            }

            $countTimer[$item->getTimerId()]++;
            if (!array_key_exists($item->getTimerId(), $amount)) {
                $amount[$item->getTimerId()] = 0;
            }
            if ($item->getTriggerStatus() != AW_Eventdiscount_Model_Source_Trigger_Status::USED) {
                continue;
            }
            foreach (unserialize($item->getData('action')) as $action) {
                if ($action['type'] == AW_Eventdiscount_Model_Source_Action::CHANGE_GROUP) {
                    continue;
                }
                $amount[$item->getTimerId()] += floatval($action['action']);
            }
        }

        foreach ($this as $item) {
            $item->addData(
                array(
                    'store_ids'          => explode(',', $item->getStoreIds()),
                    'customer_group_ids' => explode(',', $item->getCustomerGroupIds()),
                    'total_runs'         => isset($countTimer[$item->getId()]) ? $countTimer[$item->getId()] : 0,
                    'duration'           => $this->timeToString($item->getDuration()),
                    'amount'             => isset($amount[$item->getId()]) ? $amount[$item->getId()] : 0
                )
            );
        }
        parent::_afterLoad();
        return $this;
    }
}