<?php

class Magestore_ManageApi_Block_Adminhtml_Linkshare extends Mage_Adminhtml_Block_Widget_Grid_Container
{
	public function __construct(){
		$this->_controller = 'adminhtml_linkshare';
		$this->_blockGroup = 'manageapi';
		$this->_headerText = Mage::helper('manageapi')->__('API Manager');
		$this->_addButtonLabel = Mage::helper('manageapi')->__('Add Item');
		parent::__construct();
		$this->_removeButton('add');
	}
}