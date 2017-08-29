<?php

class Magestore_TruGiftCard_Block_Adminhtml_Transaction_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
	public function __construct(){
		parent::__construct();
		$this->setId('transactionGrid');
		$this->setDefaultSort('transaction_id');
		$this->setDefaultDir('DESC');
		$this->setSaveParametersInSession(true);

	}

	protected function _prepareCollection(){
		$collection = Mage::getModel('trugiftcard/transaction')->getCollection();
		$this->setCollection($collection);
		return parent::_prepareCollection();
	}

	protected function _prepareColumns(){
		$this->addColumn('transaction_id', array(
			'header'	=> Mage::helper('trugiftcard')->__('ID'),
			'align'	 =>'right',
			'width'	 => '50px',
			'index'	 => 'transaction_id',
		));

		$this->addColumn('customer_id', array(
			'header'	=> Mage::helper('trugiftcard')->__('Customer ID'),
			'align'	 =>'left',
			'index'	 => 'customer_id',
		));

		$this->addColumn('customer_email', array(
			'header'	=> Mage::helper('trugiftcard')->__('Customer Email'),
			'align'	 =>'left',
			'index'	 => 'customer_email',
		));

		$this->addColumn('title', array(
			'header'	=> Mage::helper('trugiftcard')->__('Title'),
			'align'	 =>'left',
			'index'	 => 'title',
			'width'	=> '100'
		));

		$this->addColumn('action_type', array(
			'header'    => Mage::helper('trugiftcard')->__('Action'),
			'align'     => 'left',
			'index'     => 'action_type',
			'type'      => 'options',
			'width'		=> '100px',
			'options'   => Mage::getSingleton('trugiftcard/type')->getOptionArray(),
		));

		$this->addColumn('current_credit', array(
			'header'    => Mage::helper('trugiftcard')->__('Current Credits'),
			'align'     => 'right',
			'index'     => 'current_credit',
			'type'      => 'number',
		));

		$this->addColumn('changed_credit', array(
			'header'    => Mage::helper('trugiftcard')->__('Updated Credits'),
			'align'     => 'right',
			'index'     => 'changed_credit',
			'type'      => 'number',
		));

		$this->addColumn('created_time', array(
			'header'    => Mage::helper('trugiftcard')->__('Created On'),
			'index'     => 'created_time',
			'type'      => 'datetime',
		));

		$this->addColumn('order_id', array(
			'header'    => Mage::helper('trugiftcard')->__('Order ID'),
			'index'     => 'order_id',
			'renderer'  => 'Magestore_TruGiftCard_Block_Adminhtml_Renderer_Transaction_Grid',
		));

		/*$this->addColumn('expiration_date', array(
			'header'    => Mage::helper('trugiftcard')->__('Expires On'),
			'index'     => 'expiration_date',
			'type'      => 'datetime',
		));*/

		$this->addColumn('receiver_email', array(
			'header'    => Mage::helper('trugiftcard')->__('Receiver Email'),
			'index'     => 'receiver_email',
		));

		$this->addColumn('status', array(
			'header'    => Mage::helper('trugiftcard')->__('Status'),
			'align'     => 'left',
			'index'     => 'status',
			'type'      => 'options',
			'options'   => Mage::getSingleton('trugiftcard/status')->getTransactionOptionArray(),
		));

		$this->addColumn('store_id', array(
			'header'    => Mage::helper('trugiftcard')->__('Store View'),
			'align'     => 'left',
			'index'     => 'store_id',
			'type'      => 'options',
			'options'   => Mage::getModel('adminhtml/system_store')->getStoreOptionHash(true),
		));

		$this->addColumn('action',
			array(
				'header'	=>	Mage::helper('trugiftcard')->__('Action'),
				'width'		=> '100',
				'type'		=> 'action',
				'getter'	=> 'getId',
				'actions'	=> array(
					array(
						'caption'	=> Mage::helper('trugiftcard')->__('Edit'),
						'url'		=> array('base'=> '*/*/edit'),
						'field'		=> 'id'
					)),
				'filter'	=> false,
				'sortable'	=> false,
				'index'		=> 'stores',
				'is_system'	=> true,
		));

		$this->addExportType('*/*/exportCsv', Mage::helper('trugiftcard')->__('CSV'));
		$this->addExportType('*/*/exportXml', Mage::helper('trugiftcard')->__('XML'));

		return parent::_prepareColumns();
	}

	protected function _prepareMassaction(){
		$this->setMassactionIdField('trugiftcard_id');
		$this->getMassactionBlock()->setFormFieldName('trugiftcard');

		$this->getMassactionBlock()->addItem('delete', array(
			'label'		=> Mage::helper('trugiftcard')->__('Delete'),
			'url'		=> $this->getUrl('*/*/massDelete'),
			'confirm'	=> Mage::helper('trugiftcard')->__('Are you sure?')
		));

		$statuses = Magestore_TruGiftCard_Model_Status::getTransactionOptionArray();
		array_unshift($statuses, array('label'=>'', 'value'=>''));
		$this->getMassactionBlock()->addItem('status', array(
			'label'=> Mage::helper('trugiftcard')->__('Change status'),
			'url'	=> $this->getUrl('*/*/massStatus', array('_current'=>true)),
			'additional' => array(
				'visibility' => array(
					'name'	=> 'status',
					'type'	=> 'select',
					'class'	=> 'required-entry',
					'label'	=> Mage::helper('trugiftcard')->__('Status'),
					'values'=> $statuses
				))
		));
		return $this;
	}

	public function getRowUrl($row){
		return $this->getUrl('*/*/edit', array('id' => $row->getId()));
	}
}