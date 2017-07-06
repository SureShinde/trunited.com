<?php

class Magestore_Manageapi_Block_Adminhtml_Manageapi_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
	public function __construct(){
		parent::__construct();
		
		$this->_objectId = 'id';
		$this->_blockGroup = 'manageapi';
		$this->_controller = 'adminhtml_manageapi';
		
		$this->_updateButton('save', 'label', Mage::helper('manageapi')->__('Run API'));
//		$this->_updateButton('delete', 'label', Mage::helper('manageapi')->__('Delete Item'));

		$this->_formScripts[] = "
			function toggleEditor() {
				if (tinyMCE.getInstanceById('manageapi_content') == null)
					tinyMCE.execCommand('mceAddControl', false, 'manageapi_content');
				else
					tinyMCE.execCommand('mceRemoveControl', false, 'manageapi_content');
			}

			function saveAndContinueEdit(){
				editForm.submit($('edit_form').action+'back/edit/');
			}
		";
	}

	public function getHeaderText(){
		if(Mage::registry('manageapi_data') && Mage::registry('manageapi_data')->getId())
			return Mage::helper('manageapi')->__("Edit Item '%s'", $this->htmlEscape(Mage::registry('manageapi_data')->getTitle()));
		return Mage::helper('manageapi')->__('Run APIs');
	}
}