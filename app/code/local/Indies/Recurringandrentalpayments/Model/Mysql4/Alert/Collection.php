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

class Indies_Recurringandrentalpayments_Model_Mysql4_Alert_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{

    public function _construct()
    {
        parent::_construct();
        $this->_init('recurringandrentalpayments/alert');
    }

    /**
     * Return only enabled entries
     * return Indies_Recurringandrentalpayments_Model_Mysql4_Alert_Collection;
     */
    public function addEnabledFilter()
    {
        $this->getSelect()->where('status=?', Indies_Recurringandrentalpayments_Model_Alert::STATUS_ENABLED);
        return $this;
    }

}