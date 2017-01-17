<?php

class Magestore_TruBox_Block_Adminhtml_Items_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
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

        $fieldset = $form->addFieldset('trubox_form', array('legend' => Mage::helper('trubox')->__('Item information')));

        $customer_id = Mage::getModel('trubox/trubox')->load($data['trubox_id'])->getCustomerId();
        $customer = Mage::getModel('customer/customer')->load($customer_id);
        $data['customer_name'] = $customer->getName();
        $data['customer_email'] = $customer->getEmail();

        $fieldset->addField('customer_name', 'link', array(
            'label' => Mage::helper('trubox')->__('Customer Name'),
            'style'   => "",
            'href' => Mage::helper("adminhtml")->getUrl("adminhtml/customer/edit",array("id"=>$customer_id)),
            'value'  =>  $customer->getName(),
            'after_element_html' => ''
        ));

        $fieldset->addField('customer_email', 'label', array(
            'label' => Mage::helper('trubox')->__('Customer Email'),
            'name' => 'customer_email',
        ));

        $form->setValues($data);
        return parent::_prepareForm();
    }
}