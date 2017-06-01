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

class AW_Eventdiscount_Block_Adminhtml_Timer_Grid extends Mage_Adminhtml_Block_Widget_Grid
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
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $helper = Mage::helper('eventdiscount');

        $this->addColumn('id', array(
            'header' => $helper->__('ID'),
            'align' => 'right',
            'width' => '5',
            'index' => 'id'
        ));

        $this->addColumn('timer_name', array(
            'header' => $helper->__('Timer name'),
            'align' => 'left',
            'index' => 'timer_name'
        ));

        $this->addColumn('status', array(
            'header' => $helper->__('Status'),
            'align' => 'center',
            'width' => '80px',
            'index' => 'status',
            'type' => 'options',
            'options' => Mage::getModel('aweventdiscount/source_timer_status')->toOptionArray(),
        ));
        $outputFormat = Mage::app()->getLocale()->getDateTimeFormat(Mage_Core_Model_Locale::FORMAT_TYPE_MEDIUM);

        $this->addColumn('active_from', array(
            'header' => $helper->__('Active from'),
            'index' => 'active_from',
            'type' => 'datetime',
            'format' => $outputFormat,
        ));

        $this->addColumn('active_to', array(
            'header' => $helper->__('Active to'),
            'index' => 'active_to',
            'type' => 'datetime',
            'format' => $outputFormat,
        ));

        $this->addColumn('design', array(
            'header' => $helper->__('Design'),
            'align' => 'left',
            'index' => 'design',
            'type' => 'options',
            'options' => Mage::getModel('aweventdiscount/source_design')->toOptionArray()
        ));

        $this->addColumn('event', array(
            'header' => $helper->__('Event'),
            'align' => 'left',
            'index' => 'event',
            'type' => 'options',
            'options' => Mage::getModel('aweventdiscount/event')->eventsToArray()
        ));
        return parent::_prepareColumns();
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('id');
        $this->getMassactionBlock()->setFormFieldName('id');

        $this->getMassactionBlock()->addItem('delete', array(
            'label' => Mage::helper('eventdiscount')->__('Delete'),
            'url' => $this->getUrl('*/*/massDelete'),
            'confirm' => Mage::helper('eventdiscount')->__('Are you sure?')
        ));

        $statuses = Mage::getSingleton('aweventdiscount/source_timer_status')->toOptionArray();
        $this->getMassactionBlock()->addItem('status', array(
            'label' => Mage::helper('eventdiscount')->__('Change status'),
            'url' => $this->getUrl('*/*/massStatus', array('_current' => true)),
            'additional' => array(
                'visibility' => array(
                    'name' => 'status',
                    'type' => 'select',
                    'class' => 'required-entry',
                    'label' => Mage::helper('eventdiscount')->__('Status'),
                    'values' => $statuses
                )
            ),
            'confirm' => Mage::helper('eventdiscount')->__('Are you sure?'),
        ));
        return $this;
    }
}