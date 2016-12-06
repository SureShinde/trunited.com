<?php

class Magestore_Affiliatepluspayperlead_Block_Adminhtml_Affiliatepluspayperlead_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
	protected function _prepareForm(){
		$form = new Varien_Data_Form();
		$this->setForm($form);
		
		if (Mage::getSingleton('adminhtml/session')->getAffiliatepluspayperleadData()){
			$data = Mage::getSingleton('adminhtml/session')->getAffiliatepluspayperleadData();
			Mage::getSingleton('adminhtml/session')->setAffiliatepluspayperleadData(null);
		}elseif(Mage::registry('affiliatepluspayperlead_data'))
			$data = Mage::registry('affiliatepluspayperlead_data')->getData();
		
		$fieldset = $form->addFieldset('affiliatepluspayperlead_form', array('legend'=>Mage::helper('affiliatepluspayperlead')->__('Item information')));

		$fieldset->addField('account_name', 'link', array(
			'label'		=> Mage::helper('affiliatepluspayperlead')->__('Account Name'),
			'href'		=> $this->getUrl('affiliateplus/adminhtml_account/edit', array('_current'=>true, 'id' => $data['account_id'])),
			'name'		=> 'account_name',
		));
		$fieldset->addField('customer_email', 'note', array(
			'label'		=> Mage::helper('affiliatepluspayperlead')->__('Customer Email'),
			'text'		=> '<strong>'.$data['customer_email'].'</strong>',
		));
		$actions = Mage::getSingleton('affiliatepluspayperlead/action')->getOptionArray();
		$fieldset->addField('action', 'note', array(
			'label'   => Mage::helper('affiliatepluspayperlead')->__('Action'),
			'text'	=> '<b>' . $actions[$data['action']] . '</b>',
		));
		
		$fieldset->addField('commission', 'note', array(
			'label' => Mage::helper('affiliatepluspayperlead')->__('Commission'),
			'text'	=> '<strong>'.Mage::helper('core')->currencyByStore($data['commission'], $store).'</strong>',
		));
		$statuses = Mage::getSingleton('affiliatepluspayperlead/status')->getOptionArray();
		$fieldset->addField('status', 'note', array(
			'label'   => Mage::helper('affiliatepluspayperlead')->__('Status'),
			'text'	=> '<b>' . $statuses[$data['status']] . '</b>',
		));


		$form->setValues($data);
		return parent::_prepareForm();
	}
}