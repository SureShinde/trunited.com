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

class Indies_Recurringandrentalpayments_Block_Adminhtml_Subscribed_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

  public function __construct()
  {
      parent::__construct();
      $this->setId('recurringandrentalpayments_tabs');
      $this->setDestElementId('edit_form');
      $this->setTitle(Mage::helper('recurringandrentalpayments')->__('Edit Subscription'));
  }
  

  protected function _beforeToHtml()
  {
	  $this->addTab('info_section', array(
          'label'     => Mage::helper('recurringandrentalpayments')->__('Subscription Information'),
          'title'     => Mage::helper('recurringandrentalpayments')->__('Subscription Information'),
          'content'   => $this->getLayout()->createBlock('recurringandrentalpayments/adminhtml_subscribed_edit_tab_info')->toHtml(),
      ));
	   $this->addTab('payments_section', array(
                                               'label' => $this->__('Subscription Payments'),
                                               'title' => $this->__('Subscription Payments'),
                                               'content' => $this->getLayout()->createBlock('recurringandrentalpayments/adminhtml_subscribed_edit_tab_payments')->toHtml()
                                          )
        );
      return parent::_beforeToHtml();
  }
}