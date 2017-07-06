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

class AW_Eventdiscount_Block_Adminhtml_Timer_Edit_Tab_Statistics extends Mage_Adminhtml_Block_Widget
{
    public $statistics = null;

    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('aw_eventdiscount/statistics.phtml');
        $this->getStatistics();
    }

    public  function  getStatistics()
    {
        $_statInProgressIndex = AW_Eventdiscount_Model_Source_Trigger_Status::IN_PROGGRESS;
        $_statMissedIndex = AW_Eventdiscount_Model_Source_Trigger_Status::MISSED;
        $_statUsedIndex = AW_Eventdiscount_Model_Source_Trigger_Status::USED;
        if (is_null($this->statistics)) {
            $this->statistics = array();
            $this->statistics[$_statInProgressIndex] =
            $this->statistics[$_statMissedIndex] =
            $this->statistics[$_statUsedIndex] = 0;
            $this->statistics['run_count'] =   $this->statistics['amount']=0;

            $limit = null;
            $timerId = $this->getRequest()->getParam('id');
            /** @var $collection AW_Eventdiscount_Model_Resource_Trigger_Collection */
            $collection = Mage::getModel('aweventdiscount/trigger')->getCollection();
            $collection->joinWithTimer();
            $collection->addTimerIdFilter($timerId);
            foreach ($collection as $item) {
                if (empty($this->statistics['limit'])) {
                    $this->statistics['limit'] = $item->getLimit();
                }
                if (empty($this->statistics['limit_per_customer'])) {
                    $this->statistics['limit_per_customer'] = $item->getLimitPerCustomer();
                }
                if (empty($this->statistics['run_count'])) {
                    $this->statistics['run_count'] = 0;
                }
                $this->statistics['run_count']++;
                if (empty($this->statistics['amount'])) {
                    $this->statistics['amount'] = 0;
                }
                if ($item->getTriggerStatus() == $_statUsedIndex) {
                    foreach (unserialize($item->getData('action')) as $action) {
                        if ($action['type'] != AW_Eventdiscount_Model_Source_Action::CHANGE_GROUP) {
                            $this->statistics['amount'] += floatval($action['action']);
                        }
                    }
                }
                switch ($item->getTriggerStatus()) {
                    case $_statInProgressIndex:
                        $this->statistics[$_statInProgressIndex]++;
                    break;
                    case $_statMissedIndex :
                        $this->statistics[$_statMissedIndex]++;
                    break;
                    case $_statUsedIndex :
                        $this->statistics[$_statUsedIndex]++;
                    break;
                }
            }
        }
        $this->statistics[$_statInProgressIndex . '_percent'] =
        $this->statistics[$_statUsedIndex . '_percent'] =
        $this->statistics[$_statMissedIndex . '_percent'] = 0;
        if (!empty($this->statistics['run_count'])) {
            $this->statistics[$_statInProgressIndex . '_percent'] =
                round($this->statistics[$_statInProgressIndex] / $this->statistics['run_count'] * 100);
            $this->statistics[$_statUsedIndex . '_percent'] =
                round($this->statistics[$_statUsedIndex] / $this->statistics['run_count'] * 100);
            $this->statistics[$_statMissedIndex . '_percent'] =
                round($this->statistics[$_statMissedIndex] / $this->statistics['run_count'] * 100);
        }

        $this->statistics['amount'] = Mage_Core_Helper_Data::currency($this->statistics['amount']);
        if (!$collection->getSize()) {
            $_data = Mage::registry('timer_data');
            $this->statistics['limit_per_customer'] = @$_data->getData('limit_per_customer');
            $this->statistics['limit'] = @$_data->getData('limit');
        }
        $this->addData($this->statistics);
        return $this;
    }
}