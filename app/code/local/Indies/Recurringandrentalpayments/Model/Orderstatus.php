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

class Indies_Recurringandrentalpayments_Model_Orderstatus
{
    // set null to enable all possible
    protected $_stateStatuses = array(
        Mage_Sales_Model_Order::STATE_NEW,
        Mage_Sales_Model_Order::STATE_PROCESSING,
        Mage_Sales_Model_Order::STATE_COMPLETE,
    );

    public function toOptionArray()
    {
		 return array(
            array(
                'value' => 'pending',
                'label' => Mage::helper('recurringandrentalpayments')->__('Order Placement')
            ),
            array(
                'value' => 'processing',
                'label' => Mage::helper('recurringandrentalpayments')->__('Invoice Generation')
            ),
			array(
                'value' => 'complete',
                'label' => Mage::helper('recurringandrentalpayments')->__('Order Completion')
            ),
			array(
                'value' => 'manuallybyadmin',
                'label' => Mage::helper('recurringandrentalpayments')->__('Manually by Admin')
            )
        );
   }
}
