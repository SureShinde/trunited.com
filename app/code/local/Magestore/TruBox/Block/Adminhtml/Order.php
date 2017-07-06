<?php

class Magestore_TruBox_Block_Adminhtml_Order extends Mage_Adminhtml_Block_Widget_Grid_Container
{
	public function __construct(){
		$this->_controller = 'adminhtml_order';
		$this->_blockGroup = 'trubox';
		$this->_headerText = Mage::helper('trubox')->__('Order Manager');
		$this->_addButtonLabel = Mage::helper('trubox')->__('Generating Orders');
		parent::__construct();
		$this->_removeButton('add');
	}
}