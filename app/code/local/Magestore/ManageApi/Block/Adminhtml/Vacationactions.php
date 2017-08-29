<?php

class Magestore_ManageApi_Block_Adminhtml_Vacationactions extends Mage_Adminhtml_Block_Widget_Grid_Container
{
	public function __construct(){
		$this->_controller = 'adminhtml_vacationactions';
		$this->_blockGroup = 'manageapi';
		$this->_headerText = Mage::helper('manageapi')->__('Manage Price Line Vacation API');
		$this->_addButtonLabel = Mage::helper('manageapi')->__('Add');
		parent::__construct();
		$this->_removeButton('add');

	}
}