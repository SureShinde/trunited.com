<?php

class Magestore_SpecialOccasion_Block_Adminhtml_History_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
	public function __construct(){
		parent::__construct();
		$this->setId('historyGrid');
		$this->setDefaultSort('history_id');
		$this->setDefaultDir('ASC');
		$this->setSaveParametersInSession(true);
	}

	protected function _prepareCollection(){
        $collection = Mage::getModel('specialoccasion/history')->getCollection();

        $this->setCollection($collection);
		return parent::_prepareCollection();
	}

	protected function _prepareColumns(){
		$this->addColumn('history_id', array(
			'header'	=> Mage::helper('specialoccasion')->__('ID'),
			'align'	 =>'right',
			'width'	 => '20px',
			'index'	 => 'history_id',
		));

		$this->addColumn('customer_id', array(
			'header'	=> Mage::helper('specialoccasion')->__('Customer ID'),
			'align'	 =>'left',
            'width'	 => '40px',
			'index'	 => 'customer_id',
		));

        $this->addColumn('customer_name', array(
            'header'	=> Mage::helper('specialoccasion')->__('Customer Name'),
            'align'	 =>'left',
            'index'	 => 'customer_name',
            'renderer'  => 'Magestore_SpecialOccasion_Block_Adminhtml_Renderer_History_Name',
        ));

        $this->addColumn('customer_email', array(
            'header'	=> Mage::helper('specialoccasion')->__('Customer Email'),
            'align'	 =>'left',
            'index'	 => 'customer_email',
            'renderer'  => 'Magestore_SpecialOccasion_Block_Adminhtml_Renderer_History_Email',
        ));

		$this->addColumn('order_increment_id', array(
			'header'	=> Mage::helper('specialoccasion')->__('Order ID'),
			'width'	 => '150px',
			'index'	 => 'order_increment_id',
            'renderer'  => 'Magestore_SpecialOccasion_Block_Adminhtml_Renderer_History_Order',
		));

        $this->addColumn('products', array(
            'header'    => Mage::helper('trubox')->__('Product Info'),
            'align'     =>'right',
            'index'     => 'products',
            'filter'	=> false,
            'sortable'	=> false,
            'renderer'  => 'Magestore_SpecialOccasion_Block_Adminhtml_Renderer_History_Product',
        ));

        $this->addColumn('points', array(
            'header'	=> Mage::helper('specialoccasion')->__('Points'),
            'index'	 => 'points',
            'type'  => 'number',
        ));

        $this->addColumn('cost', array(
            'header'	=> Mage::helper('specialoccasion')->__('Grand Total'),
            'index'	 => 'cost',
            'type'  => 'number'
        ));

        $this->addColumn('created_at', array(
            'header'    => Mage::helper('trubox')->__('Created At'),
            'align'     =>'right',
            'index'     => 'created_at',
            'type'		=> 'date'
        ));

        $this->addColumn('updated_at', array(
            'header'    => Mage::helper('trubox')->__('Updated At'),
            'align'     =>'right',
            'index'     => 'updated_at',
            'type'		=> 'date'
        ));

		$this->addExportType('*/*/exportCsv', Mage::helper('specialoccasion')->__('CSV'));
		$this->addExportType('*/*/exportXml', Mage::helper('specialoccasion')->__('XML'));

		return parent::_prepareColumns();
	}

	protected function _prepareMassaction(){
//		/*$this->setMassactionIdField('item_id');
//		$this->getMassactionBlock()->setFormFieldName('specialoccasion');
//
//		$this->getMassactionBlock()->addItem('delete', array(
//			'label'		=> Mage::helper('specialoccasion')->__('Delete'),
//			'url'		=> $this->getUrl('*/*/massDelete'),
//			'confirm'	=> Mage::helper('specialoccasion')->__('Are you sure?')
//		));*/
		return $this;
	}
}
