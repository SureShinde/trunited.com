<?php

class Magestore_Other_Block_Adminhtml_Other_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
	public function __construct(){
		parent::__construct();
		$this->setId('other_tabs');
		$this->setDestElementId('edit_form');
		$this->setTitle(Mage::helper('other')->__('Item Information'));
	}

	protected function _beforeToHtml(){
		$this->addTab('form_section', array(
			'label'	 => Mage::helper('other')->__('Item Information'),
			'title'	 => Mage::helper('other')->__('Item Information'),
			'content'	 => $this->getLayout()->createBlock('other/adminhtml_other_edit_tab_form')->toHtml(),
		));
		return parent::_beforeToHtml();
	}
}