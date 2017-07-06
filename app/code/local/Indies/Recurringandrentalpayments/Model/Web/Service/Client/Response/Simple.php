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

class Indies_Recurringandrentalpayments_Model_Web_Service_Client_Response_Simple extends Varien_Object
{

    protected $_fields;

    public function setOnceFields(array $fields)
    {
        $this->_fields = $fields;
    }

    public function reset()
    {
        $this->setData(array());
        return $this;
    }

    public function setData($key, $value = null)
    {
        if ($key instanceof StdClass) {
            foreach ($key as $prop => $value) {
                parent::setData($prop, $value);
            }
            return $this;
        } else {
            return parent::setData($key, $value);
        }
    }
}
