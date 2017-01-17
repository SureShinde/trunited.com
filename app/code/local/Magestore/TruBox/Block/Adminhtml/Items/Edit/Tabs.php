<?php

class Magestore_TruBox_Block_Adminhtml_Items_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
	public function __construct(){
		parent::__construct();
		$this->setId('items_tabs');
		$this->setDestElementId('edit_form');
		$this->setTitle(Mage::helper('trubox')->__('Item Information'));
	}

	protected function _beforeToHtml(){
		$this->addTab('form_section', array(
			'label'	 => Mage::helper('trubox')->__('Customer Information'),
			'title'	 => Mage::helper('trubox')->__('Customer Information'),
			'content'	 => $this->getLayout()->createBlock('trubox/adminhtml_items_edit_tab_form')->toHtml(),
		));

		$this->addTab('items_section', array(
			'label'	 => Mage::helper('trubox')->__('Items Information'),
			'title'	 => Mage::helper('trubox')->__('Items Information'),
			'content'	 => $this->getLayout()->createBlock('trubox/adminhtml_items_edit_tab_item')->toHtml(),
		));

		$this->addTab('order_section', array(
			'label'	 => Mage::helper('trubox')->__('Order Information'),
			'title'	 => Mage::helper('trubox')->__('Order Information'),
			'content'	 => $this->getLayout()->createBlock('trubox/adminhtml_items_edit_tab_order')->toHtml(),
		));
		return parent::_beforeToHtml();
	}
}