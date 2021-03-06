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

class Indies_Recurringandrentalpayments_Model_Order_Section_Shipping extends Indies_Recurringandrentalpayments_Model_Order_Section
{

    /**
     * Saves billing section to order
     * @param Mage_Sales_Model_Order $Order
     * @return
     */
    public function preset(Indies_Recurringandrentalpayments_Model_Subscription $subscription, $data)
    {
        $order = $subscription->getOrder();
        $shippingAddress = $order->getShippingAddress();
        foreach ($data as $key => $value) {
            $shippingAddress->setData($key, $value);
        }
        $shippingAddress->implodeStreetAddress();
        $shippingAddress->save();

        return $order;
    }
}