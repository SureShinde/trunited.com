<?php

class Magestore_ManageApi_Block_Adminhtml_Linkshareadvertisers_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
	public function __construct(){
		parent::__construct();
		$this->setId('linkshareadvertisersGrid');
		$this->setDefaultSort('linkshare_advertisers_id');
		$this->setDefaultDir('ASC');
		$this->setSaveParametersInSession(true);
	}

	protected function _prepareCollection(){
		$collection = Mage::getModel('manageapi/linkshareadvertisers')->getCollection();
		$this->setCollection($collection);
		return parent::_prepareCollection();
	}

	protected function _prepareColumns(){
		$this->addColumn('linkshare_advertisers_id', array(
			'header'	=> Mage::helper('manageapi')->__('ID'),
			'align'	 =>'right',
			'width'	 => '50px',
			'index'	 => 'linkshare_advertisers_id',
		));

		$this->addColumn('etransaction_id', array(
			'header'	=> Mage::helper('manageapi')->__('Etransaction ID'),
			'align'	 =>'left',
			'index'	 => 'etransaction_id',
		));

		$this->addColumn('advertiser_id', array(
			'header'	=> Mage::helper('manageapi')->__('Advertiser ID'),
			'align'	 =>'left',
			'index'	 => 'advertiser_id',
		));

		$this->addColumn('sid', array(
			'header'	=> Mage::helper('manageapi')->__('SID'),
            'align'	 =>'left',
			'index'	 => 'sid',
		));

        $this->addColumn('order_id', array(
            'header'	=> Mage::helper('manageapi')->__('Order ID'),
            'align'	 =>'left',
            'index'	 => 'order_id',
        ));

		$this->addColumn('offer_id', array(
            'header'	=> Mage::helper('manageapi')->__('Offer ID'),
            'align'	 =>'left',
            'index'	 => 'offer_id',
        ));

		$this->addColumn('sku_number', array(
			'header'	=> Mage::helper('manageapi')->__('SKU'),
			'align'	 =>'left',
			'index'	 => 'sku_number',
		));

        $this->addColumn('sale_amount', array(
            'header'	=> Mage::helper('manageapi')->__('Sales Amount'),
            'align'	 =>'left',
            'index'	 => 'sale_amount',
        ));

		$this->addColumn('quantity', array(
			'header'	=> Mage::helper('manageapi')->__('Quantity'),
			'align'	 =>'left',
			'index'	 => 'quantity',
		));

        $this->addColumn('commissions', array(
            'header'	=> Mage::helper('manageapi')->__('Commission'),
            'align'	 =>'left',
            'index'	 => 'commissions',
        ));

        $this->addColumn('process_date', array(
            'header'	=> Mage::helper('manageapi')->__('Process Date'),
            'align'	 =>'left',
            'index'	 => 'process_date',
            'type'		=> 'date'
        ));

		$this->addColumn('transaction_date', array(
            'header'	=> Mage::helper('manageapi')->__('Transaction Date'),
            'align'	 =>'left',
            'index'	 => 'transaction_date',
            'type'		=> 'date'
        ));

		$this->addColumn('transaction_type', array(
			'header'	=> Mage::helper('manageapi')->__('Transaction Type'),
			'align'	 =>'left',
			'index'	 => 'transaction_type',
		));

		$this->addColumn('product_name', array(
			'header'	=> Mage::helper('manageapi')->__('Product Name'),
			'align'	 =>'left',
			'index'	 => 'product_name',
		));

		$this->addColumn('u1', array(
			'header'	=> Mage::helper('manageapi')->__('U1'),
			'align'	 =>'left',
			'index'	 => 'u1',
		));

		$this->addColumn('currency', array(
			'header'	=> Mage::helper('manageapi')->__('Currency'),
			'align'	 =>'left',
			'index'	 => 'currency',
		));

		$this->addColumn('is_event', array(
			'header'	=> Mage::helper('manageapi')->__('Is Event'),
			'align'	 =>'left',
			'index'	 => 'is_event',
		));

        $this->addColumn('created_time', array(
            'header'	=> Mage::helper('manageapi')->__('Created At'),
            'align'	 =>'left',
            'index'	 => 'created_time',
            'type'		=> 'date'
        ));

		$this->addExportType('*/*/exportCsv', Mage::helper('manageapi')->__('CSV'));
		$this->addExportType('*/*/exportXml', Mage::helper('manageapi')->__('XML'));

		return parent::_prepareColumns();
	}

	protected function _prepareMassaction(){
		$this->setMassactionIdField('linkshare_advertisers_id');
		$this->getMassactionBlock()->setFormFieldName('manageapi');

		$this->getMassactionBlock()->addItem('delete', array(
			'label'		=> Mage::helper('manageapi')->__('Delete'),
			'url'		=> $this->getUrl('*/*/massDelete'),
			'confirm'	=> Mage::helper('manageapi')->__('Are you sure?')
		));

		return $this;
	}

	public function getRowUrl($row){	}
}