<?php

class Magestore_Other_Block_Adminhtml_Other extends Mage_Adminhtml_Block_Widget_Grid_Container
{
	public function __construct(){
		$this->_controller = 'adminhtml_other';
		$this->_blockGroup = 'other';
		$this->_headerText = Mage::helper('other')->__('Item Manager');
		$this->_addButtonLabel = Mage::helper('other')->__('Add Item');
		parent::__construct();
	}
}