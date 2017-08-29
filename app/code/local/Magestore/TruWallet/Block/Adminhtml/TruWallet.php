<?php

class Magestore_TruWallet_Block_Adminhtml_TruWallet extends Mage_Adminhtml_Block_Widget_Grid_Container
{
	public function __construct(){
		$this->_controller = 'adminhtml_truwallet';
		$this->_blockGroup = 'truwallet';
		$this->_headerText = Mage::helper('truwallet')->__('Item Manager');
		$this->_addButtonLabel = Mage::helper('truwallet')->__('Add Item');
		parent::__construct();
	}
}