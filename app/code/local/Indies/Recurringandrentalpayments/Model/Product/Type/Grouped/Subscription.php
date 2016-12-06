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

class Indies_Recurringandrentalpayments_Model_Product_Type_Grouped_Subscription extends Mage_Catalog_Model_Product_Type_Grouped
{
    const TYPE_CODE = 'grouped';

    protected $_canConfigure = true;

    protected function _addRecurringandrentalpaymentsInfo($buyRequest, $product)
    {
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
     * Prepares product for cart according to buyRequest.
     *
     * @param Varien_Object $buyRequest
     * @param object        $product [optional]
     * @return
     */
    public function prepareForCart(Varien_Object $buyRequest, $product = null)
    {
        Mage::getModel('recurringandrentalpayments/product_type_default')->checkPeriod($product, $buyRequest);

        /*
         * For creating order from admin
         * If product is added to cart from admin, we doesn't add sart custom options to it.
         */
        $req = Mage::app()->getFrontController()->getRequest();

        $product = $this->getProduct($product);

        $productsInfo = $buyRequest->getSuperGroup();

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

        if (!empty($productsInfo) && is_array($productsInfo)) {
            $products = array();
            $associatedProducts = $this->getAssociatedProducts($product);
            if ($associatedProducts) {
                foreach ($associatedProducts as $subProduct) {
                    if (isset($productsInfo[$subProduct->getId()])) {
                        $qty = $productsInfo[$subProduct->getId()];
                        if (!empty($qty) && is_numeric($qty)) {

                            $this->_addRecurringandrentalpaymentsInfo($buyRequest, $subProduct);

                            $_result = $subProduct->getTypeInstance(true)
                                    ->prepareForCart($buyRequest, $subProduct);
                            if (is_string($_result) && !is_array($_result)) {
                                return $_result;
                            }

                            if (!isset($_result[0])) {
                                return Mage::helper('checkout')->__('Cannot add the item to shopping cart.');
                            }

                            $_result[0]->setCartQty($qty);
                            $_result[0]->addCustomOption('product_type', self::TYPE_CODE, $product);


                            $RecurringandrentalpaymentsSubscriptionStart = $RecurringandrentalpaymentsSubscriptionType = null;
                            if ($subProduct->getCustomOption('indies_recurringandrentalpayments_subscription_start')) {
                               
                                $RecurringandrentalpaymentsSubscriptionStart = new Zend_Date($subProduct->getCustomOption('indies_recurringandrentalpayments_subscription_start')->getValue(), Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT));
                                $RecurringandrentalpaymentsSubscriptionStart = $RecurringandrentalpaymentsSubscriptionStart->toString(preg_replace(array('/M/', '/d/'), array('MM', 'dd'), Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT)));
                            }
                            if ($subProduct->getCustomOption('indies_recurringandrentalpayments_subscription_type')) {
                                $RecurringandrentalpaymentsSubscriptionType = $subProduct->getCustomOption('indies_recurringandrentalpayments_subscription_type')->getValue();
                            }
                            $_result[0]->addCustomOption('info_buyRequest',
                                                         serialize(array(
                                                                        'super_product_config' => array(
                                                                            'product_type' => self::TYPE_CODE,
                                                                            'product_id' => $product->getId()
                                                                        ),
                                                                        'options' => $buyRequest->getOptions(),
                                                                        'indies_recurringandrentalpayments_subscription_start' => $RecurringandrentalpaymentsSubscriptionStart,
                                                                        'indies_recurringandrentalpayments_subscription_type' => $RecurringandrentalpaymentsSubscriptionType
                                                                   ))
                            );
                            $products[] = $_result[0];
                        }
                    }
                }
            }
            if (count($products)) {
                return $products;
            }
        }
        return Mage::helper('catalog')->__('Please specify the quantity of product(s).');

    }

    public function prepareForCartAdvanced(Varien_Object $buyRequest, $product = null, $processMode = null)
    {
        Mage::getModel('recurringandrentalpayments/product_type_default')->checkPeriod($product, $buyRequest);
        return $this->prepareForCart($buyRequest, $product);
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
        if($plan->getPlanStatus() && $this->getProduct($product)->getSubscriptionPrice() == '')
            $this->getProduct($product)->setSubscriptionPrice($product->getData('price'));
    }
}