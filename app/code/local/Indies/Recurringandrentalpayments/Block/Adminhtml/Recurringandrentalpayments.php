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

class Indies_Recurringandrentalpayments_Block_Adminhtml_Recurringandrentalpayments extends Mage_Adminhtml_Block_Widget_Grid_Container
{
	  public function __construct()
	  {
			$this->_controller = 'adminhtml_recurringandrentalpayments';
			$this->_blockGroup = 'recurringandrentalpayments';
			$this->_headerText = Mage::helper('recurringandrentalpayments')->__('Manage Plans');
			$this->_addButtonLabel = Mage::helper('recurringandrentalpayments')->__('Add Plan');
			parent::__construct();
	  }
}