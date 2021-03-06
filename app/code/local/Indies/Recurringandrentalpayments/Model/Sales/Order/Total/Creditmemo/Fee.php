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

class Indies_Fee_Model_Sales_Order_Total_Creditmemo_Fee extends Mage_Sales_Model_Order_Creditmemo_Total_Abstract
{
	public function collect(Mage_Sales_Model_Order_Creditmemo $creditmemo)
	{
		$calculationHelper = Mage::helper('partialpayment/calculation');
		$order = $creditmemo->getOrder();
		$feeAmountLeft = $order->getFeeAmountInvoiced() - $order->getFeeAmountRefunded();
		$basefeeAmountLeft = $order->getBaseFeeAmountInvoiced() - $order->getBaseFeeAmountRefunded();
		if ($basefeeAmountLeft > 0) {
			//$creditmemo->setGrandTotal($creditmemo->getGrandTotal() + $feeAmountLeft);
			//$creditmemo->setBaseGrandTotal($creditmemo->getBaseGrandTotal() + $basefeeAmountLeft);
			$creditmemo->setFeeAmount($feeAmountLeft);
			//$creditmemo->setBaseFeeAmount($calculationHelper->convertCurrencyAmount($basefeeAmountLeft));
		}
		return $this;
	}
}
