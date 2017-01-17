<?php

class Magestore_TruBox_Block_Adminhtml_TruBox_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
	public function __construct(){
		parent::__construct();
		$this->setId('trubox_tabs');
		$this->setDestElementId('edit_form');
		$this->setTitle(Mage::helper('trubox')->__('Item Information'));
	}

	protected function _beforeToHtml(){
		$this->addTab('form_section', array(
			'label'	 => Mage::helper('trubox')->__('Item Information'),
			'title'	 => Mage::helper('trubox')->__('Item Information'),
			'content'	 => $this->getLayout()->createBlock('trubox/adminhtml_trubox_edit_tab_form')->toHtml(),
		));
		return parent::_beforeToHtml();
	}
}