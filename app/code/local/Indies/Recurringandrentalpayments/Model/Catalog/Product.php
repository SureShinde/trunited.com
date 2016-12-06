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

class Indies_Recurringandrentalpayments_Model_Catalog_Product extends Mage_Catalog_Model_Product
{
    /**
     * Check is product configurable
     *
     * @return bool
     */
    public function isConfigurable()
    {
        return ($this->getTypeId() == Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE ||
                $this->getTypeId() == 'configurable');
    }

    public function isGrouped()
    {
        return ($this->getTypeId() == Mage_Catalog_Model_Product_Type::TYPE_GROUPED ||
                $this->getTypeId() == Indies_Recurringandrentalpayments_Model_Product_Type_Grouped_Subscription::TYPE_CODE);
    }
}
