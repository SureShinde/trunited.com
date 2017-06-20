<?php

class Magestore_ManageApi_Block_Adminhtml_Targetactions_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('targetactionsGrid');
        $this->setDefaultSort('target_actions_id');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getModel('manageapi/targetactions')->getCollection();
        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('target_actions_id', array(
            'header' => Mage::helper('manageapi')->__('ID'),
            'align' => 'right',
            'width' => '50px',
            'index' => 'target_actions_id',
        ));

        $this->addColumn('referral_date', array(
            'header' => Mage::helper('manageapi')->__('Referral Date'),
            'align' => 'left',
            'index' => 'referral_date',
        ));
        $this->addColumn('action_date', array(
            'header' => Mage::helper('manageapi')->__('Action Date'),
            'align' => 'left',
            'index' => 'action_date',
            'type' => 'date',
        ));
        $this->addColumn('locking_date', array(
            'header' => Mage::helper('manageapi')->__('Locking Date'),
            'align' => 'left',
            'index' => 'locking_date',
        ));
        $this->addColumn('adj_date', array(
            'header' => Mage::helper('manageapi')->__('Adj Date'),
            'align' => 'left',
            'index' => 'adj_date',
        ));
        $this->addColumn('scheduled_clearing_date', array(
            'header' => Mage::helper('manageapi')->__('Scheduled Clearing Date'),
            'align' => 'left',
            'index' => 'scheduled_clearing_date',
        ));
        $this->addColumn('action_id', array(
            'header' => Mage::helper('manageapi')->__('Action ID'),
            'align' => 'left',
            'index' => 'action_id',
        ));
        $this->addColumn('campaign', array(
            'header' => Mage::helper('manageapi')->__('Campaign'),
            'align' => 'left',
            'index' => 'campaign',
        ));
        $this->addColumn('action_tracker', array(
            'header' => Mage::helper('manageapi')->__('Action Tracker'),
            'align' => 'left',
            'index' => 'action_tracker',
        ));
        $this->addColumn('status', array(
            'header' => Mage::helper('manageapi')->__('Status'),
            'align' => 'left',
            'index' => 'status',
        ));
        $this->addColumn('status_detail', array(
            'header' => Mage::helper('manageapi')->__('Status Detail'),
            'align' => 'left',
            'index' => 'status_detail',
        ));
        $this->addColumn('category_list', array(
            'header' => Mage::helper('manageapi')->__('Category List'),
            'align' => 'left',
            'index' => 'category_list',
        ));
        $this->addColumn('sku', array(
            'header' => Mage::helper('manageapi')->__('Sku'),
            'align' => 'left',
            'index' => 'sku',
        ));
        $this->addColumn('item_name', array(
            'header' => Mage::helper('manageapi')->__('Item Name'),
            'align' => 'left',
            'index' => 'item_name',
        ));
        $this->addColumn('category', array(
            'header' => Mage::helper('manageapi')->__('Category'),
            'align' => 'left',
            'index' => 'category',
        ));
        $this->addColumn('quantity', array(
            'header' => Mage::helper('manageapi')->__('Quantity'),
            'align' => 'left',
            'index' => 'quantity',
        ));
        $this->addColumn('sale_amount', array(
            'header' => Mage::helper('manageapi')->__('Sale Amount'),
            'align' => 'left',
            'index' => 'sale_amount',
        ));
        $this->addColumn('original_sale_amount', array(
            'header' => Mage::helper('manageapi')->__('Original Sale Amount'),
            'align' => 'left',
            'index' => 'original_sale_amount',
        ));
        $this->addColumn('payout', array(
            'header' => Mage::helper('manageapi')->__('Payout'),
            'align' => 'left',
            'index' => 'payout',
        ));
        $this->addColumn('original_payout', array(
            'header' => Mage::helper('manageapi')->__('Original Payout'),
            'align' => 'left',
            'index' => 'original_payout',
        ));
        $this->addColumn('vat', array(
            'header' => Mage::helper('manageapi')->__('VAT'),
            'align' => 'left',
            'index' => 'vat',
        ));
        $this->addColumn('promo_code', array(
            'header' => Mage::helper('manageapi')->__('Promo Code'),
            'align' => 'left',
            'index' => 'promo_code',
        ));
        $this->addColumn('ad', array(
            'header' => Mage::helper('manageapi')->__('AD'),
            'align' => 'left',
            'index' => 'ad',
        ));
        $this->addColumn('referring_url', array(
            'header' => Mage::helper('manageapi')->__('Referring Url'),
            'align' => 'left',
            'index' => 'referring_url',
        ));
        $this->addColumn('referring_type', array(
            'header' => Mage::helper('manageapi')->__('Referring Type'),
            'align' => 'left',
            'index' => 'referring_type',
        ));
        $this->addColumn('ip_address', array(
            'header' => Mage::helper('manageapi')->__('IP Address'),
            'align' => 'left',
            'index' => 'ip_address',
        ));
        $this->addColumn('geo_location', array(
            'header' => Mage::helper('manageapi')->__('Geo Location'),
            'align' => 'left',
            'index' => 'geo_location',
        ));
        $this->addColumn('subid1', array(
            'header' => Mage::helper('manageapi')->__('Sub ID 1'),
            'align' => 'left',
            'index' => 'subid1',
        ));
        $this->addColumn('subid2', array(
            'header' => Mage::helper('manageapi')->__('Sub ID 2'),
            'align' => 'left',
            'index' => 'subid2',
        ));
        $this->addColumn('subid3', array(
            'header' => Mage::helper('manageapi')->__('Sub ID 3'),
            'align' => 'left',
            'index' => 'subid3',
        ));
        $this->addColumn('sharedid', array(
            'header' => Mage::helper('manageapi')->__('Shared ID'),
            'align' => 'left',
            'index' => 'sharedid',
        ));
        $this->addColumn('date1', array(
            'header' => Mage::helper('manageapi')->__('Date 1'),
            'align' => 'left',
            'index' => 'date1',
        ));
        $this->addColumn('date2', array(
            'header' => Mage::helper('manageapi')->__('Date 2'),
            'align' => 'left',
            'index' => 'date2',
        ));
        $this->addColumn('paystub', array(
            'header' => Mage::helper('manageapi')->__('Paystub'),
            'align' => 'left',
            'index' => 'paystub',
        ));
        $this->addColumn('device', array(
            'header' => Mage::helper('manageapi')->__('Device'),
            'align' => 'left',
            'index' => 'device',
        ));
        $this->addColumn('os', array(
            'header' => Mage::helper('manageapi')->__('OS'),
            'align' => 'left',
            'index' => 'os',
        ));
        $this->addColumn('paystub', array(
            'header' => Mage::helper('manageapi')->__('Paystub'),
            'align' => 'left',
            'index' => 'paystub',
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
        $this->setMassactionIdField('targetactions_id');
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