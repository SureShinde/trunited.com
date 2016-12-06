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

class Indies_Recurringandrentalpayments_Model_Product_Type_Simple_Subscription extends Mage_Catalog_Model_Product_Type_Abstract
{

    protected $_canConfigure = true;

    /**
     * Prepares product for cart according to buyRequest.
     *
     * @param Varien_Object $buyRequest
     * @param object        $product [optional]
     * @return
     */
    public function prepareForCart(Varien_Object $buyRequest, $product = null, $old = false)
    {
        Mage::getModel('recurringandrentalpayments/product_type_default')->checkPeriod($product, $buyRequest);

        if (!Mage::getModel('recurringandrentalpayments/product_type_default')->validateSubscription($product, $buyRequest)) {
		    if (Mage::helper('recurringandrentalpayments')->checkVersion('1.5.0.0'))
                Mage::throwException(Mage::helper('catalog')->__('Please specify the product\'s required option(s).'));
            else
                return Mage::helper('catalog')->__('Please specify the product(s) quantity');
        }
        else {
            /*
                * For creating order from admin
                * If product is added to cart from admin, we doesn't add sart custom options to it.
                */
            $Period = Mage::getModel('recurringandrentalpayments/terms');

            if ($buyRequest->getIndiesRecurringandrentalpaymentsSubscriptionType()) {
            if (count(explode(",",$buyRequest->getIndiesRecurringandrentalpaymentsSubscriptionType())) === 1) {
                    $date = Mage::getModel('recurringandrentalpayments/terms')->load($buyRequest->getIndiesRecurringandrentalpaymentsSubscriptionType())->getNearestAvailableDay();
                    $product->setIndiesRecurringandrentalpaymentsSubscriptionType($buyRequest->getIndiesRecurringandrentalpaymentsSubscriptionType());
                    $product->setIndiesRnrSubscriptionStart($date->toString(), Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT));
                }
            }

            /* We should add custom options that doesnt exist */
            if ($buyRequest->getIndiesRecurringandrentalpaymentsSubscriptionType()) {
                if ($Period->load($buyRequest->getIndiesRecurringandrentalpaymentsSubscriptionType())
				->getId()) {
                    $product->addCustomOption('indies_recurringandrentalpayments_subscription_type', $Period->getId());
                }
            }
            else
            {
                if ($product->getIndiesRecurringandrentalpaymentsSubscriptionType()) {
                    $buyRequest->setIndiesRecurringandrentalpaymentsSubscriptionType($product->getIndiesRecurringandrentalpaymentsSubscriptionType());
                    $product->addCustomOption('indies_recurringandrentalpayments_subscription_type', $product->getIndiesRecurringandrentalpaymentsSubscriptionType());
                    $Period->setId($product->getIndiesRnrSubscriptionStart());
                }
            }

            if ($this->requiresSubscriptionOptions($product) && !$Period->getId()) {
                return (Mage::helper('recurringandrentalpayments')->__("Selected product requires subscription options"));
            }

            $options = $buyRequest->getOptions();
            if (isset($options['indies_recurringandrentalpayments_subscription_start']) && is_array($options['indies_recurringandrentalpayments_subscription_start'])) {
                $subscriptionStart = $options['indies_recurringandrentalpayments_subscription_start'];
                $date = new Zend_Date();
                $date
                        ->setMinute(0)
                        ->setHour(0)
                        ->setSecond(0)
                        ->setDay($subscriptionStart['day'])
                        ->setMonth($subscriptionStart['month'])
                        ->setYear($subscriptionStart['year']);
                $buyRequest->setIndiesRnrSubscriptionStart($date->toString(Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT)));
            }

            if ($buyRequest->getIndiesRnrSubscriptionStart() && $Period->getId()) {
                $date = new Zend_Date($buyRequest->getIndiesRnrSubscriptionStart(), Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT));
                // Check date

                // Never check if start date

                $performDateCompare = !Indies_Recurringandrentalpayments_Model_Recurringandrentalpaymentscron::$isCronSession;

                $today = new Zend_Date;
                if (!$this->isVirtual($product)) {
                    $today->addDayOfYear($Period->getPaymentOffset());
                }

                if ($performDateCompare
                    && ($date->compare($today, Zend_Date::DATE_SHORT) < 0
                        || !$Period->isAllowedDate($date, $product))
                ) {
                    throw new Mage_Core_Exception(Mage::helper('recurringandrentalpayments')->__("Selected date is not valid for specified period"));
                }
            }
            else
            {
                $date = Mage::app()->getLocale()->date();
            }
            $product->addCustomOption('indies_recurringandrentalpayments_subscription_start', $date->toString('Y-MM-dd'));

            if (!$old)
                return parent::prepareForCart($buyRequest, $product);

        }
    }

    public function prepareForCartAdvanced(Varien_Object $buyRequest, $product = null, $processMode = null)
    {
        Mage::getModel('recurringandrentalpayments/product_type_default')->checkPeriod($product, $buyRequest);
        $this->prepareForCart($buyRequest, $product, true);
        return parent::prepareForCartAdvanced($buyRequest, $product);
    }

    /**
     * @param $product
     * @return bool
     */
    public function hasRequiredOptions($product = null)
	{
		return true;
	}

    /**
     * Returns true if product has subscriptions options
     * @return bool
     */
     public function hasSubscriptionOptions()
    {
         $plan = Mage::getModel('recurringandrentalpayments/plans')->load($this->getProduct()->getId(),'product_id');
		if(!$plan->getId()){
            return false;
        }
        return true;
    }

    /**
     * Returns true if product requires subscription options
     * @return bool
     */
     public function requiresSubscriptionOptions($product = null)
    {
        if (is_null($product)) $product = $this->getProduct();
        $plan = Mage::getModel('recurringandrentalpayments/plans')->load($product->getId(),'product_id');
		if (!$plan->getPlanStatus()) return false;
        
        if (!$plan->getId()) {
            return false;
        }
        return true;
    }
    /**
     * Returns default period id. If none, returns -1
     * @return int
     */
    public function getDefaultSubscriptionPeriodId()
    {
        $plan = Mage::getModel('recurringandrentalpayments/plans')->load($product->getId(),'product_id');
        return isset($plan->geId()) ? $plan->geId() : Indies_Recurringandrentalpayments_Model_Terms::PERIOD_TYPE_NONE;
    }

    /**
     * Returns if product is "virtual", e.g. requires no shipping
     *
     * @param object $product [optional]
     * @return bool
     */
    public function isVirtual($product = null)
    {
        if (is_null($product)) {
            $product = $this->getProduct();
        }
        $product->load($product->getId());
        return false;
    }

    public function processBuyRequest($product, $buyRequest)
    {
        $toReturn = array();
        if ($buyRequest->getData('indies_recurringandrentalpayments_subscription_start')) $toReturn['indies_recurringandrentalpayments_subscription_start'] = $buyRequest->getData('indies_recurringandrentalpayments_subscription_start');
        if ($buyRequest->getData('indies_recurringandrentalpayments_subscription_type')) $toReturn['indies_recurringandrentalpayments_subscription_type'] = $buyRequest->getData('indies_recurringandrentalpayments_subscription_type');
        return $toReturn;
    }

    public function beforeSave($product = null)
    {
        parent::beforeSave($product);
         $plan = Mage::getModel('recurringandrentalpayments/plans')->load($product->getId(),'product_id');
		if ($plan->getPlanStatus() && $this->getProduct($product)->getSubscriptionPrice() == '')
            $this->getProduct($product)->setSubscriptionPrice($product->getData('price'));
    }

}