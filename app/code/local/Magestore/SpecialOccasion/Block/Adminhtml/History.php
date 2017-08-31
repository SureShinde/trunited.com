<?php

class Magestore_SpecialOccasion_Block_Adminhtml_History extends Mage_Adminhtml_Block_Widget_Grid_Container
{
	public function __construct(){
		$this->_controller = 'adminhtml_history';
		$this->_blockGroup = 'specialoccasion';
		$this->_headerText = Mage::helper('specialoccasion')->__('History Manager');
		$this->_addButtonLabel = Mage::helper('specialoccasion')->__('Add Item');
		parent::__construct();
		$this->_removeButton('add');
	}
}
