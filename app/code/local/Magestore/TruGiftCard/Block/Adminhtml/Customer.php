<?php

class Magestore_TruGiftCard_Block_Adminhtml_Customer extends Mage_Adminhtml_Block_Widget_Grid_Container
{
	public function __construct(){
		$this->_controller = 'adminhtml_customer';
		$this->_blockGroup = 'trugiftcard';
		$this->_headerText = Mage::helper('trugiftcard')->__('Customer Manager');
		$this->_addButtonLabel = Mage::helper('trugiftcard')->__('Add Customer');
		parent::__construct();
	}
}