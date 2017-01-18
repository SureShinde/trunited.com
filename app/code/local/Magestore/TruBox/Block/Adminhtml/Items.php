<?php

class Magestore_TruBox_Block_Adminhtml_Items extends Mage_Adminhtml_Block_Widget_Grid_Container
{
	public function __construct(){
		$this->_controller = 'adminhtml_items';
		$this->_blockGroup = 'trubox';
		$this->_headerText = Mage::helper('trubox')->__('Item Manager');
		$this->_addButtonLabel = Mage::helper('trubox')->__('Generating Orders');
		parent::__construct();
 $this->_removeButton('add');
		$location = Mage::helper('adminhtml')->getUrl('truboxadmin/adminhtml_items/generateOrders');
		/* $this->_addButton('add', array(
			'label'     => Mage::helper('trubox')->__('Generating Orders'),
			'onclick'   => 'setLocation(\'' . $location .'\')',
			'class'     => 'add',
		)); */
	}
}