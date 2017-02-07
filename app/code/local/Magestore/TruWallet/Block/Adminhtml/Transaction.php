<?php

class Magestore_TruWallet_Block_Adminhtml_Transaction extends Mage_Adminhtml_Block_Widget_Grid_Container
{
	public function __construct(){
		$this->_controller = 'adminhtml_transaction';
		$this->_blockGroup = 'truwallet';
		$this->_headerText = Mage::helper('truwallet')->__('Transaction Manager');
		parent::__construct();
		$this->_removeButton('add');
		$this->_addButtonLabel = Mage::helper('truwallet')->__('Export All Orders');
		$this->_addButton('import', array(
			'label' => Mage::helper('truwallet')->__('Import Transactions'),
			'onclick' => "setLocation('" . $this->getUrl('*/*/import', array('page_key' => 'collection')) . "')",
			'class' => 'add'
		));
	}
}