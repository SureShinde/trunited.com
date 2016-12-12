<?php

class Magestore_Custompromotions_Block_Adminhtml_Custompromotions_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
	protected function _prepareForm(){
		$form = new Varien_Data_Form();
		$this->setForm($form);
		
		if (Mage::getSingleton('adminhtml/session')->getCustompromotionsData()){
			$data = Mage::getSingleton('adminhtml/session')->getCustompromotionsData();
			Mage::getSingleton('adminhtml/session')->setCustompromotionsData(null);
		}elseif(Mage::registry('custompromotions_data'))
			$data = Mage::registry('custompromotions_data')->getData();
		
		$fieldset = $form->addFieldset('custompromotions_form', array('legend'=>Mage::helper('custompromotions')->__('Item information')));

		$fieldset->addField('title', 'text', array(
			'label'		=> Mage::helper('custompromotions')->__('Title'),
			'class'		=> 'required-entry',
			'required'	=> true,
			'name'		=> 'title',
		));

		$fieldset->addField('filename', 'file', array(
			'label'		=> Mage::helper('custompromotions')->__('File'),
			'required'	=> false,
			'name'		=> 'filename',
		));

		$fieldset->addField('status', 'select', array(
			'label'		=> Mage::helper('custompromotions')->__('Status'),
			'name'		=> 'status',
			'values'	=> Mage::getSingleton('custompromotions/status')->getOptionHash(),
		));

		$fieldset->addField('content', 'editor', array(
			'name'		=> 'content',
			'label'		=> Mage::helper('custompromotions')->__('Content'),
			'title'		=> Mage::helper('custompromotions')->__('Content'),
			'style'		=> 'width:700px; height:500px;',
			'wysiwyg'	=> false,
			'required'	=> true,
		));

		$form->setValues($data);
		return parent::_prepareForm();
	}
}