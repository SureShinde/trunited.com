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

class Indies_Recurringandrentalpayments_Model_Sales_Order_Total_Invoice_Recurringdiscount extends Mage_Sales_Model_Order_Invoice_Total_Abstract
{	
	public function collect(Mage_Sales_Model_Order_Invoice $invoice)
	{
	   $order = $invoice->getOrder();

   	   $discountAmountLeft = $order->getRecurringDiscountAmount() - $order->getRecurringDiscountAmountInvoiced();
	   $baseDiscountAmountLeft = $order->getBaseRecurringDiscountAmount() - $order->getBaseRecurringDiscountAmountInvoiced();
	   
	   $invoice->setGrandTotal($invoice->getGrandTotal() + $order->getRecurringDiscountAmount());
       $invoice->setBaseGrandTotal($invoice->getBaseGrandTotal() + $order->getBaseRecurringDiscountAmount());
	   
	   $invoice->setRecurringDiscountAmount($discountAmountLeft);
	   $invoice->setBaseRecurringDiscountAmount($baseDiscountAmountLeft);
	  return $this;
	}
}
