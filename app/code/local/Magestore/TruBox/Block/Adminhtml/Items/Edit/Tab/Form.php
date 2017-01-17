<?php

class Magestore_TruBox_Block_Adminhtml_TruBox_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
	protected function _prepareForm(){
		$form = new Varien_Data_Form();
		$this->setForm($form);
		
		if (Mage::getSingleton('adminhtml/session')->getTruBoxData()){
			$data = Mage::getSingleton('adminhtml/session')->getTruBoxData();
			Mage::getSingleton('adminhtml/session')->setTruBoxData(null);
		}elseif(Mage::registry('trubox_data'))
			$data = Mage::registry('trubox_data')->getData();
		
		$fieldset = $form->addFieldset('trubox_form', array('legend'=>Mage::helper('trubox')->__('Item information')));

		$fieldset->addField('title', 'text', array(
			'label'		=> Mage::helper('trubox')->__('Title'),
			'class'		=> 'required-entry',
			'required'	=> true,
			'name'		=> 'title',
		));

		$fieldset->addField('filename', 'file', array(
			'label'		=> Mage::helper('trubox')->__('File'),
			'required'	=> false,
			'name'		=> 'filename',
		));

		$fieldset->addField('status', 'select', array(
			'label'		=> Mage::helper('trubox')->__('Status'),
			'name'		=> 'status',
			'values'	=> Mage::getSingleton('trubox/status')->getOptionHash(),
		));

		$fieldset->addField('content', 'editor', array(
			'name'		=> 'content',
			'label'		=> Mage::helper('trubox')->__('Content'),
			'title'		=> Mage::helper('trubox')->__('Content'),
			'style'		=> 'width:700px; height:500px;',
			'wysiwyg'	=> false,
			'required'	=> true,
		));

		$form->setValues($data);
		return parent::_prepareForm();
	}
}