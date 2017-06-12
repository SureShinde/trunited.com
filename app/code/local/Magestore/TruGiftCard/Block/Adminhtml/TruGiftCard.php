<?php

class Magestore_TruGiftCard_Block_Adminhtml_TruGiftCard extends Mage_Adminhtml_Block_Widget_Grid_Container
{
	public function __construct(){
		$this->_controller = 'adminhtml_trugiftcard';
		$this->_blockGroup = 'trugiftcard';
		$this->_headerText = Mage::helper('trugiftcard')->__('Item Manager');
		$this->_addButtonLabel = Mage::helper('trugiftcard')->__('Add Item');
		parent::__construct();
	}
}