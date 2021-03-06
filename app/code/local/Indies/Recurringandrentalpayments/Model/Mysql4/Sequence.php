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

class Indies_Recurringandrentalpayments_Model_Mysql4_Sequence extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init('recurringandrentalpayments/sequence', 'id');
    }

    /**
     * Delete complete sequence for selected subscription_id
     * @param int $id
     * @return Indies_Recurringandrentalpayments_Model_Mysql4_Sequence
     */
    public function deleteBySubscriptionId($id)
    {
        $this->_getWriteAdapter()->delete($this->getMainTable(), 'subscription_id=' . $id . ' AND status=\'' . Indies_Recurringandrentalpayments_Model_Sequence::STATUS_PENDING . "'");
        return $this;
    }
}
