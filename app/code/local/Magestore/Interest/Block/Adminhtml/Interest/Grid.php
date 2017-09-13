<?php

class Magestore_Interest_Block_Adminhtml_Interest_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
	public function __construct(){
		parent::__construct();
		$this->setId('interestGrid');
		$this->setDefaultSort('interest_id');
		$this->setDefaultDir('ASC');
		$this->setSaveParametersInSession(true);
	}

	protected function _prepareCollection(){
		$collection = Mage::getModel('interest/interest')->getCollection();
		$this->setCollection($collection);
		return parent::_prepareCollection();
	}

	protected function _prepareColumns(){
		$this->addColumn('interest_id', array(
			'header'	=> Mage::helper('interest')->__('ID'),
			'align'	 =>'right',
			'width'	 => '50px',
			'index'	 => 'interest_id',
		));

		$this->addColumn('title', array(
			'header'	=> Mage::helper('interest')->__('Title'),
			'align'	 =>'left',
			'index'	 => 'title',
		));

		$this->addColumn('sort_order', array(
			'header'	=> Mage::helper('interest')->__('Sort Order'),
			'width'	 => '150px',
			'index'	 => 'sort_order',
		));

		$this->addColumn('status', array(
			'header'	=> Mage::helper('interest')->__('Status'),
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
				'header'	=>	Mage::helper('interest')->__('Action'),
				'width'		=> '100',
				'type'		=> 'action',
				'getter'	=> 'getId',
				'actions'	=> array(
					array(
						'caption'	=> Mage::helper('interest')->__('Edit'),
						'url'		=> array('base'=> '*/*/edit'),
						'field'		=> 'id'
					)),
				'filter'	=> false,
				'sortable'	=> false,
				'index'		=> 'stores',
				'is_system'	=> true,
		));

		$this->addExportType('*/*/exportCsv', Mage::helper('interest')->__('CSV'));
		$this->addExportType('*/*/exportXml', Mage::helper('interest')->__('XML'));

		return parent::_prepareColumns();
	}

	protected function _prepareMassaction(){
		$this->setMassactionIdField('interest_id');
		$this->getMassactionBlock()->setFormFieldName('interest');

		$this->getMassactionBlock()->addItem('delete', array(
			'label'		=> Mage::helper('interest')->__('Delete'),
			'url'		=> $this->getUrl('*/*/massDelete'),
			'confirm'	=> Mage::helper('interest')->__('Are you sure?')
		));

		$statuses = Mage::getSingleton('interest/status')->getOptionArray();

		array_unshift($statuses, array('label'=>'', 'value'=>''));
		$this->getMassactionBlock()->addItem('status', array(
			'label'=> Mage::helper('interest')->__('Change status'),
			'url'	=> $this->getUrl('*/*/massStatus', array('_current'=>true)),
			'additional' => array(
				'visibility' => array(
					'name'	=> 'status',
					'type'	=> 'select',
					'class'	=> 'required-entry',
					'label'	=> Mage::helper('interest')->__('Status'),
					'values'=> $statuses
				))
		));
		return $this;
	}

	public function getRowUrl($row){
		return $this->getUrl('*/*/edit', array('id' => $row->getId()));
	}
}
