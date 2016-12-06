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
 * @package     Magestore_RewardPointsTransfer
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * Rewardpointstransfer Grid Block
 * 
 * @category    Magestore
 * @package     Magestore_RewardPointsTransfer
 * @author      Magestore Developer
 */
class Magestore_RewardPointsTransfer_Block_Adminhtml_Rewardpointstransfer_Grid extends Mage_Adminhtml_Block_Widget_Grid {

    public function __construct() {
        parent::__construct();
        $this->setId('rewardpointstransferGrid');
        $this->setDefaultSort('transfer_id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
    }

    /**
     * prepare collection for block to display
     *
     * @return Magestore_RewardPointsTransfer_Block_Adminhtml_Rewardpointstransfer_Grid
     */
    protected function _prepareCollection() {
        $collection = Mage::getModel('rewardpointstransfer/rewardpointstransfer')->getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * prepare columns for this grid
     *
     * @return Magestore_RewardPointsTransfer_Block_Adminhtml_Rewardpointstransfer_Grid
     */
    protected function _prepareColumns() {
        $this->addColumn('transfer_id', array(
            'header' => Mage::helper('rewardpointstransfer')->__('ID'),
            'align' => 'right',
            'width' => '50px',
            'index' => 'transfer_id',
        ));

        $this->addColumn('sender_email', array(
            'header' => Mage::helper('rewardpointstransfer')->__('Sender\'s Email'),
            'align' => 'left',
            'index' => 'sender_email',
            'renderer'  => 'rewardpointstransfer/adminhtml_rewardpointstransfer_renderer_sender',
        ));

        $this->addColumn('receiver_email', array(
            'header' => Mage::helper('rewardpointstransfer')->__('Recipient\'s Email'),
            'width' => '150px',
            'index' => 'receiver_email',
            'renderer'  => 'rewardpointstransfer/adminhtml_rewardpointstransfer_renderer_receiver',
        ));
         $this->addColumn('point_amount', array(
            'header' => Mage::helper('rewardpointstransfer')->__('Point Amount Transferred'),
            'width' => '150px',
            'index' => 'point_amount',
        ));
          $this->addColumn('created_time', array(
            'header'    => Mage::helper('rewardpoints')->__('Created On'),
            'index'     => 'created_time',
            'type'      => 'datetime',
        ));
        
        $this->addColumn('update_time', array(
            'header'    => Mage::helper('rewardpoints')->__('Updated On'),
            'index'     => 'update_time',
            'type'      => 'datetime',
        ));
        $this->addColumn('status', array(
            'header' => Mage::helper('rewardpointstransfer')->__('Status'),
            'align' => 'left',
            'width' => '80px',
            'index' => 'status',
            'type' => 'options',
            'options' => array(
                1 => Mage::helper('rewardpointstransfer')->__('Holding'),
                2 => Mage::helper('rewardpointstransfer')->__('Pending'),
                3 => Mage::helper('rewardpointstransfer')->__('Complete'),
                4 => Mage::helper('rewardpointstransfer')->__('Canceled'))
                )
        );
        $this->addColumn('store_id', array(
            'header'    => Mage::helper('rewardpointstransfer')->__('Store View'),
            'align'     => 'left',
            'index'     => 'store_id',
            'type'      => 'options',
            'options'   => Mage::getModel('adminhtml/system_store')->getStoreOptionHash(true),
        ));
        $this->addColumn('action', array(
            'header' => Mage::helper('rewardpointstransfer')->__('Action'),
            'width' => '100',
            'type' => 'action',
            'getter' => 'getId',
            'actions' => array(
                array(
                    'caption' => Mage::helper('rewardpointstransfer')->__('View'),
                    'url' => array('base' => '*/*/edit'),
                    'field' => 'id'
                )),
            'filter' => false,
            'sortable' => false,
            'index' => 'stores',
            'is_system' => true,
        ));

        $this->addExportType('*/*/exportCsv', Mage::helper('rewardpointstransfer')->__('CSV'));
        $this->addExportType('*/*/exportXml', Mage::helper('rewardpointstransfer')->__('XML'));

        return parent::_prepareColumns();
    }

    /**
     * prepare mass action for this grid
     *
     * @return Magestore_RewardPointsTransfer_Block_Adminhtml_Rewardpointstransfer_Grid
     */
    protected function _prepareMassaction() {
        $this->setMassactionIdField('transfer_id');
        $this->getMassactionBlock()->setFormFieldName('rewardpointstransfer');

        $this->getMassactionBlock()->addItem('complete', array(
            'label'        => Mage::helper('rewardpointstransfer')->__('Complete'),
            'url'        => $this->getUrl('*/*/massComplete'),
            'confirm'    => Mage::helper('rewardpointstransfer')->__('Are you sure?')
        ));
        
        $this->getMassactionBlock()->addItem('cancel', array(
            'label'        => Mage::helper('rewardpointstransfer')->__('Cancel'),
            'url'        => $this->getUrl('*/*/massCancel'),
            'confirm'    => Mage::helper('rewardpointstransfer')->__('Are you sure?')
        ));
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