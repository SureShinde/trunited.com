<?php

class Magestore_TruWallet_Block_Adminhtml_Customer_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
	public function __construct(){
		parent::__construct();
		
		$this->_objectId = 'id';
		$this->_blockGroup = 'truwallet';
		$this->_controller = 'adminhtml_customer';
		
		$this->_updateButton('save', 'label', Mage::helper('truwallet')->__('Save Customer'));
		$this->_updateButton('delete', 'label', Mage::helper('truwallet')->__('Delete Customer'));
		
		$this->_addButton('saveandcontinue', array(
			'label'		=> Mage::helper('adminhtml')->__('Save And Continue Edit'),
			'onclick'	=> 'saveAndContinueEdit()',
			'class'		=> 'save',
		), -100);

		$this->_formScripts[] = "
			function toggleEditor() {
				if (tinyMCE.getInstanceById('truwallet_content') == null)
					tinyMCE.execCommand('mceAddControl', false, 'truwallet_content');
				else
					tinyMCE.execCommand('mceRemoveControl', false, 'truwallet_content');
			}

			function saveAndContinueEdit(){
				editForm.submit($('edit_form').action+'back/edit/');
			}
		";
	}

	public function getHeaderText(){
		if(Mage::registry('customer_data') && Mage::registry('customer_data')->getId())
			return Mage::helper('truwallet')->__("Edit Customer '%s'", $this->htmlEscape(Mage::registry('customer_data')->getTitle()));
		return Mage::helper('truwallet')->__('Add Customer');
	}
}