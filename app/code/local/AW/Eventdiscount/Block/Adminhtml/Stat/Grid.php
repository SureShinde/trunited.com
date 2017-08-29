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

class AW_Eventdiscount_Block_Adminhtml_Stat_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('timerGrid');
        $this->setDefaultSort('id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getModel('aweventdiscount/timer')->getCollection();
        $this->setCollection($collection);
        parent::_prepareCollection();
        $this->addDiscountsStats();
        return $this;
    }

    protected function _prepareColumns()
    {
        $helper = Mage::helper('eventdiscount');
        $this->addColumn('id', array(
            'header' => $helper->__('ID'),
            'align'  => 'right',
            'width'  => '5',
            'index'  => 'id'
        ));
        $this->addColumn('timer_name', array(
            'header' => $helper->__('Timer name'),
            'align'  => 'left',
            'index'  => 'timer_name'
        ));
        $this->addColumn('status', array(
            'header'  => $helper->__('Status'),
            'align'   => 'center',
            'width'   => '80px',
            'index'   => 'status',
            'type'    => 'options',
            'options' => Mage::getModel('aweventdiscount/source_timer_status')->toOptionArray(),
        ));
        $outputFormat = Mage::app()->getLocale()->getDateTimeFormat(Mage_Core_Model_Locale::FORMAT_TYPE_MEDIUM);
        $this->addColumn('active_from', array(
            'header' => $helper->__('Active from'),
            'index'  => 'active_from',
            'type'   => 'datetime',
            'format' => $outputFormat,
        ));
        $this->addColumn('active_to', array(
            'header' => $helper->__('Active to'),
            'index'  => 'active_to',
            'type'   => 'datetime',
            'format' => $outputFormat,
        ));
        $this->addColumn('duration', array(
            'header' => $helper->__('Duration'),
            'align'  => 'left',
            'index'  => 'duration',
            'width'  => '100px',
        ));
        $this->addColumn('event', array(
            'header'  => $helper->__('Event type'),
            'align'   => 'left',
            'index'   => 'event',
            'type'    => 'options',
            'options' => Mage::getModel('aweventdiscount/event')->eventsToarray(),
        ));
        $this->addColumn('store_ids', array(
            'header' => $helper->__('Store'),
            'align'  => 'left',
            'index'  => 'store_ids',
            'type'   => 'store'
        ));
        $groups = Mage::getResourceModel('customer/group_collection')
            ->addFieldToFilter('customer_group_id', array('gt' => 0)) //remove record "no logged in"
            ->load()
            ->toOptionHash()
        ;
        $this->addColumn('customer_group_ids', array(
            'header'                    => $helper->__('Customer Group'),
            'align'                     => 'left',
            'index'                     => 'customer_group_ids',
            'type'                      => 'options',
            'options'                   => $groups,
            'filter_condition_callback' => array($this, '_filterGroupCondition'),
            'width'                     => '150px',
        ));
        $this->addColumn('used', array(
            'header'   => $helper->__('Used'),
            'align'    => 'left',
            'index'    => 'used',
            'filter'   => false,
            'sortable' => false,
            'width'    => '50px',
        ));
        $this->addColumn('missed', array(
            'header'   => $helper->__('Missed'),
            'align'    => 'left',
            'index'    => 'missed',
            'filter'   => false,
            'sortable' => false,
            'width'    => '50px',
        ));
        $this->addColumn('in_progress', array(
            'header'   => $helper->__('Active'),
            'align'    => 'left',
            'index'    => 'in_progress',
            'filter'   => false,
            'sortable' => false,
            'width'    => '50px',
        ));
        $this->addColumn('total_runs', array(
            'header'   => $helper->__('Total Runs'),
            'align'    => 'left',
            'index'    => 'total_runs',
            'filter'   => false,
            'sortable' => false,
            'width'    => '50px',
        ));
        $this->addColumn('amount', array(
            'header'        => $helper->__('Discounts earned in base currency'),
            'align'         => 'left',
            'index'         => 'amount',
            'filter'        => false,
            'sortable'      => false,
            'type'          => 'currency',
            'default'       => Mage::app()->getLocale()->currency(Mage::app()->getBaseCurrencyCode())->toCurrency(0),
            'currency_code' => Mage::app()->getBaseCurrencyCode(),
        ));
        $this->addExportType('*/*/exportCsv', Mage::helper('eventdiscount')->__('CSV'));
        $this->addExportType('*/*/exportXml', Mage::helper('eventdiscount')->__('Excel XML'));
        return parent::_prepareColumns();
    }

    protected function _filterGroupCondition($collection, $column)
    {
        $value = $column->getFilter()->getValue();
        $this->getCollection()->addCustomerGroupFilter($value);
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('*/aweventdiscount_timer/edit/',
            array('id' => $row->getId(), 'tab' => 'timer_tabs_statistics', 'back' => 'stat')
        );
    }

    public function addDiscountsStats()
    {
        foreach ($this->getCollection() as $item) {
            $item->setData('used', 0);
            $item->setData('in_progress', 0);
            $item->setData('missed', 0);
            $triggers= Mage::getModel('aweventdiscount/trigger')->getCollection()->loadByTimerId($item->getId());
            foreach ($triggers as $trigger) {
                if ($trigger->getTriggerStatus() == AW_Eventdiscount_Model_Source_Trigger_Status::MISSED) {
                    $item->setData('missed', $item->getData('missed') + 1);
                }
                if ($trigger->getTriggerStatus() == AW_Eventdiscount_Model_Source_Trigger_Status::IN_PROGGRESS) {
                    $item->setData('in_progress', $item->getData('in_progress') + 1);
                }
                if ($trigger->getTriggerStatus() == AW_Eventdiscount_Model_Source_Trigger_Status::USED) {
                    $item->setData('used', $item->getData('used') + 1);
                }
            }
        }
    }
}