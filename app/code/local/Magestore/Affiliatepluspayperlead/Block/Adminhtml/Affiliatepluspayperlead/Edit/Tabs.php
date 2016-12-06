<?php

class Magestore_Affiliatepluspayperlead_Block_Adminhtml_Affiliatepluspayperlead_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
	public function __construct(){
		parent::__construct();
		$this->setId('affiliatepluspayperlead_tabs');
		$this->setDestElementId('edit_form');
		$this->setTitle(Mage::helper('affiliatepluspayperlead')->__('Lead Information'));
	}

	protected function _beforeToHtml(){
		$this->addTab('form_section', array(
			'label'	 => Mage::helper('affiliatepluspayperlead')->__('Lead Information'),
			'title'	 => Mage::helper('affiliatepluspayperlead')->__('Lead Information'),
			'content'	 => $this->getLayout()->createBlock('affiliatepluspayperlead/adminhtml_affiliatepluspayperlead_edit_tab_form')->toHtml(),
		));
		return parent::_beforeToHtml();
	}
}