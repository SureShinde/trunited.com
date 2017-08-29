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

class Indies_Recurringandrentalpayments_Model_Sales_Quote_Address_Total_Recurringdiscount extends Mage_Sales_Model_Quote_Address_Total_Abstract{
	
	protected $_code = 'recurring_discount';            


	public function collect(Mage_Sales_Model_Quote_Address $address)
	{
		parent::collect($address);
		$items = $this->_getAddressItems($address);

		if (!count($items)) {
			return $this;
		}

		$recurring_discount = 0 ;
		if(Mage::helper('recurringandrentalpayments')->canRun())
		{
		   if(Mage::helper('recurringandrentalpayments')->isEnabled())
		   {
				if(Mage::helper('recurringandrentalpayments')->isApplyDiscount())   // enable/disable
				{
					$recurring_discount = Mage::getModel('recurringandrentalpayments/discount')->getDiscount();
				}
			}
		}
				

		if($recurring_discount != 0 && $address->getSubtotal()>$recurring_discount)
		{
			$baseRecurringDiscountAmount = Mage::helper('recurringandrentalpayments')->convertDiscountAmount($recurring_discount);
/*			$totals = array_sum($address->getAllTotalAmounts());
			$baseTotals = array_sum($address->getAllBaseTotalAmounts());*/
			$grandTotal = $address->getGrandTotal();
			$baseGrandTotal = $address->getBaseGrandTotal();
			
		
			$address->setRecurringDiscountAmount(-$recurring_discount);
			$address->setBaseRecurringDiscountAmount(-$baseRecurringDiscountAmount);
			$address->setRecurringDiscountDescription('Recurring Discount');
			
			$address->setGrandTotal($grandTotal - $recurring_discount);
			$address->setBaseGrandTotal($baseGrandTotal - $baseRecurringDiscountAmount);
			
		}
		else
		{
			$address->setRecurringDiscountAmount(0);
			$address->setBaseRecurringDiscountAmount(0);	
		}
	    return $this;
		
	}


	public function fetch(Mage_Sales_Model_Quote_Address $address)
	{
		 $recurring_discount_amount = $address->getRecurringDiscountAmount();
		 if ($recurring_discount_amount != 0) {    
              $address->addTotal(array(
						'code'=>$this->getCode(),
						'title'=>$address->getRecurringDiscountDescription(),
						'value'=> $recurring_discount_amount,
				), 'grand_total');
            }
		/*	$address->addTotal(array(
                'code'  => $this->getCode(),
                'title' => $title,
                'value' => $amount
            ));*/
		return $address;
	}
}