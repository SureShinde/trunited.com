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
	
class Indies_Recurringandrentalpayments_Model_Subscriptionstatus
{
    public function toOptionArray()
    {
        return array(
            array(
                'value' => 'active',
                'label' => Mage::helper('recurringandrentalpayments')->__('Active')
            ),
            array(
                'value' => 'suspended',
                'label' => Mage::helper('recurringandrentalpayments')->__('Suspended')
            ),
			array(
                'value' => 'cancelled',
                'label' => Mage::helper('recurringandrentalpayments')->__('Cancelled')
            ),
			array(
                'value' => 'expired',
                'label' => Mage::helper('recurringandrentalpayments')->__('Expired')
            )
			
        );
    }
}