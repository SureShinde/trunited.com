<?php

class Magestore_Nationpassport_Block_Adminhtml_Nationpassport extends Mage_Adminhtml_Block_Widget_Grid_Container
{
	public function __construct(){
		$this->_controller = 'adminhtml_nationpassport';
		$this->_blockGroup = 'nationpassport';
		$this->_headerText = Mage::helper('nationpassport')->__('Item Manager');
		$this->_addButtonLabel = Mage::helper('nationpassport')->__('Add Item');
		parent::__construct();
	}
}