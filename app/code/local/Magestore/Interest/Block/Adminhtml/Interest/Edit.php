<?php

class Magestore_Interest_Block_Adminhtml_Interest_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
	public function __construct(){
		parent::__construct();
		
		$this->_objectId = 'id';
		$this->_blockGroup = 'interest';
		$this->_controller = 'adminhtml_interest';
		
		$this->_updateButton('save', 'label', Mage::helper('interest')->__('Save Item'));
		$this->_updateButton('delete', 'label', Mage::helper('interest')->__('Delete Item'));
		
		$this->_addButton('saveandcontinue', array(
			'label'		=> Mage::helper('adminhtml')->__('Save And Continue Edit'),
			'onclick'	=> 'saveAndContinueEdit()',
			'class'		=> 'save',
		), -100);

		$this->_formScripts[] = "
			function toggleEditor() {
				if (tinyMCE.getInstanceById('interest_content') == null)
					tinyMCE.execCommand('mceAddControl', false, 'interest_content');
				else
					tinyMCE.execCommand('mceRemoveControl', false, 'interest_content');
			}

			function saveAndContinueEdit(){
				editForm.submit($('edit_form').action+'back/edit/');
			}
		";
	}

	public function getHeaderText(){
		if(Mage::registry('interest_data') && Mage::registry('interest_data')->getId())
			return Mage::helper('interest')->__("Edit Item '%s'", $this->htmlEscape(Mage::registry('interest_data')->getTitle()));
		return Mage::helper('interest')->__('Add Item');
	}
}