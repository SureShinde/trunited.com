<?php

class Magestore_TruGiftCard_Block_Adminhtml_Transaction extends Mage_Adminhtml_Block_Widget_Grid_Container
{
	public function __construct(){
		$this->_controller = 'adminhtml_transaction';
		$this->_blockGroup = 'trugiftcard';
		$this->_headerText = Mage::helper('trugiftcard')->__('Transaction Manager');
		parent::__construct();
		$this->_removeButton('add');
		$this->_addButtonLabel = Mage::helper('trugiftcard')->__('Export All Orders');
//		$this->_addButton('import', array(
//			'label' => Mage::helper('trugiftcard')->__('Import Transactions'),
//			'onclick' => "setLocation('" . $this->getUrl('*/*/import', array('page_key' => 'collection')) . "')",
//			'class' => 'add'
//		));
	}
}