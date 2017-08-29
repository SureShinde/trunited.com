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

class Indies_Recurringandrentalpayments_Block_Adminhtml_Sales_Order_View_Items_Column_Downloadable_Name extends Mage_Downloadable_Block_Adminhtml_Sales_Items_Column_Downloadable_Name
{
    public function getOrderOptions()
    {
        $result = array();
        if ($options = $this->getItem()->getProductOptions()) {
            if (isset($options['info_buyRequest'])) {
                $periodTypeId = @$options['info_buyRequest']['indies_recurringandrentalpayments_subscription_type'];
                $periodStartDate = @$options['info_buyRequest']['indies_recurringandrentalpayments_subscription_start'];
                if ($periodTypeId && $periodStartDate) {
                    $result[] = array(
                        'label' => $this->__('Subscription type:'),
                        'value' => Mage::getModel('recurringandrentalpayments/terms')->load($periodTypeId)->getLabel()
                    );

                    $result[] = array(
                        'label' => $this->__("Subscription start:"),
						'value' => Mage::helper('recurringandrentalpayments')->__(date("M d, Y" ,strtotime($periodStartDate)))
                    );
                }
            }
            $result = array_merge($result, parent::getOrderOptions());
        }
        return $result;
    }
}
