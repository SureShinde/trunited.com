<?php

class Magestore_TruBox_Block_Adminhtml_Items_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
	public function __construct(){
		parent::__construct();
		
		$this->_objectId = 'id';
		$this->_blockGroup = 'trubox';
		$this->_controller = 'adminhtml_items';
		
		$this->_updateButton('save', 'label', Mage::helper('trubox')->__('Save Item'));
		$this->_updateButton('delete', 'label', Mage::helper('trubox')->__('Delete Item'));
		
		$this->_addButton('saveandcontinue', array(
			'label'		=> Mage::helper('adminhtml')->__('Save And Continue Edit'),
			'onclick'	=> 'saveAndContinueEdit()',
			'class'		=> 'save',
		), -100);

		$this->_formScripts[] = "
			function toggleEditor() {
				if (tinyMCE.getInstanceById('trubox_content') == null)
					tinyMCE.execCommand('mceAddControl', false, 'trubox_content');
				else
					tinyMCE.execCommand('mceRemoveControl', false, 'trubox_content');
			}

			function saveAndContinueEdit(){
				editForm.submit($('edit_form').action+'back/edit/');
			}
		";
	}

	public function getHeaderText(){
		if(Mage::registry('trubox_data') && Mage::registry('trubox_data')->getId())
			return Mage::helper('trubox')->__("Edit Item '%s'", $this->htmlEscape(Mage::registry('trubox_data')->getTitle()));
		return Mage::helper('trubox')->__('Add Item');
	}
}