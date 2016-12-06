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
 * @package     Magestore_AffiliateplusDirectLink
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * Affiliateplusdirectlink Grid Block
 * 
 * @category    Magestore
 * @package     Magestore_AffiliateplusDirectLink
 * @author      Magestore Developer
 */
class Magestore_AffiliateplusDirectLink_Block_Adminhtml_Domain_Grid extends Mage_Adminhtml_Block_Widget_Grid {

    public function __construct() {
        parent::__construct();
        $this->setId('domainGrid');
        $this->setDefaultSort('domain_id');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
    }

    /**
     * prepare collection for block to display
     *
     * @return Magestore_AffiliateplusDirectLink_Block_Adminhtml_Affiliateplusdirectlink_Grid
     */
    protected function _prepareCollection() {
        $collection = Mage::getModel('affiliateplusdirectlink/domain')->getCollection();
        $collection->getSelect()
                ->join(array('aa' => Mage::getModel('core/resource')->getTableName('affiliateplus_account')), 'aa.account_id =main_table.account_id', array('name', 'email'));
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * prepare columns for this grid
     *
     * @return Magestore_AffiliateplusDirectLink_Block_Adminhtml_Affiliateplusdirectlink_Grid
     */
    protected function _prepareColumns() {
        $this->addColumn('domain_id', array(
            'header' => Mage::helper('affiliateplusdirectlink')->__('ID'),
            'align' => 'right',
            'width' => '50px',
            'index' => 'domain_id',
        ));

//        $this->addColumn('name', array(
//            'header'    => Mage::helper('affiliateplusdirectlink')->__('Account'),
//            'align'     =>'left',
//            'index'     => 'name',
//        ));

        $this->addColumn('email', array(
            'header' => Mage::helper('affiliateplusdirectlink')->__('Affiliate Account'),
            'align' => 'left',
            'index' => 'email',
            'renderer' => 'affiliateplusdirectlink/adminhtml_renderer_email',
        ));

        $this->addColumn('domain', array(
            'header' => Mage::helper('affiliateplusdirectlink')->__('Domain'),
            'align' => 'left',
            'index' => 'domain',
            'renderer' => 'affiliateplusdirectlink/adminhtml_renderer_domain',
        ));

        $this->addColumn('status', array(
            'header' => Mage::helper('affiliateplusdirectlink')->__('Status'),
            'align' => 'left',
            'width' => '80px',
            'index' => 'status',
            'filter_index' => 'main_table.status',
            'type' => 'options',
            'options' => Mage::getSingleton('affiliateplusdirectlink/status')->getOptionArray(),
        ));

        $this->addColumn('action', array(
            'header' => Mage::helper('affiliateplusdirectlink')->__('Action'),
            'width' => '100',
            'type' => 'action',
            'getter' => 'getId',
            'actions' => array(
                array(
                    'caption' => Mage::helper('affiliateplusdirectlink')->__('Edit'),
                    'url' => array('base' => '*/*/edit'),
                    'field' => 'id'
            )),
            'filter' => false,
            'sortable' => false,
            'index' => 'stores',
            'is_system' => true,
        ));

        $this->addExportType('*/*/exportCsv', Mage::helper('affiliateplusdirectlink')->__('CSV'));
        $this->addExportType('*/*/exportXml', Mage::helper('affiliateplusdirectlink')->__('XML'));

        return parent::_prepareColumns();
    }

    /**
     * prepare mass action for this grid
     *
     * @return Magestore_AffiliateplusDirectLink_Block_Adminhtml_Affiliateplusdirectlink_Grid
     */
    protected function _prepareMassaction() {
        $this->setMassactionIdField('domain_id');
        $this->getMassactionBlock()->setFormFieldName('domain');

        $this->getMassactionBlock()->addItem('delete', array(
            'label' => Mage::helper('affiliateplusdirectlink')->__('Delete'),
            'url' => $this->getUrl('*/*/massDelete'),
            'confirm' => Mage::helper('affiliateplusdirectlink')->__('Are you sure?')
        ));

        $statuses = Mage::getSingleton('affiliateplusdirectlink/status')->getOptionArray();

        array_unshift($statuses, array('label' => '', 'value' => ''));
        $this->getMassactionBlock()->addItem('status', array(
            'label' => Mage::helper('affiliateplusdirectlink')->__('Change status'),
            'url' => $this->getUrl('*/*/massStatus', array('_current' => true)),
            'additional' => array(
                'visibility' => array(
                    'name' => 'status',
                    'type' => 'select',
                    'class' => 'required-entry',
                    'label' => Mage::helper('affiliateplusdirectlink')->__('Status'),
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