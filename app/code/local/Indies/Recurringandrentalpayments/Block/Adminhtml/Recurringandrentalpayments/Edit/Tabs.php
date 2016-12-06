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

class Indies_Recurringandrentalpayments_Block_Adminhtml_Recurringandrentalpayments_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

  public function __construct()
  {
      parent::__construct();
      $this->setId('recurringandrentalpayments_tabs');
      $this->setDestElementId('edit_form');
      $this->setTitle(Mage::helper('recurringandrentalpayments')->__('Manage Plan'));
  }

  protected function _beforeToHtml()
  {
	  $this->addTab('general_section', array(
		  'label'     => Mage::helper('recurringandrentalpayments')->__('Plan Information'),
		  'title'     => Mage::helper('recurringandrentalpayments')->__('Plan Information'),
		  'content'   => $this->getLayout()->createBlock('recurringandrentalpayments/adminhtml_recurringandrentalpayments_edit_tab_form')->toHtml(),
	  ));
	 
	  $this->addTab('term_section', array(
		  'label'     => Mage::helper('recurringandrentalpayments')->__('Terms'),
		  'title'     => Mage::helper('recurringandrentalpayments')->__('Terms'),
		  'content'   => $this->getLayout()->createBlock('recurringandrentalpayments/adminhtml_recurringandrentalpayments_edit_tab_term')->toHtml(),
	   ));
	   $this->addTab('Product_section', array(
          	 'label'     => Mage::helper('recurringandrentalpayments')->__('Products'),
          	 'title'     => Mage::helper('recurringandrentalpayments')->__('Products'),
          	 'content'   => $this->getLayout()->createBlock('recurringandrentalpayments/adminhtml_recurringandrentalpayments_edit_tab_grid')->toHtml(),
		));
      return parent::_beforeToHtml();
  }
}