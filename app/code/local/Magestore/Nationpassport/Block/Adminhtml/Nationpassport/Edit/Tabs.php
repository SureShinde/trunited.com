<?php

class Magestore_Nationpassport_Block_Adminhtml_Nationpassport_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
	public function __construct(){
		parent::__construct();
		$this->setId('nationpassport_tabs');
		$this->setDestElementId('edit_form');
		$this->setTitle(Mage::helper('nationpassport')->__('Item Information'));
	}

	protected function _beforeToHtml(){
		$this->addTab('form_section', array(
			'label'	 => Mage::helper('nationpassport')->__('Item Information'),
			'title'	 => Mage::helper('nationpassport')->__('Item Information'),
			'content'	 => $this->getLayout()->createBlock('nationpassport/adminhtml_nationpassport_edit_tab_form')->toHtml(),
		));
		return parent::_beforeToHtml();
	}
}