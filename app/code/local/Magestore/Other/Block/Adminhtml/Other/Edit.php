<?php

class Magestore_Other_Block_Adminhtml_Other_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
	public function __construct(){
		parent::__construct();
		
		$this->_objectId = 'id';
		$this->_blockGroup = 'other';
		$this->_controller = 'adminhtml_other';
		
		$this->_updateButton('save', 'label', Mage::helper('other')->__('Save Item'));
		$this->_updateButton('delete', 'label', Mage::helper('other')->__('Delete Item'));
		
		$this->_addButton('saveandcontinue', array(
			'label'		=> Mage::helper('adminhtml')->__('Save And Continue Edit'),
			'onclick'	=> 'saveAndContinueEdit()',
			'class'		=> 'save',
		), -100);

		$this->_formScripts[] = "
			function toggleEditor() {
				if (tinyMCE.getInstanceById('other_content') == null)
					tinyMCE.execCommand('mceAddControl', false, 'other_content');
				else
					tinyMCE.execCommand('mceRemoveControl', false, 'other_content');
			}

			function saveAndContinueEdit(){
				editForm.submit($('edit_form').action+'back/edit/');
			}
		";
	}

	public function getHeaderText(){
		if(Mage::registry('other_data') && Mage::registry('other_data')->getId())
			return Mage::helper('other')->__("Edit Item '%s'", $this->htmlEscape(Mage::registry('other_data')->getTitle()));
		return Mage::helper('other')->__('Add Item');
	}
}