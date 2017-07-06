<?php
/**
*
* Do not edit or add to this file if you wish to upgrade the module to newer
* versions in the future. If you wish to customize the module for your
* needs please contact us to https://www.milople.com/magento-extensions/contacts/
*
* @category     Ecommerce
* @package      Indies_Recurringandrentalpayments
* @copyright    Copyright (c) 2015 Milople Technologies Pvt. Ltd. All Rights Reserved.
* @url          https://www.milople.com/magento-extensions/recurring-and-subscription-payments.html
*
* Milople was known as Indies Services earlier.
*
**/

class Indies_Recurringandrentalpayments_Block_Adminhtml_Recurringandrentalpayments_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
      $form = new Varien_Data_Form();
      $this->setForm($form);
      $fieldset = $form->addFieldset('recurringandrentalpayments_form', array('legend'=>Mage::helper('recurringandrentalpayments')->__('Plan Information')));
 
	  $fieldset->addField('plan_name', 'text', array(
          'label'     => Mage::helper('recurringandrentalpayments')->__('Plan Name '),
          'name'      => 'plan_name',
		  'class'     => 'required-entry',
          'required'  => true,
   	   ));

	 $fieldset->addField('is_normal', 'select', array(
          'label'     => Mage::helper('recurringandrentalpayments')->__('Allow Purchase as Normal Product'),
          'name'      => 'is_normal',
          'values'    => array(
              array(
                  'value'     => '1',
                  'label'     => Mage::helper('recurringandrentalpayments')->__('Yes'),
              ),
			array(
                  'value'     => '0',
                  'label'     => Mage::helper('recurringandrentalpayments')->__('No'),
              ),
          ),
      ));
	  $fieldset->addField('start_date', 'select', array(
          'label'     => Mage::helper('recurringandrentalpayments')->__('Subscription Start Date'),
          'name'      => 'start_date',
          'values'    => array(
              array(
                  'value'     => '1',
                  'label'     => Mage::helper('recurringandrentalpayments')->__('Selected by Customer'),
              ),
			array(
                  'value'     => '2',
                  'label'     => Mage::helper('recurringandrentalpayments')->__('Moment of Purchase'),
              ),
			  array(
                  'value'     => '3',
                  'label'     => Mage::helper('recurringandrentalpayments')->__('First Day of Next Month'),
              ),
          ),
      ));
	  $fieldset->addField('plan_status', 'select', array(
          'label'     => Mage::helper('recurringandrentalpayments')->__('Status'),
          'name'      => 'plan_status',
          'values'    => array(
              array(
                  'value'     => '1',
                  'label'     => Mage::helper('recurringandrentalpayments')->__('Enable'),
              ),
			array(
                  'value'     => '2',
                  'label'     => Mage::helper('recurringandrentalpayments')->__('Disable'),
              ),
          ),
      ));
	 
     
      if ( Mage::getSingleton('adminhtml/session')->getRecurringandrentalpaymentsData() )
      {
         
		  $form->setValues(Mage::getSingleton('adminhtml/session')->getRecurringandrentalpaymentsData());
          Mage::getSingleton('adminhtml/session')->setRecurringandrentalpaymentsData(null);
      } elseif ( Mage::registry('recurringandrentalpayments_data') ) {
          $form->setValues(Mage::registry('recurringandrentalpayments_data')->getData());
      }
      return parent::_prepareForm();
  }
}