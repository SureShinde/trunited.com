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

class Indies_Recurringandrentalpayments_Model_Validation extends Mage_Core_Model_Config_Data {
	
	public static $discountamount;
	public function save()
    {
		$data = $this->_getData('fieldset_data');
		if($data['apply_discount_settings'] == 1)
		{
			if($data['discount_amount'] != '')
			{
				if($data['discount_cal_type'] !=1) {
					self::$discountamount = $data['discount_amount'];
					if (self::$discountamount < 0 || self::$discountamount > 99 ) {
						Mage::throwException('Discount amount value must be between 0 to 99.');
					}
				}
			}
			else
			{
				Mage::throwException('Please Enter Discount Amount.');
			}
		}
    return parent::save();
	}
}
