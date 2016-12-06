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

class Indies_Recurringandrentalpayments_Model_Customertypes
{
    public function toOptionArray()
    {
        return array(
            array(
                'value' => '1',
                'label' => Mage::helper('recurringandrentalpayments')->__('All Customers Including Guest')
            ),
            array(
                'value' => '2',
                'label' => Mage::helper('recurringandrentalpayments')->__('Registered Customers Only')
            ),
			array(
                'value' => '3',
                'label' => Mage::helper('recurringandrentalpayments')->__('Specific Customer Groups Only')
            )
        );
    }
}