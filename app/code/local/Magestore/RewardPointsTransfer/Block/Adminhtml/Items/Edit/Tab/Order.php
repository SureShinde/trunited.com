<?php

class Magestore_TruBox_Block_Adminhtml_Items_Edit_Tab_Order extends Mage_Adminhtml_Block_Widget_Form
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

		$form->setValues($data);
		return parent::_prepareForm();
	}
}