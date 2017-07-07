<?php

class Magestore_TruWallet_Block_Adminhtml_Customer extends Mage_Adminhtml_Block_Widget_Grid_Container
{
	public function __construct(){
		$this->_controller = 'adminhtml_customer';
		$this->_blockGroup = 'truwallet';
		$this->_headerText = Mage::helper('truwallet')->__('Customer Manager');
		$this->_addButtonLabel = Mage::helper('truwallet')->__('Add Customer');
		parent::__construct();
	}
}