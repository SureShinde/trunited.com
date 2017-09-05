<?php

class Magestore_Interest_Block_Adminhtml_Interest_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
	protected function _prepareForm(){
		$form = new Varien_Data_Form();
		$this->setForm($form);
		
		if (Mage::getSingleton('adminhtml/session')->getInterestData()){
			$data = Mage::getSingleton('adminhtml/session')->getInterestData();
			Mage::getSingleton('adminhtml/session')->setInterestData(null);
		}elseif(Mage::registry('interest_data'))
			$data = Mage::registry('interest_data')->getData();
		
		$fieldset = $form->addFieldset('interest_form', array('legend'=>Mage::helper('interest')->__('Item information')));

		$fieldset->addField('title', 'text', array(
			'label'		=> Mage::helper('interest')->__('Title'),
			'class'		=> 'required-entry',
			'required'	=> true,
			'name'		=> 'title',
		));

        $fieldset->addField('sort_order', 'text', array(
            'label'		=> Mage::helper('interest')->__('Sort Order'),
            'required'	=> false,
            'name'		=> 'sort_order',
        ));

		$fieldset->addField('status', 'select', array(
			'label'		=> Mage::helper('interest')->__('Status'),
			'name'		=> 'status',
			'values'	=> Mage::getSingleton('interest/status')->getOptionHash(),
		));

		$form->setValues($data);
		return parent::_prepareForm();
	}
}
