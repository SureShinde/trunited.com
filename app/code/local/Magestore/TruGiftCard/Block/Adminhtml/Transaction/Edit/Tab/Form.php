<?php

class Magestore_TruGiftCard_Block_Adminhtml_Transaction_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
	protected function _prepareForm(){
		$form = new Varien_Data_Form();
		$this->setForm($form);

		if (Mage::getSingleton('adminhtml/session')->getTransactionData()){
			$data = Mage::getSingleton('adminhtml/session')->getTransactionData();
			Mage::getSingleton('adminhtml/session')->setTransactionData(null);
		}elseif(Mage::registry('transaction_data'))
			$data = Mage::registry('transaction_data')->getData();

		$customer = Mage::getModel('customer/customer')->load($data['customer_id']);
		$fieldset = $form->addFieldset('transaction_form', array('legend'=>Mage::helper('trugiftcard')->__('Transaction information')));

		$url = '#';
		if($customer != null && $customer->getId()){
			$data['customer_name'] = $customer->getName();
			$url = Mage::helper('adminhtml')->getUrl('adminhtml/customer/edit', array('id' => $customer->getId()));
		}

		$fieldset->addField('customer_name', 'link', array(
			'label' => Mage::helper('trugiftcard')->__('Customer Name'),
			'href' => $url,
		));

		$fieldset->addField('customer_email', 'link', array(
			'label' => Mage::helper('trugiftcard')->__('Customer Email'),
			'href' => $url,
		));

		$data['current_credit'] = Mage::helper('core')->currency(
			$data['current_credit'],
			true,
			false
		);
		$fieldset->addField('current_credit', 'label', array(
			'label' => Mage::helper('trugiftcard')->__('Current Credit'),
			'style' => 'font-weight: bold;'
		));

		$data['changed_credit'] = Mage::helper('core')->currency(
			$data['changed_credit'],
			true,
			false
		);
		$fieldset->addField('changed_credit', 'label', array(
			'label' => Mage::helper('trugiftcard')->__('Updated Credit'),
			'style' => 'font-weight: bold'
		));

		$fieldset->addField('title', 'label', array(
			'label'		=> Mage::helper('trugiftcard')->__('Title'),
			//'value'
		));

		$fieldset->addField('action_type', 'select', array(
			'label'		=> Mage::helper('trugiftcard')->__('Type'),
			'name'		=> 'action_type',
			'values' => Magestore_TruGiftCard_Model_Type::getOptionArray(),
			'disabled' => true,
			'readonly' => true,
		));

		$fieldset->addField('status', 'select', array(
			'label'		=> Mage::helper('trugiftcard')->__('Status'),
			'name'		=> 'status',
			'values'	=> Magestore_TruGiftCard_Model_Status::getTransactionOptionArray(),
			'disabled' => true,
			'readonly' => true,
		));

		$fieldset->addField('created_time', 'label', array(
			'label'		=> Mage::helper('trugiftcard')->__('Created On'),
		));

		$fieldset->addField('updated_time', 'label', array(
			'label'		=> Mage::helper('trugiftcard')->__('Updated On'),
		));

		$fieldset->addField('expiration_date', 'label', array(
			'label'		=> Mage::helper('trugiftcard')->__('Expiration On'),
		));

		$order_url = '';
		if($data['order_id'] != null)
		{
			$order_url = Mage::helper('adminhtml')->getUrl('adminhtml/sales_order/edit', array('order_id' => $data['order_id']));
		}
		$fieldset->addField('order_id', 'link', array(
			'label' => Mage::helper('trugiftcard')->__('Order Id'),
			'href' => $order_url,
		));

		$receiver = Mage::getModel('customer/customer')->load($data['receiver_customer_id']);
		$receiver_url = '#';
		if($receiver->getId())
		{
			$receiver_url = Mage::helper('adminhtml')->getUrl('adminhtml/customer/edit', array('id' => $receiver->getId()));
		}
		$fieldset->addField('receiver_email', 'link', array(
			'label' => Mage::helper('trugiftcard')->__('Receiver Email'),
			'href' => $receiver_url
		));

		$form->setValues($data);
		return parent::_prepareForm();
	}
}