<?php

class Magestore_TruWallet_Block_Adminhtml_Customer_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
	public function __construct(){
		parent::__construct();
		$this->setId('customer_tabs');
		$this->setDestElementId('edit_form');
		$this->setTitle(Mage::helper('truwallet')->__('Customer Information'));
	}

	protected function _beforeToHtml(){
		$this->addTab('form_section', array(
			'label'	 => Mage::helper('truwallet')->__('Item Information'),
			'title'	 => Mage::helper('truwallet')->__('Item Information'),
			'content'	 => $this->getLayout()->createBlock('truwallet/adminhtml_customer_edit_tab_form')->toHtml(),
		));
		return parent::_beforeToHtml();
	}
}