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

class Indies_Recurringandrentalpayments_Model_Subscription_Flat extends Mage_Core_Model_Abstract
{

    const DB_DELIMITER = "\r\n";
    const DB_EXPLODE_RE = "/[\r\n]+/";
    const DB_DATE_FORMAT = 'yyyy-MM-dd'; // DON'T use Y(uppercase here)

    protected function _construct()
    {
        $this->_init('recurringandrentalpayments/subscription_flat');
    }

    /**
     * Converts subscription products to text
     * @param Indies_Recurringandrentalpayments_Model_Subscription $Subscription
     * @return
     */
    protected function _convertProductsText(Indies_Recurringandrentalpayments_Model_Subscription $Subscription)
    {
        $out = array();
        foreach ($Subscription->getItems() as $Item) {
            $out[] = $Item->getOrderItem()->getName() . " (" . intval($Item->getOrderItem()->getQtyOrdered()) . ")";
        }
        return implode(self::DB_DELIMITER, $out);
    }

    /**
     * Converts subscription products to sku text
     * @param Indies_Recurringandrentalpayments_Model_Subscription $Subscription
     * @return
     */
    protected function _convertProductsSku(Indies_Recurringandrentalpayments_Model_Subscription $Subscription)
    {
        $out = array();
        foreach ($Subscription->getItems() as $Item) {
            $out[] = $Item->getOrderItem()->getSku();
        }
        return implode(self::DB_DELIMITER, $out);
    }

    /**
     * Presets flat data from subscription
     * @param Indies_Recurringandrentalpayments_Model_Subscription $Subscription
     * @return Indies_Recurringandrentalpayments_Model_Subscription_Flat
     */
    public function setSubscription(Indies_Recurringandrentalpayments_Model_Subscription $Subscription)
    {
		
		Zend_Date::setOptions(array('extend_month' => true)); // Fix Zend_Date::addMonth unexpected result
 		$virtual = 0;
        if (!$Subscription->isInfinite()) {
            $expireDate = $Subscription->getDateExpire()->toString(Indies_Recurringandrentalpayments_Model_Subscription::DB_DATE_FORMAT);

			$check =(int) $Subscription->getTerm()->getPaymentBeforeDays() - 1;  
			$expireDate = date('Y-m-d', strtotime("+$check day", strtotime($expireDate)));
	    } else {
            $expireDate = NULL;
		}
        if ($Subscription->getIsNew()) {
            $lastOrderAmount = 0;
            $quote = clone $Subscription->getQuote();
			
            foreach ($quote->getItemsCollection() as $Item) {
					$buyInfo = $Item ->getBuyRequest();
					$period_type = $buyInfo->getIndiesRecurringandrentalpaymentsSubscriptionType();
					$Options =Mage::getModel('recurringandrentalpayments/terms')->load($period_type);
                if ($Options) {
                    $quote->removeItem($Item->getId());
                }
                $quote->getShippingAddress()->setCollectShippingRates(true)->collectTotals();
                $quote->collectTotals();
				$virtual = $Item->getIsVirtual();
            }
            $lastOrderAmount = $Subscription->getQuote()->getGrandTotal();
			//$virtual = $quote->getIsVirtual();
			
            unset($quote);
        } else {
            $lastOrderAmount = $Subscription->getLastOrder()->getGrandTotal();
            $virtual = $Subscription->getLastOrder()->getIsVirtual();
			 }
       // if ($Subscription->isActive()) {
			$paymentOffset = $Subscription->getTerm()->getPaymentBeforeDays();// Mage::getModel('recurringandrentalpayments/plans')->load($Subscription->getTerm()->getPlanId())->getPaymentBeforeDays();
            // Get next payment date
		    if (!$Subscription->getLastPaidDate() && $Subscription->getIsNew()) {     // Come here on placing an order

					$nextPaymentDate = $Subscription->getLastOrder()->getCreatedAtStoreDate();
					$nextPaymentDate = $Subscription->getNextSubscriptionEventDate($Subscription->getDateStart());
	
            } else {
				$paidDate = new Zend_Date($Subscription->getLastPaidDate(), self::DB_DATE_FORMAT);
			    $nextPaymentDate = $Subscription->getNextSubscriptionEventDate($paidDate);
				
            }
			
            if ($paymentOffset) {       // $paymentOffset used for Payment before days
                if (!$Subscription->getLastPaidDate()) {
                    // No payments made yet
                    $lastOrderDate = clone $Subscription->getDateStart();
                    $lastOrderDate->addDayOfYear(0 - floatval($paymentOffset));

                } else {
                    $lastOrderDate = $Subscription->getLastOrder()->getCreatedAtStoreDate();
                }

                $nextPaymentDate->addDayOfYear(0 - floatval($paymentOffset));
            }
           

        $nextPaymentDate = $nextPaymentDate->toString(Indies_Recurringandrentalpayments_Model_Subscription::DB_DATE_FORMAT);
		
		if($Subscription->getParentOrderId() == '')
		   $order_id = $Subscription->getOrder()->getIncrementId();
		else
		   $order_id = $Subscription->getParentOrderId();
		
		
		$magento_date = (Mage::getModel('core/date')->date('Y-m-d'));
		if($magento_date == $expireDate)
		{
			$nextPaymentDate = $expireDate ;
		}
		
		$m_date = new Zend_Date($magento_date);
		$next_p_date = $this->getFlatNextPaymentDate(); 
		if(isset($next_p_date) && $next_p_date == $m_date->toString(self::DB_DATE_FORMAT))
		{
		   $nextPaymentDate = $magento_date;
		}
        $this->setSubscriptionId($Subscription->getId())
                ->setCustomerName($Subscription->getCustomer()->getName())
                ->setCustomerEmail($Subscription->getCustomer()->getEmail())
				->setParentOrderId($order_id)
                ->setFlatLastOrderStatus($Subscription->getLastOrder()->getStatus())
                ->setFlatLastOrderAmount($lastOrderAmount)
                ->setFlatLastOrderCurrencyCode($Subscription->getLastOrder()->getOrderCurrencyCode())
                ->setFlatDateExpire($expireDate)
                ->setHasShipping(1 - $virtual)
                ->setFlatNextPaymentDate($nextPaymentDate)
                ->setProductsText($this->_convertProductsText($Subscription))
                ->setProductsSku($this->_convertProductsSku($Subscription));	

        $Subscription
                ->setCustomerName($Subscription->getCustomer()->getName())
                ->setCustomerEmail($Subscription->getCustomer()->getEmail())
				->setParentOrderId($order_id)
                ->setFlatLastOrderStatus($Subscription->getLastOrder()->getStatus())
                ->setFlatLastOrderAmount($lastOrderAmount)
                ->setFlatLastOrderCurrencyCode($Subscription->getLastOrder()->getOrderCurrencyCode())
                ->setFlatDateExpire($expireDate)
                ->setHasShipping(1 - $virtual)
                ->setFlatNextPaymentDate($nextPaymentDate)
                ->setProductsText($this->_convertProductsText($Subscription))
                ->setProductsSku($this->_convertProductsSku($Subscription));
        return $this;
    }


}
