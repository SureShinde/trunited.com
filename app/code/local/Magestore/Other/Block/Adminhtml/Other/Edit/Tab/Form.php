<?php

class Magestore_Other_Block_Adminhtml_Other_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
	protected function _prepareForm(){
		$form = new Varien_Data_Form();
		$this->setForm($form);
		
		if (Mage::getSingleton('adminhtml/session')->getOtherData()){
			$data = Mage::getSingleton('adminhtml/session')->getOtherData();
			Mage::getSingleton('adminhtml/session')->setOtherData(null);
		}elseif(Mage::registry('other_data'))
			$data = Mage::registry('other_data')->getData();
		
		$fieldset = $form->addFieldset('other_form', array('legend'=>Mage::helper('other')->__('Item information')));

		$fieldset->addField('title', 'text', array(
			'label'		=> Mage::helper('other')->__('Title'),
			'class'		=> 'required-entry',
			'required'	=> true,
			'name'		=> 'title',
		));

		$fieldset->addField('filename', 'file', array(
			'label'		=> Mage::helper('other')->__('File'),
			'required'	=> false,
			'name'		=> 'filename',
		));

		$fieldset->addField('status', 'select', array(
			'label'		=> Mage::helper('other')->__('Status'),
			'name'		=> 'status',
			'values'	=> Mage::getSingleton('other/status')->getOptionHash(),
		));

		$fieldset->addField('content', 'editor', array(
			'name'		=> 'content',
			'label'		=> Mage::helper('other')->__('Content'),
			'title'		=> Mage::helper('other')->__('Content'),
			'style'		=> 'width:700px; height:500px;',
			'wysiwyg'	=> false,
			'required'	=> true,
		));

		$form->setValues($data);
		return parent::_prepareForm();
	}
}