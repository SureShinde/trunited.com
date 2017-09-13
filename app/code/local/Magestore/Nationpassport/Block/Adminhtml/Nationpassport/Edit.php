<?php

class Magestore_Nationpassport_Block_Adminhtml_Nationpassport_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
	public function __construct(){
		parent::__construct();
		
		$this->_objectId = 'id';
		$this->_blockGroup = 'nationpassport';
		$this->_controller = 'adminhtml_nationpassport';
		
		$this->_updateButton('save', 'label', Mage::helper('nationpassport')->__('Save Item'));
		$this->_updateButton('delete', 'label', Mage::helper('nationpassport')->__('Delete Item'));
		
		$this->_addButton('saveandcontinue', array(
			'label'		=> Mage::helper('adminhtml')->__('Save And Continue Edit'),
			'onclick'	=> 'saveAndContinueEdit()',
			'class'		=> 'save',
		), -100);

		$this->_formScripts[] = "
			function toggleEditor() {
				if (tinyMCE.getInstanceById('nationpassport_content') == null)
					tinyMCE.execCommand('mceAddControl', false, 'nationpassport_content');
				else
					tinyMCE.execCommand('mceRemoveControl', false, 'nationpassport_content');
			}

			function saveAndContinueEdit(){
				editForm.submit($('edit_form').action+'back/edit/');
			}
		";
	}

	public function getHeaderText(){
		if(Mage::registry('nationpassport_data') && Mage::registry('nationpassport_data')->getId())
			return Mage::helper('nationpassport')->__("Edit Item '%s'", $this->htmlEscape(Mage::registry('nationpassport_data')->getTitle()));
		return Mage::helper('nationpassport')->__('Add Item');
	}
}