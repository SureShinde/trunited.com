<?php

class Magestore_Affiliatepluspayperlead_Block_Adminhtml_Affiliatepluspayperlead_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
	public function __construct(){
		parent::__construct();
		$this->setId('affiliatepluspayperleadGrid');
		$this->setDefaultSort('lead_id');
		$this->setDefaultDir('DESC');
		$this->setSaveParametersInSession(true);
	}

	protected function _prepareCollection(){
		$collection = Mage::getModel('affiliatepluspayperlead/lead')->getCollection();
		$this->setCollection($collection);
		return parent::_prepareCollection();
	}

	protected function _prepareColumns(){
		$currencyCode = Mage::app()->getStore()->getBaseCurrency()->getCode();
		$this->addColumn('lead_id', array(
			'header'	=> Mage::helper('affiliatepluspayperlead')->__('ID'),
			'align'	 =>'right',
			'width'	 => '50px',
			'index'	 => 'lead_id',
		));

		$this->addColumn('account_name', array(
			'header'	=> Mage::helper('affiliatepluspayperlead')->__('Account Name'),
			'align'	 =>'left',
			'index'	 => 'account_name',
			'renderer'	=> 'affiliatepluspayperlead/adminhtml_affiliatepluspayperlead_renderer_account'
		));

		$this->addColumn('customer_email', array(
			'header'	=> Mage::helper('affiliatepluspayperlead')->__('Customer Email'),
			'align'	 => 'left',
			'index'	 => 'customer_email',
		));
		$this->addColumn('action', array(
			'header'	=> Mage::helper('affiliatepluspayperlead')->__('Action'),
			'align'	 => 'left',
			'width'	 => '80px',
			'index'	 => 'action',
			'type'		=> 'options',
			'options'	 => array(
				1 => 'Sign up for an account',
				2 => 'Subscribe to newsletters',
			),
		));
		$this->addColumn('commission', array(
			'header'	=> Mage::helper('affiliatepluspayperlead')->__('Commission'),
			'align'	 => 'left',
			'index'	 => 'commission',
			'type'  	=> 'price',
		  	'currency_code' => $currencyCode,
		));
		$this->addColumn('created_time', array(
			'header'	=> Mage::helper('affiliatepluspayperlead')->__('Date Created'),
			'align'	 => 'left',
			'type'	=>	'datetime',
			'index'	 => 'created_time',
		));
		$this->addColumn('status', array(
			'header'	=> Mage::helper('affiliatepluspayperlead')->__('Status'),
			'align'	 => 'left',
			'width'	 => '80px',
			'index'	 => 'status',
			'type'		=> 'options',
			'options'	 => Mage::getSingleton('affiliatepluspayperlead/status')->getOptionArray()
		));

		

		$this->addExportType('*/*/exportCsv', Mage::helper('affiliatepluspayperlead')->__('CSV'));
		$this->addExportType('*/*/exportXml', Mage::helper('affiliatepluspayperlead')->__('XML'));

		return parent::_prepareColumns();
	}

	protected function _prepareMassaction(){
		return $this;
	}

	public function getRowUrl($row){
		return $this->getUrl('*/*/edit', array('id' => $row->getId()));
	}
}