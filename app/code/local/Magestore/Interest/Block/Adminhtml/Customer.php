<?php

class Magestore_Interest_Block_Adminhtml_Customer extends Mage_Adminhtml_Block_Widget_Grid_Container
{
	public function __construct(){
		$this->_controller = 'adminhtml_customer';
		$this->_blockGroup = 'interest';
		$this->_headerText = Mage::helper('interest')->__('Customer Manager');
		$this->_addButtonLabel = Mage::helper('interest')->__('Add Item');
		parent::__construct();
		$this->_removeButton('add');
	}
}
