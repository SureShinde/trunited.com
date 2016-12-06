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

class Indies_Recurringandrentalpayments_Model_Sales_Order_Total_Creditmemo_Recurringdiscount extends Mage_Sales_Model_Order_Creditmemo_Total_Abstract
{
	public function collect(Mage_Sales_Model_Order_Creditmemo $creditmemo)
	{
	   $order = $creditmemo->getOrder();

   	   $discountAmountLeft = $order->getRecurringDiscountAmount() - $order->getRecurringDiscountAmountRefunded();
	   $baseDiscountAmountLeft = $order->getBaseRecurringDiscountAmount() - $order->getBaseRecurringDiscountAmountRefunded();
	   
	   $creditmemo->setGrandTotal($creditmemo->getGrandTotal() + $order->getRecurringDiscountAmount());
       $creditmemo->setBaseGrandTotal($creditmemo->getBaseGrandTotal() + $order->getBaseRecurringDiscountAmount());
	   
	   $creditmemo->setRecurringDiscountAmount($discountAmountLeft);
	   $creditmemo->setBaseRecurringDiscountAmount($baseDiscountAmountLeft);
	   
	  return $this;
	}
}
