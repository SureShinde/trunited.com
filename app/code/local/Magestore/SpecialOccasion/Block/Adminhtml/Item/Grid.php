<?php

class Magestore_SpecialOccasion_Block_Adminhtml_Item_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
	public function __construct(){
		parent::__construct();
		$this->setId('itemGrid');
		$this->setDefaultSort('item_id');
		$this->setDefaultDir('ASC');
		$this->setSaveParametersInSession(true);
	}

	protected function _prepareCollection(){
        $productsTableName = Mage::getSingleton('core/resource')->getTableName('catalog_product_entity');
        $productsVarcharTableName = Mage::getSingleton('core/resource')->getTableName('catalog_product_entity_varchar');
		$collection = Mage::getModel('specialoccasion/item')->getCollection();

        $entityTypeId = Mage::getModel('eav/entity')
            ->setType('catalog_product')
            ->getTypeId();
        $prodNameAttrId = Mage::getModel('eav/entity_attribute')
            ->loadByCode($entityTypeId, 'name')
            ->getAttributeId();
        $collection->getSelect()
            ->joinLeft(
                array('prod' => $productsTableName),
                'prod.entity_id = main_table.product_id',
                array('sku')
            )
            ->joinLeft(
                array('cpev' => $productsVarcharTableName),
                'cpev.entity_id=prod.entity_id AND cpev.attribute_id='.$prodNameAttrId.'',
                array('name' => 'value')
            );

        $this->setCollection($collection);
		return parent::_prepareCollection();
	}

	protected function _prepareColumns(){
		$this->addColumn('item_id', array(
			'header'	=> Mage::helper('specialoccasion')->__('ID'),
			'align'	 =>'right',
			'width'	 => '20px',
			'index'	 => 'item_id',
		));

		$this->addColumn('product_id', array(
			'header'	=> Mage::helper('specialoccasion')->__('Product ID'),
			'align'	 =>'left',
            'width'	 => '40px',
			'index'	 => 'product_id',
		));

        $this->addColumn('name', array(
            'header'	=> Mage::helper('specialoccasion')->__('Product Name'),
            'align'	 =>'left',
            'index'	 => 'name',
            'filter_index'  => 'cpev.value',
        ));

        $this->addColumn('sku', array(
            'header'	=> Mage::helper('specialoccasion')->__('Product SKU'),
            'align'	 =>'left',
            'index'	 => 'sku',
            'filter_index'  => 'prod.sku',
        ));

		$this->addColumn('occasion', array(
			'header'	=> Mage::helper('specialoccasion')->__('Occasion'),
			'width'	 => '150px',
			'index'	 => 'occasion',
		));

        $this->addColumn('ship_date', array(
            'header'    => Mage::helper('trubox')->__('Ship Date'),
            'align'     =>'right',
            'index'     => 'ship_date',
            'type'		=> 'date'
        ));

        $this->addColumn('message', array(
            'header'	=> Mage::helper('specialoccasion')->__('Message'),
            'index'	 => 'message',
        ));

		$this->addColumn('status', array(
			'header'	=> Mage::helper('specialoccasion')->__('Status'),
			'align'	 => 'left',
			'width'	 => '80px',
			'index'	 => 'status',
			'type'		=> 'options',
			'options'	 => Magestore_SpecialOccasion_Model_Status::getOptionItemArray(),
		));

        $this->addColumn('state', array(
            'header'	=> Mage::helper('specialoccasion')->__('State'),
            'align'	 => 'left',
            'width'	 => '80px',
            'index'	 => 'state',
            'type'		=> 'options',
            'options'	 => Magestore_SpecialOccasion_Model_Status::getOptionStateArray(),
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
		$this->setMassactionIdField('item_id');
		$this->getMassactionBlock()->setFormFieldName('specialoccasion');

		$this->getMassactionBlock()->addItem('delete', array(
			'label'		=> Mage::helper('specialoccasion')->__('Delete'),
			'url'		=> $this->getUrl('*/*/massDelete'),
			'confirm'	=> Mage::helper('specialoccasion')->__('Are you sure?')
		));

//		$statuses = Mage::getSingleton('specialoccasion/status')->getOptionArray();
//
//		array_unshift($statuses, array('label'=>'', 'value'=>''));
//		$this->getMassactionBlock()->addItem('status', array(
//			'label'=> Mage::helper('specialoccasion')->__('Change status'),
//			'url'	=> $this->getUrl('*/*/massStatus', array('_current'=>true)),
//			'additional' => array(
//				'visibility' => array(
//					'name'	=> 'status',
//					'type'	=> 'select',
//					'class'	=> 'required-entry',
//					'label'	=> Mage::helper('specialoccasion')->__('Status'),
//					'values'=> $statuses
//				))
//		));
		return $this;
	}

//	public function getRowUrl($row){
//		return $this->getUrl('*/*/edit', array('id' => $row->getId()));
//	}
}
