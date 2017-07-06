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

class Indies_Recurringandrentalpayments_Model_Source_Subscription_Status extends Mage_Eav_Model_Entity_Attribute_Source_Abstract
{
    /**
     * Retrive all attribute options
     *
     * @return array
     */

    public function getAllOptions()
    {
        return $this->toOptionArray();
    }

    public function toOptionArray()
    {
        return array(
            array('value' => Indies_Recurringandrentalpayments_Model_Subscription::STATUS_ENABLED, 'label' => Mage::helper('recurringandrentalpayments')->__('Active')),
            array('value' => Indies_Recurringandrentalpayments_Model_Subscription::STATUS_SUSPENDED, 'label' => Mage::helper('recurringandrentalpayments')->__('Suspended')),
            array('value' => Indies_Recurringandrentalpayments_Model_Subscription::STATUS_SUSPENDED_BY_CUSTOMER, 'label' => Mage::helper('recurringandrentalpayments')->__('Suspended by Customer')),
            array('value' => Indies_Recurringandrentalpayments_Model_Subscription::STATUS_EXPIRED, 'label' => Mage::helper('recurringandrentalpayments')->__('Expired')),
            array('value' => Indies_Recurringandrentalpayments_Model_Subscription::STATUS_CANCELED, 'label' => Mage::helper('recurringandrentalpayments')->__('Cancelled'))
        );
    }

    /**
     * Returns label for value
     * @param string $value
     * @return string
     */
    public function getLabel($value)
    {
        $options = $this->toOptionArray();
        foreach ($options as $v) {
            if ($v['value'] == $value) {
                return $v['label'];
            }
        }
        return '';
    }

    /**
     * Returns array ready for use by grid
     * @return array
     */
    public function getGridOptions()
    {
        $items = $this->getAllOptions();
        $out = array();
        foreach ($items as $item) {
            $out[$item['value']] = $item['label'];
        }
        return $out;
    }

}
