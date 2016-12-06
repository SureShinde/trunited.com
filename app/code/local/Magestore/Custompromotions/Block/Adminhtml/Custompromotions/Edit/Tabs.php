<?php

class Magestore_Custompromotions_Block_Adminhtml_Custompromotions_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
	public function __construct(){
		parent::__construct();
		$this->setId('custompromotions_tabs');
		$this->setDestElementId('edit_form');
		$this->setTitle(Mage::helper('custompromotions')->__('Item Information'));
	}

	protected function _beforeToHtml(){
		$this->addTab('form_section', array(
			'label'	 => Mage::helper('custompromotions')->__('Item Information'),
			'title'	 => Mage::helper('custompromotions')->__('Item Information'),
			'content'	 => $this->getLayout()->createBlock('custompromotions/adminhtml_custompromotions_edit_tab_form')->toHtml(),
		));
		return parent::_beforeToHtml();
	}
}