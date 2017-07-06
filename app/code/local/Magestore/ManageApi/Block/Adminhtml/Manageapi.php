<?php

class Magestore_Manageapi_Block_Adminhtml_Manageapi extends Mage_Adminhtml_Block_Widget_Grid_Container
{
	public function __construct(){
		$this->_controller = 'adminhtml_manageapi';
		$this->_blockGroup = 'manageapi';
		$this->_headerText = Mage::helper('manageapi')->__('Item Manager');
		$this->_addButtonLabel = Mage::helper('manageapi')->__('Add Item');
		parent::__construct();
		$this->_removeButton('add');
	}
}