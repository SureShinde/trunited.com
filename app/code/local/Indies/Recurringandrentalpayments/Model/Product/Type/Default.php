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

class Indies_Recurringandrentalpayments_Model_Product_Type_Default
{
    /*
     *  checking sended period value, if product doesn't include this period, then  function throw exception with message
    */

    public function checkPeriod(Indies_Recurringandrentalpayments_Model_Catalog_Product $product, Varien_Object $buyRequest)
    {

        if ($buyRequest->getIndiesRecurringandrentalpaymentsSubscriptionType() != '') {
            throw new Exception('Wrong Terms passed!');
        }
    }

    public function validateSubscription(Indies_Recurringandrentalpayments_Model_Catalog_Product $product, Varien_Object $buyRequest)
    {
        if ($options = $buyRequest->getOptions()) {
            if (!isset($options['indies_recurringandrentalpayments_subscription_start'])) {
                if(!$buyRequest->getIndiesRnrSubscriptionStart())
                    return false;
                else
                    return true;
            } else {
                return true;
            }
        }
        if (!$buyRequest->getIndiesRnrSubscriptionStart())
            return false;
        else
            return true;
    }
}