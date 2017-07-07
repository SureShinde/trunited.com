<?php

class Magestore_TruGiftCard_Block_Adminhtml_TruGiftCard_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
	public function __construct(){
		parent::__construct();
		$this->setId('trugiftcard_tabs');
		$this->setDestElementId('edit_form');
		$this->setTitle(Mage::helper('trugiftcard')->__('Item Information'));
	}

	protected function _beforeToHtml(){
		$this->addTab('form_section', array(
			'label'	 => Mage::helper('trugiftcard')->__('Item Information'),
			'title'	 => Mage::helper('trugiftcard')->__('Item Information'),
			'content'	 => $this->getLayout()->createBlock('trugiftcard/adminhtml_trugiftcard_edit_tab_form')->toHtml(),
		));
		return parent::_beforeToHtml();
	}
}