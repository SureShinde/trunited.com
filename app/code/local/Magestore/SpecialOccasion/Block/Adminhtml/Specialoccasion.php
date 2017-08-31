<?php

class Magestore_SpecialOccasion_Block_Adminhtml_Specialoccasion extends Mage_Adminhtml_Block_Widget_Grid_Container
{
	public function __construct(){
		$this->_controller = 'adminhtml_specialoccasion';
		$this->_blockGroup = 'specialoccasion';
		$this->_headerText = Mage::helper('specialoccasion')->__('Item Manager');
		$this->_addButtonLabel = Mage::helper('specialoccasion')->__('Add Item');
		parent::__construct();
	}
}
