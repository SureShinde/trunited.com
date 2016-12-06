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

class Indies_Recurringandrentalpayments_Model_Payment_Method_Core_Purchaseorder extends Mage_Payment_Model_Method_Purchaseorder
{
    protected static $poIsset = false;

    /**
     * Validate payment method information object
     *
     * @param   Mage_Payment_Model_Info $info
     * @return  Mage_Payment_Model_Abstract
     */
    public function validate()
    {
        if (Indies_Recurringandrentalpayments_Model_Subscription::isIterating()) {
            if (!self::$poIsset) {
                $info = $this->getInfoInstance();
                if ($info) {
                    $poNumber = $info->getData('po_number');
                    if ($poNumber) {
                        $info->setPoNumber($poNumber . '-' . Mage::getModel('core/date')->date('dmy'));
                        self::$poIsset = true;
                    }
                }
            }

            return $this;
        } else {
            return parent::validate();
        }
    }
}