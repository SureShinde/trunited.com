<?php

class Magestore_Nationpassport_Block_Adminhtml_Nationpassport_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
	protected function _prepareForm(){
		$form = new Varien_Data_Form();
		$this->setForm($form);
		
		if (Mage::getSingleton('adminhtml/session')->getNationpassportData()){
			$data = Mage::getSingleton('adminhtml/session')->getNationpassportData();
			Mage::getSingleton('adminhtml/session')->setNationpassportData(null);
		}elseif(Mage::registry('nationpassport_data'))
			$data = Mage::registry('nationpassport_data')->getData();
		
		$fieldset = $form->addFieldset('nationpassport_form', array('legend'=>Mage::helper('nationpassport')->__('Item information')));

		$fieldset->addField('title', 'text', array(
			'label'		=> Mage::helper('nationpassport')->__('Title'),
			'class'		=> 'required-entry',
			'required'	=> true,
			'name'		=> 'title',
		));

		$fieldset->addField('filename', 'file', array(
			'label'		=> Mage::helper('nationpassport')->__('File'),
			'required'	=> false,
			'name'		=> 'filename',
		));

		$fieldset->addField('status', 'select', array(
			'label'		=> Mage::helper('nationpassport')->__('Status'),
			'name'		=> 'status',
			'values'	=> Mage::getSingleton('nationpassport/status')->getOptionHash(),
		));

		$fieldset->addField('content', 'editor', array(
			'name'		=> 'content',
			'label'		=> Mage::helper('nationpassport')->__('Content'),
			'title'		=> Mage::helper('nationpassport')->__('Content'),
			'style'		=> 'width:700px; height:500px;',
			'wysiwyg'	=> false,
			'required'	=> true,
		));

		$form->setValues($data);
		return parent::_prepareForm();
	}
}