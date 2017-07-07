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

class Indies_Recurringandrentalpayments_Block_Adminhtml_Recurringandrentalpayments_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
                 
        $this->_objectId = 'id';
        $this->_blockGroup = 'recurringandrentalpayments';
        $this->_controller = 'adminhtml_recurringandrentalpayments';
        $helper = Mage::helper('recurringandrentalpayments');
		
		$this->_updateButton('save', '', array(
            'label' => $helper->__('Save Plan'),
            'onclick' => 'saveOptionsForm()',
            'class' => 'save',
            'sort_order' => 10
            ), 1);

        $this->_updateButton('delete', '', array(
            'label' => $helper->__('Delete Plan'),
       		'onclick' => "deleteConfirm('{$helper->__('If you delete this item(s) all the options inside will be deleted as well?')}', '{$this->getUrl('*/*/delete', array('id' => (int) $this->getRequest()->getParam('id')))}')",
            'class' => 'delete',
            'sort_order' => 10
        ));

        $this->_addButton('saveandcontinue', array(
            'label' => $helper->__('Save And Continue Edit'),
            'onclick' => 'saveAndContinueEdit()',
            'class' => 'save',
            'sort_order' => 10
                ), -100);

		$this->_formScripts[] = "
            function saveOptionsForm() {
                applySelectedProducts('save')
            }
            function saveAndContinueEdit() {
                applySelectedProducts('saveandcontinue')
            }
        ";
    }

    public function getHeaderText()
    {
		if( Mage::registry('recurringandrentalpayments_data') &&  Mage::registry('recurringandrentalpayments_data')->getId() ) {
            return Mage::helper('recurringandrentalpayments')->__("Edit Plan '%s'", $this->htmlEscape(Mage::registry('recurringandrentalpayments_data')->getPlanName()));
        } else {
            return Mage::helper('recurringandrentalpayments')->__('Add New Plan');
        }
    }
}