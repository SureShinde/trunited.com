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
 * @package     Magestore_RewardPointsCoupon
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * Rewardpointscoupon Grid Block
 * 
 * @category    Magestore
 * @package     Magestore_RewardPointsCoupon
 * @author      Magestore Developer
 */
class Magestore_RewardPointsCoupon_Block_Adminhtml_Rewardpointscoupon_Grid extends Mage_Adminhtml_Block_Widget_Grid {

    public function __construct() {
        parent::__construct();
        $this->setId('rewardpointscouponGrid');
        $this->setDefaultSort('coupon_id');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
    }

    /**
     * prepare collection for block to display
     *
     * @return Magestore_RewardPointsCoupon_Block_Adminhtml_Rewardpointscoupon_Grid
     */
    protected function _prepareCollection() {
        $collection = Mage::getModel('rewardpointscoupon/template')->getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * prepare columns for this grid
     *
     * @return Magestore_RewardPointsCoupon_Block_Adminhtml_Rewardpointscoupon_Grid
     */
    protected function _prepareColumns() {
        $this->addColumn('template_id', array(
            'header' => Mage::helper('rewardpointscoupon')->__('ID'),
            'align' => 'right',
            'width' => '50px',
            'index' => 'template_id',
        ));

        $this->addColumn('name', array(
            'header' => Mage::helper('rewardpointscoupon')->__('Title'),
            'align' => 'left',
            'index' => 'name',
        ));

        $this->addColumn('point_balance', array(
            'header' => Mage::helper('rewardpointscoupon')->__('Point Balance'),
            'width' => '150px',
            'index' => 'point_balance',
            'type' => 'number',
        ));
        $groups = Mage::getResourceModel('customer/group_collection')
                ->addFieldToFilter('customer_group_id', array('gt' => 0))
                ->load()
                ->toOptionHash();

//        $this->addColumn('customer_group_ids', array(
//            'header' => Mage::helper('rewardpointscoupon')->__('Group'),
////            'width' => '100',
//            'index' => 'customer_group_ids',
//            'type' => 'options',
//            'options' => $groups,
//        ));

        $this->addColumn('from_date', array(
            'header' => Mage::helper('rewardpointscoupon')->__('Starting Date'),
            'align' => 'left',
            'index' => 'from_date',
            'type' => 'datetime',
        ));

        $this->addColumn('to_date', array(
            'header' => Mage::helper('rewardpointscoupon')->__('Expiring Date'),
            'align' => 'left',
            'index' => 'to_date',
            'type' => 'datetime',
        ));
        if (!Mage::app()->isSingleStoreMode()) {
            $this->addColumn('website_ids', array(
                'header' => Mage::helper('rewardpointscoupon')->__('Website'),
                'align' => 'center',
                'width' => '80px',
                'type' => 'options',
                'options' => Mage::getSingleton('adminhtml/system_store')->getWebsiteOptionHash(true),
                'index' => 'website_ids',
            ));
        }
        $this->addColumn('status', array(
            'header' => Mage::helper('rewardpointscoupon')->__('Status'),
            'align' => 'left',
            'width' => '80px',
            'index' => 'status',
            'type' => 'options',
            'options' => array(
                1 => 'Active',
                0 => 'Inactive',
            ),
        ));

        $this->addColumn('action', array(
            'header' => Mage::helper('rewardpointscoupon')->__('Action'),
            'width' => '100',
            'type' => 'action',
            'getter' => 'getId',
            'actions' => array(
                array(
                    'caption' => Mage::helper('rewardpointscoupon')->__('Edit'),
                    'url' => array('base' => '*/*/edit'),
                    'field' => 'id'
                )),
            'filter' => false,
            'sortable' => false,
            'index' => 'stores',
            'is_system' => true,
        ));

        $this->addExportType('*/*/exportCsv', Mage::helper('rewardpointscoupon')->__('CSV'));
        $this->addExportType('*/*/exportXml', Mage::helper('rewardpointscoupon')->__('XML'));

        return parent::_prepareColumns();
    }

    /**
     * prepare mass action for this grid
     *
     * @return Magestore_RewardPointsCoupon_Block_Adminhtml_Rewardpointscoupon_Grid
     */
    protected function _prepareMassaction() {
        $this->setMassactionIdField('rewardpointscoupon_id');
        $this->getMassactionBlock()->setFormFieldName('rewardpointscoupon');

        $this->getMassactionBlock()->addItem('delete', array(
            'label' => Mage::helper('rewardpointscoupon')->__('Delete'),
            'url' => $this->getUrl('*/*/massDelete'),
            'confirm' => Mage::helper('rewardpointscoupon')->__('Are you sure?')
        ));


        $this->getMassactionBlock()->addItem('print', array(
            'label' => Mage::helper('rewardpointscoupon')->__('Print'),
            'url' => $this->getUrl('*/*/massPrint'),
        ));

        $statuses = Mage::getModel('rewardpointscoupon/status')->getOptionArray();
        $this->getMassactionBlock()->addItem('status', array(
            'label' => Mage::helper('rewardpointscoupon')->__('Change status'),
            'url' => $this->getUrl('*/*/massStatus', array('_current' => true)),
            'additional' => array(
                'visibility' => array(
                    'name' => 'status',
                    'type' => 'select',
                    'class' => 'required-entry',
                    'label' => Mage::helper('rewardpointscoupon')->__('Status'),
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
