<?php

class Magestore_TruWallet_Block_Adminhtml_Transaction extends Mage_Adminhtml_Block_Widget_Grid_Container
{
	public function __construct(){
		$this->_controller = 'adminhtml_transaction';
		$this->_blockGroup = 'truwallet';
		$this->_headerText = Mage::helper('truwallet')->__('Transaction Manager');
		$this->_addButtonLabel = Mage::helper('truwallet')->__('Add Transaction');
		parent::__construct();
	}
}