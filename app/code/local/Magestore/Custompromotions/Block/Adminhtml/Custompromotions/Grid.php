<?php

class Magestore_Custompromotions_Block_Adminhtml_Custompromotions_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
	public function __construct(){
		parent::__construct();
		$this->setId('custompromotionsGrid');
		$this->setDefaultSort('custompromotions_id');
		$this->setDefaultDir('DESC');
		$this->setSaveParametersInSession(true);
	}

	protected function _prepareCollection(){
		$collection = Mage::getModel('custompromotions/custompromotions')->getCollection();
		$fn = Mage::getModel('eav/entity_attribute')->loadByCode('1', 'firstname');
		$ln = Mage::getModel('eav/entity_attribute')->loadByCode('1', 'lastname');
		$customer_table = Mage::getSingleton('core/resource')->getTableName('customer_entity');
		$collection->getSelect()
			->join(
				$customer_table,
				'main_table.customer_id = '.$customer_table.'.entity_id', array('customer_email' => 'email')
			);

		$affiliate_table = Mage::getSingleton('core/resource')->getTableName('affiliateplus_account');
		$collection->getSelect()
			->joinLeft(array('ac'=>$affiliate_table), 'main_table.affiliate_id = ac.account_id',array('name'=>'name','email'=>'email'));
		$this->setCollection($collection);
		return parent::_prepareCollection();
	}

	protected function _prepareColumns(){
		$customer_table = Mage::getSingleton('core/resource')->getTableName('customer_entity');
		$this->addColumn('custompromotions_id', array(
			'header'	=> Mage::helper('custompromotions')->__('ID'),
			'align'	 =>'right',
			'width'	 => '50px',
			'index'	 => 'custompromotions_id',
		));

		$this->addColumn('customer_email', array(
			'header'	=> Mage::helper('custompromotions')->__('Customer Email'),
			'index'	 => 'customer_email',
			'filter_index' => $customer_table.'.email',
			'renderer'  => 'Magestore_Custompromotions_Block_Adminhtml_Renderer_Customer',
		));

		$this->addColumn('register_amount', array(
			'header'	=> Mage::helper('custompromotions')->__('Register Amount'),
			'index'	 => 'register_amount',
		));

		$this->addColumn('name', array(
			'header'	=> Mage::helper('custompromotions')->__('Affiliate Name'),
			'width'	 => '150px',
			'index'	 => 'name',
			'renderer'  => 'Magestore_Custompromotions_Block_Adminhtml_Renderer_Affiliate',
		));

		$this->addColumn('email', array(
			'header'	=> Mage::helper('custompromotions')->__('Affiliate Email'),
			'width'	 => '150px',
			'index'	 => 'email',
//			'renderer'  => 'Magestore_Custompromotions_Block_Adminhtml_Renderer_Affiliate',
		));

		$this->addColumn('referred_amount', array(
			'header'	=> Mage::helper('custompromotions')->__('Order Amount'),
			'index'	 => 'referred_amount',
		));

		/*$this->addColumn('type', array(
			'header'	=> Mage::helper('custompromotions')->__('Type'),
			'align'	 => 'left',
			'width'	 => '80px',
			'index'	 => 'type',
			'type'		=> 'options',
			'options'	 => Magestore_Custompromotions_Model_Status::getOptionPromotionArray(),
		));*/

		$this->addColumn('order_id', array(
			'header'	=> Mage::helper('custompromotions')->__('Order ID'),
			'index'	 => 'order_id',
			'renderer'  => 'Magestore_Custompromotions_Block_Adminhtml_Renderer_Order',
		));

		$this->addColumn('created_time', array(
			'header'    => Mage::helper('custompromotions')->__('Created At'),
			'width'     => '180px',
			'align'     =>'right',
			'index'     => 'created_time',
			'type'		=> 'date'
		));


		/*$this->addColumn('status', array(
			'header'	=> Mage::helper('custompromotions')->__('Status'),
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
				'header'	=>	Mage::helper('custompromotions')->__('Action'),
				'width'		=> '100',
				'type'		=> 'action',
				'getter'	=> 'getId',
				'actions'	=> array(
					array(
						'caption'	=> Mage::helper('custompromotions')->__('Edit'),
						'url'		=> array('base'=> '/edit'),
						'field'		=> 'id'
					)),
				'filter'	=> false,
				'sortable'	=> false,
				'index'		=> 'stores',
				'is_system'	=> true,
		));*/

		$this->addExportType('*/*/exportCsv', Mage::helper('custompromotions')->__('CSV'));
		$this->addExportType('*/*/exportXml', Mage::helper('custompromotions')->__('XML'));

		return parent::_prepareColumns();
	}

	protected function _prepareMassaction(){
		$this->setMassactionIdField('custompromotions_id');
		$this->getMassactionBlock()->setFormFieldName('custompromotions');

		$this->getMassactionBlock()->addItem('delete', array(
			'label'		=> Mage::helper('custompromotions')->__('Delete'),
			'url'		=> $this->getUrl('*/*/massDelete'),
			'confirm'	=> Mage::helper('custompromotions')->__('Are you sure?')
		));

		$statuses = Mage::getSingleton('custompromotions/status')->getOptionArray();

		array_unshift($statuses, array('label'=>'', 'value'=>''));
		$this->getMassactionBlock()->addItem('status', array(
			'label'=> Mage::helper('custompromotions')->__('Change status'),
			'url'	=> $this->getUrl('*/*/massStatus', array('_current'=>true)),
			'additional' => array(
				'visibility' => array(
					'name'	=> 'status',
					'type'	=> 'select',
					'class'	=> 'required-entry',
					'label'	=> Mage::helper('custompromotions')->__('Status'),
					'values'=> $statuses
				))
		));
		return $this;
	}

	public function getRowUrl($row){
//		return $this->getUrl('*/*/edit', array('id' => $row->getId()));
	}

	protected function _customerFilter($collection, $column)
	{
		if (!$value = $column->getFilter()->getValue()) {
			return $this;
		}

		$fn = Mage::getModel('eav/entity_attribute')->loadByCode('1', 'firstname');
		$ln = Mage::getModel('eav/entity_attribute')->loadByCode('1', 'lastname');
		$this->getCollection()->getSelect()
			->join(array('ce1' => 'customer_entity_varchar'), 'ce1.entity_id=main_table.customer_id', array('firstname' => 'value'))
			->where('ce1.attribute_id='.$fn->getAttributeId())
			->join(array('ce2' => 'customer_entity_varchar'), 'ce2.entity_id=main_table.customer_id', array('lastname' => 'value'))
			->where('ce2.attribute_id='.$ln->getAttributeId())
			->columns(new Zend_Db_Expr("CONCAT(`ce1`.`value`, ' ',`ce2`.`value`) AS customer_name"));


		return $this;
	}

	protected function _customerNameCondition($collection, $column) {
		if (!$value = trim($column->getFilter()->getValue())) {
			return;
		}
		if (is_numeric($value)) {
			$this->getCollection()->addCustomersToFilter($value);
		} else {
			$inputKeywords = explode(' ', $value);
			$customerIds = array();
			foreach (Mage::getModel('custompromotions/custompromotions')->getCollection() as $key => $item) {

				if (in_array($item->getCustomerId(), $customerIds)) {
					continue;
				}
				$fullname = trim($item->getFullname());
				$match = false;
				if (count($inputKeywords) > 1) { // Multiple name parts found in input.
					foreach ($inputKeywords as $keyword) {
						if (strstr($fullname, $keyword)) {
							$match = true;
						}
					}
				} else { // Single name part found in input.
					$firstname = trim($item->getFirstname());

					$lastname = trim($item->getLastname());
					if (strstr($firstname, $value) || strstr($lastname, $value) || strstr($fullname, $value)) {
						$match = true;
					}
				}
				if ($match) { // Match found, add customer ID to the list to be filtered on.
					$customerIds[] = $item->getCustomerId();
				}
			}
			if (!empty($customerIds)) { // Customer IDs present, filter.
				$this->getCollection()->addCustomersToFilter($customerIds);
			}
		}
	}
}