<?php

class Magestore_Manageapi_Block_Adminhtml_Manageapi_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
	public function __construct(){
		parent::__construct();
		$this->setId('manageapiGrid');
		$this->setDefaultSort('manageapi_id');
		$this->setDefaultDir('ASC');
		$this->setSaveParametersInSession(true);
	}

	protected function _prepareCollection(){
		$collection = Mage::getModel('manageapi/manageapi')->getCollection();
		$this->setCollection($collection);
		return parent::_prepareCollection();
	}

	protected function _prepareColumns(){
		$this->addColumn('manageapi_id', array(
			'header'	=> Mage::helper('manageapi')->__('ID'),
			'align'	 =>'right',
			'width'	 => '50px',
			'index'	 => 'manageapi_id',
		));

		$this->addColumn('title', array(
			'header'	=> Mage::helper('manageapi')->__('Title'),
			'align'	 =>'left',
			'index'	 => 'title',
		));

		$this->addColumn('content', array(
			'header'	=> Mage::helper('manageapi')->__('Item Content'),
			'width'	 => '150px',
			'index'	 => 'content',
		));

		$this->addColumn('status', array(
			'header'	=> Mage::helper('manageapi')->__('Status'),
			'align'	 => 'left',
			'width'	 => '80px',
			'index'	 => 'status',
			'type'		=> 'options',
			'options'	 => array(
				1 => 'Enabled',
				2 => 'Disabled',
			),
		));

		$this->addColumn('action',
			array(
				'header'	=>	Mage::helper('manageapi')->__('Action'),
				'width'		=> '100',
				'type'		=> 'action',
				'getter'	=> 'getId',
				'actions'	=> array(
					array(
						'caption'	=> Mage::helper('manageapi')->__('Edit'),
						'url'		=> array('base'=> '*/*/edit'),
						'field'		=> 'id'
					)),
				'filter'	=> false,
				'sortable'	=> false,
				'index'		=> 'stores',
				'is_system'	=> true,
		));

		$this->addExportType('*/*/exportCsv', Mage::helper('manageapi')->__('CSV'));
		$this->addExportType('*/*/exportXml', Mage::helper('manageapi')->__('XML'));

		return parent::_prepareColumns();
	}

	protected function _prepareMassaction(){
		$this->setMassactionIdField('manageapi_id');
		$this->getMassactionBlock()->setFormFieldName('manageapi');

		$this->getMassactionBlock()->addItem('delete', array(
			'label'		=> Mage::helper('manageapi')->__('Delete'),
			'url'		=> $this->getUrl('*/*/massDelete'),
			'confirm'	=> Mage::helper('manageapi')->__('Are you sure?')
		));

		$statuses = Mage::getSingleton('manageapi/status')->getOptionArray();

		array_unshift($statuses, array('label'=>'', 'value'=>''));
		$this->getMassactionBlock()->addItem('status', array(
			'label'=> Mage::helper('manageapi')->__('Change status'),
			'url'	=> $this->getUrl('*/*/massStatus', array('_current'=>true)),
			'additional' => array(
				'visibility' => array(
					'name'	=> 'status',
					'type'	=> 'select',
					'class'	=> 'required-entry',
					'label'	=> Mage::helper('manageapi')->__('Status'),
					'values'=> $statuses
				))
		));
		return $this;
	}

	public function getRowUrl($row){
		return $this->getUrl('*/*/edit', array('id' => $row->getId()));
	}
}