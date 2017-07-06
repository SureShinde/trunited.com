<?php

class Magestore_ManageApi_Block_Adminhtml_Caractions extends Mage_Adminhtml_Block_Widget_Grid_Container
{
	public function __construct(){
		$this->_controller = 'adminhtml_caractions';
		$this->_blockGroup = 'manageapi';
		$this->_headerText = Mage::helper('manageapi')->__('Manage Price Line Car API');
		$this->_addButtonLabel = Mage::helper('manageapi')->__('Add');
		parent::__construct();
		$this->_removeButton('add');

	}
}