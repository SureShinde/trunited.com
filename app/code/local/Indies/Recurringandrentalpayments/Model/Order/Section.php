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

class Indies_Recurringandrentalpayments_Model_Order_Section extends Varien_Object
{
    /**
     * Saves section. That means that new order must be created and assigned to subscription
     * @return
     */
    public function save()
    {
        if (!$this->getSubscription()) {
            throw new Indies_Recurringandrentalpayments_Exception("No subscription assigned for section editing");
        }
        if (!($save_data = $this->getDataToSave())) {
            throw new Indies_Recurringandrentalpayments_Exception("Can't save empty subscription section data");
        }
        $this->getSectionInstance()->preset($this->getSubscription(), $save_data)->save();
    }

    /**
     * Returns current section controller
     * @return Indies_Recurringandrentalpayments_Model_Order_Section
     */
    public function getSectionInstance()
    {
        if (!$this->getData('section_instance')) {
            $this->setSectionInstance(Mage::getModel('recurringandrentalpayments/order_section_' . $this->getType()));
            if (!$this->getData('section_instance')) {
                throw new Indies_Recurringandrentalpayments_Exception("No instance for section type '{$this->getType()}' found");
            }
        }
        return $this->getData('section_instance');
    }

    /**
     * Returns current set customer
     * @return Mage_Customer_Model_Customer
     */
    public function getCustomer()
    {
        if (!$this->getData('customer')) {
            $this->setCustomer(Mage::getSingleton('customer/session')->getCustomer());
        }
        return $this->getData('customer');
    }
}