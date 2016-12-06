<?php

class Magestore_Affiliatepluspayperlead_Block_Adminhtml_Affiliatepluspayperlead_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
	public function __construct(){
		parent::__construct();
		
		$this->_objectId = 'id';
		$this->_blockGroup = 'affiliatepluspayperlead';
		$this->_controller = 'adminhtml_affiliatepluspayperlead';
		
		$this->_removeButton('save');
        $this->_removeButton('delete');
		$this->_removeButton('reset');
		$this->_formScripts[] = "
			function toggleEditor() {
				if (tinyMCE.getInstanceById('affiliatepluspayperlead_content') == null)
					tinyMCE.execCommand('mceAddControl', false, 'affiliatepluspayperlead_content');
				else
					tinyMCE.execCommand('mceRemoveControl', false, 'affiliatepluspayperlead_content');
			}

			function saveAndContinueEdit(){
				editForm.submit($('edit_form').action+'back/edit/');
			}
		";
	}

	public function getHeaderText(){
		if(Mage::registry('affiliatepluspayperlead_data') && Mage::registry('affiliatepluspayperlead_data')->getId())
			return Mage::helper('affiliatepluspayperlead')->__("Edit Lead of '%s'", $this->htmlEscape(Mage::registry('affiliatepluspayperlead_data')->getAccountName()));
		return Mage::helper('affiliatepluspayperlead')->__('Add Lead');
	}
}