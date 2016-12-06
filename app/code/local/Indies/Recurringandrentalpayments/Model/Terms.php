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

class Indies_Recurringandrentalpayments_Model_Terms extends Mage_Core_Model_Abstract
{
    const TERMSPER_DAY  = 'day';
	const TERMSPER_WEEK  = 'week';
	const TERMSPER_MONTH  = 'month';
	const TERMSPER_YEAR  = 'year';
	const PERIOD_TYPE_NONE = -1;
	const PRICE_CALC_TYPE_FIXED = 0;
	const PRICE_CALC_TYPE_PER = 1;
	public function _construct()
    {
        parent::_construct();
        $this->_init('recurringandrentalpayments/terms');
    }
	public function validate()
    {
		if (((int)$this->getRepeateach()) < 1) {
            throw new Indies_Recurringandrentalpayments_Exception("Terms must be more 0");
        }
        return $this;
    }
	public function isInfinite()
    {
        if($this->getNoofterms()==0)
		{	
			return true;
		}
		else
		{
			return  false;	
		}
    }

}