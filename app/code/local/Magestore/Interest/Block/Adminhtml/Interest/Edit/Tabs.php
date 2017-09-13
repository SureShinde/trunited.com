<?php

class Magestore_Interest_Block_Adminhtml_Interest_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
	public function __construct(){
		parent::__construct();
		$this->setId('interest_tabs');
		$this->setDestElementId('edit_form');
		$this->setTitle(Mage::helper('interest')->__('Interest Information'));
	}

	protected function _beforeToHtml(){
		$this->addTab('form_section', array(
			'label'	 => Mage::helper('interest')->__('Interest Information'),
			'title'	 => Mage::helper('interest')->__('Interest Information'),
			'content'	 => $this->getLayout()->createBlock('interest/adminhtml_interest_edit_tab_form')->toHtml(),
		));
		return parent::_beforeToHtml();
	}
}
