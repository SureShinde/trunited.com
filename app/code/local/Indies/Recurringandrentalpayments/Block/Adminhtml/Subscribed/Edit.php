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

class Indies_Recurringandrentalpayments_Block_Adminhtml_Subscribed_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
                 
        $this->_objectId = 'id';
        $this->_blockGroup = 'recurringandrentalpayments';
        $this->_controller = 'adminhtml_subscribed';
		$this->_mode = 'edit';
        $this->_removeButton('delete');
        $this->_updateButton('save', 'label', $this->__('Save'));
    }

    public function getHeaderText()
    {
       return $this->__("");
    }
	public function getBackUrl()
    {
        return Mage::getSingleton('adminhtml/url')->getUrl('*/*/');
    }

    public function getDeleteUrl()
    {
        return Mage::getUrl('*/*/delete', array('id' => $this->getRequest()->getParam('id')));
    }
}