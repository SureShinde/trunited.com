<?php

class Magestore_TruBox_Block_Adminhtml_Order_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
	public function __construct(){
		parent::__construct();
		$this->setId('items_tabs');
		$this->setDestElementId('edit_form');
		$this->setTitle(Mage::helper('trubox')->__('Order Information'));
	}

	protected function _beforeToHtml(){
		$this->addTab('form_section', array(
			'label'	 => Mage::helper('trubox')->__('Order Information'),
			'title'	 => Mage::helper('trubox')->__('Order Information'),
			'content'	 => $this->getLayout()->createBlock('trubox/adminhtml_order_edit_tab_form')->toHtml(),
		));
		return parent::_beforeToHtml();
	}
}