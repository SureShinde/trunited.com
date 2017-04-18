<?php

class Magestore_TruBox_Block_Adminhtml_Order_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $this->setForm($form);

        if (Mage::getSingleton('adminhtml/session')->getTruBoxData()) {
            $data = Mage::getSingleton('adminhtml/session')->getTruBoxData();
            Mage::getSingleton('adminhtml/session')->setTruBoxData(null);
        } elseif (Mage::registry('trubox_data'))
            $data = Mage::registry('trubox_data')->getData();

        $fieldSet = $form->addFieldset('trubox_order_form', array('legend' => Mage::helper('trubox')->__('Generating Order Information')));

        $fieldSet->addField('customers', 'textarea', array(
            'label'     => Mage::helper('trubox')->__('List Customers'),
            'class'     => 'required-entry',
            'required'  => true,
            'name'      => 'customers',
            'after_element_html' => '<br /><small>The customers are separated by commas. You are able to enter id of customers. <br /><b>Example: 3493, 3495, 3499..</b></small>',
            'tabindex' => 1
        ));

        $form->setValues($data);
        return parent::_prepareForm();
    }
}