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

class Indies_Recurringandrentalpayments_Model_Product_Type_Configurable_Subscription extends Mage_Catalog_Model_Product_Type_Configurable
{
    protected $_canConfigure = true;

    const PRODUCT_TYPE_CONFIGURABLE = 'configurable';

    /**
     * Prepares product for cart according to buyRequest.
     *
     * @param Varien_Object $buyRequest
     * @param object        $product [optional]
     * @return
     */
    public function prepareForCart(Varien_Object $buyRequest, $product = null)
    {
		Mage::getModel('recurringandrentalpayments/product_type_default')->checkPeriod($product, $buyRequest);
		$Period = Mage::getModel('recurringandrentalpayments/terms');

        /* We should add custom options that doesnt exist */
        if ($buyRequest->getIndiesRecurringandrentalpaymentsSubscriptionType()) {
            if ($Period->load($buyRequest->getIndiesRecurringandrentalpaymentsSubscriptionType())->getId()) {
                $product->addCustomOption('indies_recurringandrentalpayments_subscription_type', $Period->getId());
            }
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

        if ($attributes = $buyRequest->getSuperAttribute()) {
            $result = Mage_Catalog_Model_Product_Type_Abstract::prepareForCart($buyRequest, $product);
            if (is_array($result)) {
                $product = $this->getProduct($product);
                /**
                 * $attributes = array($attributeId=>$attributeValue)
                 */
                if ($subProduct = $this->getProductByAttributes($attributes, $product)) {
                    $product->addCustomOption('attributes', serialize($attributes));
                    $product->addCustomOption('product_qty_' . $subProduct->getId(), 1, $subProduct);
                    $product->addCustomOption('simple_product', $subProduct->getId(), $subProduct);

                    $_result = $subProduct->getTypeInstance(true)->prepareForCart($buyRequest, $subProduct);
                    if (is_string($_result) && !is_array($_result)) {
                        return $_result;
                    }

                    if (!isset($_result[0])) {
                        return Mage::helper('checkout')->__('Can not add item to shopping cart');
                    }

                    /**
                     * Adding parent product custom options to child product
                     * to be sure that it will be unique as its parent
                     */
                    if ($optionIds = $product->getCustomOption('option_ids')) {
                        $optionIds = explode(',', $optionIds->getValue());
                        foreach ($optionIds as $optionId) {
                            if ($option = $product->getCustomOption('option_' . $optionId)) {
                                $_result[0]->addCustomOption('option_' . $optionId, $option->getValue());
                            }
                        }
                    }

                    if ($buyRequest->getIndiesRecurringandrentalpaymentsSubscriptionType()) {
                        if ($Period->getId()) {
                            $_result[0]->addCustomOption('indies_recurringandrentalpayments_subscription_start', $date->toString('Y-MM-dd'));
                            $_result[0]->addCustomOption('indies_recurringandrentalpayments_subscription_type', $Period->getId());
                        }
                    }

                    $_result[0]->setParentProductId($product->getId())
                    // add custom option to simple product for protection of process when we add simple product separately
                            ->addCustomOption('parent_product_id', $product->getId())
                            ->setCartQty(1);

                    $result[] = $_result[0];

                    return $result;
                }
            }
        }
        return $this->getSpecifyOptionMessage();
    }

    public function prepareForCartAdvanced(Varien_Object $buyRequest, $product = null, $processMode = null)
    {
        Mage::getModel('recurringandrentalpayments/product_type_default')->checkPeriod($product, $buyRequest);

        $Period = Mage::getModel('recurringandrentalpayments/terms');

        /* We should add custom options that doesnt exist */
        if ($buyRequest->getIndiesRecurringandrentalpaymentsSubscriptionType()) {
            if ($Period->load($buyRequest->getIndiesRecurringandrentalpaymentsSubscriptionType())->getId()) {
                $product->addCustomOption('indies_recurringandrentalpayments_subscription_type', $Period->getId());
            }
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

            //$performDateCompare = !!Mage::getSingleton('customer/session')->getCustomer()->getId();
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

        if ($attributes = $buyRequest->getSuperAttribute()) {
            $result = Mage_Catalog_Model_Product_Type_Abstract::prepareForCartAdvanced($buyRequest, $product);
            if (is_array($result)) {
                $product = $this->getProduct($product);
                /**
                 * $attributes = array($attributeId=>$attributeValue)
                 */
                if ($subProduct = $this->getProductByAttributes($attributes, $product)) {
                    $product->addCustomOption('attributes', serialize($attributes));
                    $product->addCustomOption('product_qty_' . $subProduct->getId(), 1, $subProduct);
                    $product->addCustomOption('simple_product', $subProduct->getId(), $subProduct);

                    $_result = $subProduct->getTypeInstance(true)->prepareForCartAdvanced($buyRequest, $subProduct);
                    if (is_string($_result) && !is_array($_result)) {
                        return $_result;
                    }

                    if (!isset($_result[0])) {
                        return Mage::helper('checkout')->__('Can not add item to shopping cart');
                    }

                    /**
                     * Adding parent product custom options to child product
                     * to be sure that it will be unique as its parent
                     */
                    if ($optionIds = $product->getCustomOption('option_ids')) {
                        $optionIds = explode(',', $optionIds->getValue());
                        foreach ($optionIds as $optionId) {
                            if ($option = $product->getCustomOption('option_' . $optionId)) {
                                $_result[0]->addCustomOption('option_' . $optionId, $option->getValue());
                            }
                        }
                    }

                    if ($buyRequest->getIndiesRecurringandrentalpaymentsSubscriptionType()) {
                        if ($Period->getId()) {
                            $_result[0]->addCustomOption('indies_recurringandrentalpayments_subscription_start', $date->toString('Y-MM-dd'));
                            $_result[0]->addCustomOption('indies_recurringandrentalpayments_subscription_type', $Period->getId());
                        }
                    }

                    $_result[0]->setParentProductId($product->getId())
                    // add custom option to simple product for protection of process when we add simple product separately
                            ->addCustomOption('parent_product_id', $product->getId())
                            ->setCartQty(1);

                    $result[] = $_result[0];

                    return $result;
                }
            }
        }
        return $this->getSpecifyOptionMessage();
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

    public function processBuyRequest($product, $buyRequest)
    {
        $toReturn = parent::processBuyRequest($product, $buyRequest);
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