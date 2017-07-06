<?php

class Magestore_ManageApi_Block_Adminhtml_Linkshare_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
	public function __construct(){
		parent::__construct();
		$this->setId('linkshareGrid');
		$this->setDefaultSort('linkshare_id');
		$this->setDefaultDir('ASC');
		$this->setSaveParametersInSession(true);
	}

	protected function _prepareCollection(){
		$collection = Mage::getModel('manageapi/linkshare')->getCollection();
		$this->setCollection($collection);
		return parent::_prepareCollection();
	}

	protected function _prepareColumns(){
		$this->addColumn('linkshare_id', array(
			'header'	=> Mage::helper('manageapi')->__('ID'),
			'align'	 =>'right',
			'width'	 => '50px',
			'index'	 => 'linkshare_id',
		));

		$this->addColumn('member_id', array(
			'header'	=> Mage::helper('manageapi')->__('Member ID (U1)'),
			'align'	 =>'left',
			'index'	 => 'member_id',
		));

		$this->addColumn('advertiser_name', array(
			'header'	=> Mage::helper('manageapi')->__('Advertiser Name'),
            'align'	 =>'left',
			'index'	 => 'advertiser_name',
		));

        $this->addColumn('order_id', array(
            'header'	=> Mage::helper('manageapi')->__('Order ID'),
            'align'	 =>'left',
            'index'	 => 'order_id',
        ));

        $this->addColumn('transaction_date', array(
            'header'	=> Mage::helper('manageapi')->__('Transaction Date'),
            'align'	 =>'left',
            'index'	 => 'transaction_date',
            'type'		=> 'date'
        ));

        $this->addColumn('sales', array(
            'header'	=> Mage::helper('manageapi')->__('Sales'),
            'align'	 =>'left',
            'index'	 => 'sales',
        ));

        $this->addColumn('total_commission', array(
            'header'	=> Mage::helper('manageapi')->__('Total Commission'),
            'align'	 =>'left',
            'index'	 => 'total_commission',
        ));

        $this->addColumn('process_date', array(
            'header'	=> Mage::helper('manageapi')->__('Process Date'),
            'align'	 =>'left',
            'index'	 => 'process_date',
            'type'		=> 'date'
        ));

        $this->addColumn('created_at', array(
            'header'	=> Mage::helper('manageapi')->__('Created At'),
            'align'	 =>'left',
            'index'	 => 'created_at',
            'type'		=> 'date'
        ));


		$this->addExportType('*/*/exportCsv', Mage::helper('manageapi')->__('CSV'));
		$this->addExportType('*/*/exportXml', Mage::helper('manageapi')->__('XML'));

		return parent::_prepareColumns();
	}

	protected function _prepareMassaction(){
		$this->setMassactionIdField('linkshare_id');
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