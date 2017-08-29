<?php

class Magestore_ManageApi_Block_Adminhtml_Hotelactions extends Mage_Adminhtml_Block_Widget_Grid_Container
{
	public function __construct(){
		$this->_controller = 'adminhtml_hotelactions';
		$this->_blockGroup = 'manageapi';
		$this->_headerText = Mage::helper('manageapi')->__('Manage Price Line Hotel API');
		$this->_addButtonLabel = Mage::helper('manageapi')->__('Add');
		parent::__construct();
		$this->_removeButton('add');
	}
}