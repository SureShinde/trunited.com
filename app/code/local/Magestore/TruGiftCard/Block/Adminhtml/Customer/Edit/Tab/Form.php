<?php

class Magestore_TruGiftCard_Block_Adminhtml_Customer_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
	protected function _prepareForm(){
		$form = new Varien_Data_Form();
		$this->setForm($form);
		
		if (Mage::getSingleton('adminhtml/session')->getCustomerData()){
			$data = Mage::getSingleton('adminhtml/session')->getCustomerData();
			Mage::getSingleton('adminhtml/session')->setCustomerData(null);
		}elseif(Mage::registry('customer_data'))
			$data = Mage::registry('customer_data')->getData();
		
		$fieldset = $form->addFieldset('customer_form', array('legend'=>Mage::helper('trugiftcard')->__('Customer information')));

		$fieldset->addField('title', 'text', array(
			'label'		=> Mage::helper('trugiftcard')->__('Title'),
			'class'		=> 'required-entry',
			'required'	=> true,
			'name'		=> 'title',
		));

		$fieldset->addField('filename', 'file', array(
			'label'		=> Mage::helper('trugiftcard')->__('File'),
			'required'	=> false,
			'name'		=> 'filename',
		));

		$fieldset->addField('status', 'select', array(
			'label'		=> Mage::helper('trugiftcard')->__('Status'),
			'name'		=> 'status',
			'values'	=> Mage::getSingleton('trugiftcard/status')->getOptionHash(),
		));

		$fieldset->addField('content', 'editor', array(
			'name'		=> 'content',
			'label'		=> Mage::helper('trugiftcard')->__('Content'),
			'title'		=> Mage::helper('trugiftcard')->__('Content'),
			'style'		=> 'width:700px; height:500px;',
			'wysiwyg'	=> false,
			'required'	=> true,
		));

		$form->setValues($data);
		return parent::_prepareForm();
	}
}