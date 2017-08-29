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

class Indies_Recurringandrentalpayments_Block_Customer_Subscription_Edit extends Mage_Core_Block_Template
{

    /**
     * Returns current order
     * @return Mage_Sales_Model_Order
     */
    public function getQuote()
    {
        if (!$this->getData('quote')) {
            $this->setQuote($this->getSubscription()->getQuote());
        }
        return $this->getData('quote');
    }

    /**
     * Returns according section block
     * @param string $code
     * @return  Indies_Recurringandrentalpayments_Block_Customer_Subscription_Edit
     */
    public function setSection($code)
    {


        if (in_array($code, array('billing', 'shipping'))) {

            $this->setChild(
                'edit_section',
                $this->getLayout()->createBlock('recurringandrentalpayments/customer_subscription_edit_' . $code)
                        ->setQuote($this->getQuote())
                        ->setSubscription($this->getSubscription())
            );


            return $this;
        }
        return $this;
    }

    public function getTitle()
    {
        return $this->getChild('edit_section')->getTitle();
    }

}