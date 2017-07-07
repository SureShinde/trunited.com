<?php

class Magestore_Manageapi_Block_Adminhtml_Manageapi_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
	public function __construct(){
		parent::__construct();
		$this->setId('manageapi_tabs');
		$this->setDestElementId('edit_form');
		$this->setTitle(Mage::helper('manageapi')->__('API Information'));
	}

	protected function _beforeToHtml(){
		$this->addTab('form_section', array(
			'label'	 => Mage::helper('manageapi')->__('API Information'),
			'title'	 => Mage::helper('manageapi')->__('API Information'),
			'content'	 => $this->getLayout()->createBlock('manageapi/adminhtml_manageapi_edit_tab_form')->toHtml(),
		));
		return parent::_beforeToHtml();
	}
}