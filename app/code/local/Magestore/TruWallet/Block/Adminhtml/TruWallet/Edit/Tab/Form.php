<?php

class Magestore_TruWallet_Block_Adminhtml_TruWallet_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
	protected function _prepareForm(){
		$form = new Varien_Data_Form();
		$this->setForm($form);
		
		if (Mage::getSingleton('adminhtml/session')->getTruWalletData()){
			$data = Mage::getSingleton('adminhtml/session')->getTruWalletData();
			Mage::getSingleton('adminhtml/session')->setTruWalletData(null);
		}elseif(Mage::registry('truwallet_data'))
			$data = Mage::registry('truwallet_data')->getData();
		
		$fieldset = $form->addFieldset('truwallet_form', array('legend'=>Mage::helper('truwallet')->__('Item information')));

		$fieldset->addField('title', 'text', array(
			'label'		=> Mage::helper('truwallet')->__('Title'),
			'class'		=> 'required-entry',
			'required'	=> true,
			'name'		=> 'title',
		));

		$fieldset->addField('filename', 'file', array(
			'label'		=> Mage::helper('truwallet')->__('File'),
			'required'	=> false,
			'name'		=> 'filename',
		));

		$fieldset->addField('status', 'select', array(
			'label'		=> Mage::helper('truwallet')->__('Status'),
			'name'		=> 'status',
			'values'	=> Mage::getSingleton('truwallet/status')->getOptionHash(),
		));

		$fieldset->addField('content', 'editor', array(
			'name'		=> 'content',
			'label'		=> Mage::helper('truwallet')->__('Content'),
			'title'		=> Mage::helper('truwallet')->__('Content'),
			'style'		=> 'width:700px; height:500px;',
			'wysiwyg'	=> false,
			'required'	=> true,
		));

		$form->setValues($data);
		return parent::_prepareForm();
	}
}