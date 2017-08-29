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

class Indies_Recurringandrentalpayments_Model_Mysql4_Plans_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('recurringandrentalpayments/plans');
    }
	public function hasNoSubscriptionOption()
    {
        if ($Product = $this->getProduct()) {
            $opts = Mage::getModel('recurringandrentalpayments/plans')->load($Product->getId(),'product_id');
            if ($opts->getIsNormal()==1) {
                return true;
            }
        }
        return false;
    }
	public function getProduct()
    {
       return Mage::registry('product');
    }

}