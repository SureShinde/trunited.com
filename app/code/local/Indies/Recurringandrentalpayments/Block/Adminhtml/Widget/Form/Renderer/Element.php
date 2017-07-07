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

class Indies_Recurringandrentalpayments_Block_Adminhtml_Widget_Form_Renderer_Element extends Mage_Adminhtml_Block_Widget_Form_Renderer_Fieldset_Element implements Varien_Data_Form_Element_Renderer_Interface
{
    protected function _construct()
    {
        $this->setTemplate('recurringandrentalpayments/widget/form/renderer/element.phtml');
    }
}
