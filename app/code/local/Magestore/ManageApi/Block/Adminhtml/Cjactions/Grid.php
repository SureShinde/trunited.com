<?php

class Magestore_ManageApi_Block_Adminhtml_Cjactions_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('cjactionsGrid');
        $this->setDefaultSort('cj_actions_id');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getModel('manageapi/cjactions')->getCollection();
        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('cj_actions_id', array(
            'header' => Mage::helper('manageapi')->__('ID'),
            'align' => 'right',
            'width' => '50px',
            'index' => 'cj_actions_id',
        ));

        $this->addColumn('action_status', array(
            'header' => Mage::helper('manageapi')->__('Action Status'),
            'align' => 'left',
            'index' => 'action_status',
        ));

        $this->addColumn('action_type', array(
            'header' => Mage::helper('manageapi')->__('Action Type'),
            'align' => 'left',
            'index' => 'action_type',
        ));
        $this->addColumn('aid', array(
            'header' => Mage::helper('manageapi')->__('AID'),
            'align' => 'left',
            'index' => 'aid',
        ));
        $this->addColumn('commission_id', array(
            'header' => Mage::helper('manageapi')->__('Commission ID'),
            'align' => 'left',
            'index' => 'commission_id',
        ));
        $this->addColumn('country', array(
            'header' => Mage::helper('manageapi')->__('Country'),
            'align' => 'left',
            'index' => 'country',
        ));
        $this->addColumn('event_date', array(
            'header' => Mage::helper('manageapi')->__('Event Date'),
            'align' => 'left',
            'index' => 'event_date',
            'type' => 'date',
        ));
        $this->addColumn('locking_date', array(
            'header' => Mage::helper('manageapi')->__('Locking Date'),
            'align' => 'left',
            'index' => 'locking_date',
            'type' => 'date',
        ));
        $this->addColumn('order_id', array(
            'header' => Mage::helper('manageapi')->__('Order ID'),
            'align' => 'left',
            'index' => 'order_id',
        ));
        $this->addColumn('original', array(
            'header' => Mage::helper('manageapi')->__('Original'),
            'align' => 'left',
            'index' => 'original',
        ));
        $this->addColumn('original_action_id', array(
            'header' => Mage::helper('manageapi')->__('Original Action ID'),
            'align' => 'left',
            'index' => 'original_action_id',
        ));
        $this->addColumn('posting_date', array(
            'header' => Mage::helper('manageapi')->__('Posting Date'),
            'align' => 'left',
            'index' => 'posting_date',
            'type' => 'date',
        ));
        $this->addColumn('website_id', array(
            'header' => Mage::helper('manageapi')->__('Website ID'),
            'align' => 'left',
            'index' => 'website_id',
        ));
        $this->addColumn('action_tracker_id', array(
            'header' => Mage::helper('manageapi')->__('Action Tracker ID'),
            'align' => 'left',
            'index' => 'action_tracker_id',
        ));
        $this->addColumn('action_tracker_name', array(
            'header' => Mage::helper('manageapi')->__('Action Tracker Name'),
            'align' => 'left',
            'index' => 'action_tracker_name',
        ));
        $this->addColumn('cid', array(
            'header' => Mage::helper('manageapi')->__('CID'),
            'align' => 'left',
            'index' => 'cid',
        ));
        $this->addColumn('advertiser_name', array(
            'header' => Mage::helper('manageapi')->__('Advertiser Name'),
            'align' => 'left',
            'index' => 'advertiser_name',
        ));
        $this->addColumn('commission_amount', array(
            'header' => Mage::helper('manageapi')->__('Commission Amount'),
            'align' => 'left',
            'index' => 'commission_amount',
        ));
        $this->addColumn('order_discount', array(
            'header' => Mage::helper('manageapi')->__('Order Discount'),
            'align' => 'left',
            'index' => 'order_discount',
        ));
        $this->addColumn('sid', array(
            'header' => Mage::helper('manageapi')->__('SID'),
            'align' => 'left',
            'index' => 'sid',
        ));
        $this->addColumn('sale_amount', array(
            'header' => Mage::helper('manageapi')->__('Sale Amount'),
            'align' => 'left',
            'index' => 'sale_amount',
        ));
        $this->addColumn('created_time', array(
            'header' => Mage::helper('manageapi')->__('Created At'),
            'align' => 'left',
            'index' => 'created_time',
            'type' => 'date'
        ));


        $this->addExportType('*/*/exportCsv', Mage::helper('manageapi')->__('CSV'));
        $this->addExportType('*/*/exportXml', Mage::helper('manageapi')->__('XML'));

        return parent::_prepareColumns();
    }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('cjactions_id');
        $this->getMassactionBlock()->setFormFieldName('manageapi');

        $this->getMassactionBlock()->addItem('delete', array(
            'label' => Mage::helper('manageapi')->__('Delete'),
            'url' => $this->getUrl('*/*/massDelete'),
            'confirm' => Mage::helper('manageapi')->__('Are you sure?')
        ));

        return $this;
    }

    public function getRowUrl($row)
    {
    }
}