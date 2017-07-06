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

class Indies_Recurringandrentalpayments_Model_Sales_Order_Pdf_Items_Invoice extends Mage_Sales_Model_Order_Pdf_Items_Invoice_Default
{
    public function getItemOptions()
    {
        $result = array();
		$primary_order_item_id = $this->getItem()->getId();
		$subscriberid = Mage::getModel('recurringandrentalpayments/subscription_item')
						->load($primary_order_item_id,'primary_order_item_id')->getSubscriptionId();
		$subscription = Mage::getModel('recurringandrentalpayments/subscription')->load($subscriberid);
		$type = $subscription->getTermType();
		$starttime = new Zend_Date($subscription->getDateStart() , 'Y-MM-dd');
		if ($type && $starttime) {
            $startDateLabel = $this->getItem()->getIsVirtual() ? $this->__("Subscription start:")
                    : $this->__("First delivery:");
            if ($type && $starttime) {
                $periodTypeId = $type;
                $periodStartDate = $starttime ;
				 $plans = Mage::getModel('recurringandrentalpayments/terms')->load($periodTypeId);
                if ($periodTypeId && $periodStartDate) {
                    $result[] = array(
                        'label' => $this->__('Subscription type:'),
                        'value' => $plans->getLabel()
                    );

                    $result[] = array(
                        'label' => $startDateLabel,
                        'value' => Mage::helper('core')->formatDate($periodStartDate, Mage_Core_Model_Locale::FORMAT_TYPE_LONG)
                    );
                }
            }
            $result = array_merge($result, parent::getOrderOptions());
        }
        return $result;
		
    }

}
