<?php

/**
 * Magestore
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the Magestore.com license that is
 * available through the world-wide-web at this URL:
 * http://www.magestore.com/license-agreement.html
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category    Magestore
 * @package     Magestore_RewardPointsEvent
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * Rewardpointsevent Grid Block
 * 
 * @category    Magestore
 * @package     Magestore_RewardPointsEvent
 * @author      Magestore Developer
 */
class Magestore_RewardPointsEvent_Block_Adminhtml_Rewardpointsevent_Grid extends Mage_Adminhtml_Block_Widget_Grid {

    public function __construct() {
        parent::__construct();
        $this->setId('rewardpointseventGrid');
        $this->setDefaultSort('event_id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
    }

    /**
     * prepare collection for block to display
     *
     * @return Magestore_RewardPointsEvent_Block_Adminhtml_Rewardpointsevent_Grid
     */
    protected function _prepareCollection() {
        $collection = Mage::getModel('rewardpointsevent/rewardpointsevent')->getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * prepare columns for this grid
     *
     * @return Magestore_RewardPointsEvent_Block_Adminhtml_Rewardpointsevent_Grid
     */
    protected function _prepareColumns() {
        $this->addColumn('event_id', array(
            'header' => Mage::helper('rewardpointsevent')->__('ID'),
            'align' => 'right',
            'width' => '50px',
            'index' => 'event_id',
        ));

        $this->addColumn('title', array(
            'header' => Mage::helper('rewardpointsevent')->__('Title'),
            'align' => 'left',
            'index' => 'title',
        ));


        $this->addColumn('customer_apply', array(
            'header' => Mage::helper('rewardpointsevent')->__('Application Scope'),
            'align' => 'left',
            'width' => '150px',
            'index' => 'customer_apply',
            'type' => 'options',
            'options' => array(
                0 => Mage::helper('rewardpointsevent')->__('Choose Global '),
                1 => Mage::helper('rewardpointsevent')->__('Filter by Websites and Customer Groups'),
                2 => Mage::helper('rewardpointsevent')->__('Configure Customer Conditions'),
                3 => Mage::helper('rewardpointsevent')->__('Import from a CSV file'),
            ),
        ));
        $this->addColumn('point_amount', array(
            'header' => Mage::helper('rewardpointsevent')->__('Point Amount'),
            'width' => '40px',
            'index' => 'point_amount',
            'align' => 'center',
            'type' => 'number',
        ));
//        $this->addColumn('apply_from', array(
//            'header' => Mage::helper('rewardpointsevent')->__('Effective from'),
//            'align' => 'left',
//            'index' => 'apply_from',
//            'format' => 'yyyy/MM/dd',
//            'type' => 'date',
//        ));
//        $this->addColumn('apply_to', array(
//            'header' => Mage::helper('rewardpointsevent')->__('Effective to'),
//            'align' => 'left',
//            'index' => 'apply_to',
//            'format' => 'yyyy/MM/dd',
//            'type' => 'date',
//        ));

        $this->addColumn('repeat_type', array(
            'header' => Mage::helper('rewardpointsevent')->__('Event repeated'),
            'align' => 'left',
            'width' => '80px',
            'index' => 'repeat_type',
            'type' => 'options',
            'options' => Mage::getModel('rewardpointsevent/repeattype')->getOptionArray(),
        ));
        $this->addColumn('status', array(
            'header' => Mage::helper('rewardpointsevent')->__('Status'),
            'align' => 'left',
            'width' => '80px',
            'index' => 'status',
            'type' => 'options',
            'options' => array(
                1 => Mage::helper('rewardpointsevent')->__('Enabled'),
                2 => Mage::helper('rewardpointsevent')->__('Disabled'),
            ),
        ));
        $this->addColumn('is_running', array(
            'header' => Mage::helper('rewardpointsevent')->__('Is Running'),
            'align' => 'left',
            'width' => '100px',
            'index' => 'is_running',
            'type' => 'options',
            'options' => array(
                1 => Mage::helper('rewardpointsevent')->__('Yes'),
                0 => Mage::helper('rewardpointsevent')->__('No'),
            ),
            'renderer' => 'rewardpointsevent/adminhtml_rewardpointsevent_running',
        ));
        $this->addColumn('apply_success', array(
            'header' => Mage::helper('rewardpointsevent')->__('Event applied for(times)'),
            'width' => '40px',
            'index' => 'apply_success',
            'align' => 'center',
            'type' => 'number',
        ));
        $this->addColumn('created_time', array(
            'header' => Mage::helper('rewardpointsevent')->__('Created Time'),
            'align' => 'left',
            'index' => 'created_time',
//            'format' => 'dd/MM/yyyy',
            'type' => 'datetime',
        ));

        $this->addColumn('update_time', array(
            'header' => Mage::helper('rewardpointsevent')->__('Update Time'),
            'align' => 'left',
            'index' => 'update_time',
//            'format' => 'dd/MM/yyyy',
            'type' => 'datetime',
        ));
        $this->addColumn('action', array(
            'header' => Mage::helper('rewardpointsevent')->__('Action'),
            'width' => '80px',
            'type' => 'action',
            'getter' => 'getId',
            'actions' => array(
                array(
                    'caption' => Mage::helper('rewardpointsevent')->__('Edit'),
                    'url' => array('base' => '*/*/edit'),
                    'field' => 'id'
                )),
            'filter' => false,
            'sortable' => false,
            'index' => 'stores',
            'is_system' => true,
        ));

        $this->addExportType('*/*/exportCsv', Mage::helper('rewardpointsevent')->__('CSV'));
        $this->addExportType('*/*/exportXml', Mage::helper('rewardpointsevent')->__('XML'));

        return parent::_prepareColumns();
    }

    /**
     * prepare mass action for this grid
     *
     * @return Magestore_RewardPointsEvent_Block_Adminhtml_Rewardpointsevent_Grid
     */
    protected function _prepareMassaction() {
        $this->setMassactionIdField('event_id');
        $this->getMassactionBlock()->setFormFieldName('rewardpointsevent');

        $this->getMassactionBlock()->addItem('delete', array(
            'label' => Mage::helper('rewardpointsevent')->__('Delete'),
            'url' => $this->getUrl('*/*/massDelete'),
            'confirm' => Mage::helper('rewardpointsevent')->__('Are you sure?')
        ));

        $statuses = Mage::getSingleton('rewardpointsevent/status')->getOptionArray();
        array_unshift($statuses, array('label' => '', 'value' => ''));
        $this->getMassactionBlock()->addItem('status', array(
            'label' => Mage::helper('rewardpointsevent')->__('Change status'),
            'url' => $this->getUrl('*/*/massStatus', array('_current' => true)),
            'additional' => array(
                'visibility' => array(
                    'name' => 'status',
                    'type' => 'select',
                    'class' => 'required-entry',
                    'label' => Mage::helper('rewardpointsevent')->__('Status'),
                    'values' => $statuses
                ))
        ));
        return $this;
    }

    /**
     * get url for each row in grid
     *
     * @return string
     */
    public function getRowUrl($row) {
        return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }

}