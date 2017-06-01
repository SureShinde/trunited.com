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

class AW_Eventdiscount_Model_Resource_Trigger_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    protected $_joinedWithTimer = null;

    public function _construct()
    {
        parent::_construct();
        $this->_init('aweventdiscount/trigger');
    }

    public function addStoreIdFilter($id = null)
    {
        if (!is_null($id)) {
            $this->getSelect()->where('find_in_set(?, store_ids) or find_in_set(0, store_ids)', $id);
        }
        return $this;
    }

    public function joinWithTimer()
    {
        if (is_null($this->_joinedWithTimer)) {
            $timerTableName = $this->getTable('aweventdiscount/timer');
            $this->getSelect()->joinLeft(array('timer_table' => $timerTableName),
                'main_table.timer_id = timer_table.id',
                array('title' => 'title',
                    'notice' => 'notice',
                    'color' => 'color',
                    'ext_url' => 'url',
                    'url_type' => 'url_type',
                    'design' => 'design',
                    'position' => 'position',
                    'limit' => 'limit',
                    'limit_per_customer' => 'limit_per_customer',
                    'conditions_serialized' => 'conditions_serialized',
                    'appearing' => 'appearing'
                )
            );
        }
        return $this;
    }

    public function loadByTimerId($timerId)
    {
        if ($timerId > 0) {
            $this->addFieldToFilter('timer_id', array('eq' => $timerId));
            return $this;
        }
        return $this;
    }

    public function addTimerIdFilter($ids)
    {
        $this->addFieldToFilter('timer_id', array('in' => $ids));
        return $this;
    }

    public function addCustomerIdFilter($customerId = null)
    {
        if (!is_null($customerId)) {
            $this->addFieldToFilter('customer_id', array('eq' => $customerId));
        }
        return $this;
    }

    public function addStatusFilter($filter = AW_Eventdiscount_Model_Source_Trigger_Status::IN_PROGGRESS)
    {
        $this->addFieldToFilter('trigger_status', array('eq' => $filter));
        return $this;
    }

    public function  addTimeLimitFilter($dateTime = null)
    {
        if (is_null($dateTime))
            $dateTime = Mage::getModel('core/date')->gmtDate();
        $this->addFieldToFilter('main_table.created_at', array('lteq' => $dateTime));
        $this->addFieldToFilter('main_table.active_to', array('gteq' => $dateTime));
    }

    public function checkActiveTimer($timerId = null, $customerId = null)
    {
        if (!is_null($customerId) && !is_null($timerId)) {
            $this->addCustomerIdFilter($customerId);
            $this->addTimerIdFilter($timerId);
            $this->addStatusFilter();
        }
        return $this;
    }

    public function addEventFilter($filter = null)
    {
        if (!is_null($filter)) {
            $this->addFieldToFilter('main_table.trigger_event', array('eq' => $filter));
        }
        return $this;
    }

    public function addNotLoadIdFilter($filter = null)
    {
        if (!is_null($filter)) {
            $this->addFieldToFilter('main_table.id', array('nin' => $filter));
        }
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

    public function deleteByTimerId($timerId)
    {
        if ($timerId > 0) {
            $collection = clone $this;
            $collection->addFieldToFilter('timer_id', array('eq' => $timerId));

            foreach ($collection as $item) {
                $item->delete();
            }
            return $this;
        }
        return $this;
    }
}