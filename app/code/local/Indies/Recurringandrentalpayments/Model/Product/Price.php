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

class Indies_Recurringandrentalpayments_Model_Product_Price extends Varien_Object
{

    public static $doNotApplyFirstPeriodPrice = false;
    protected $_iterate;

    /**
     * @param Mage_Catalog_Model_Product $Product
     * @return boolean
     */
    public function isSubscriptionPriceSet(Mage_Catalog_Model_Product $Product)
    {
		$priceStr = Mage::getModel('recurringandrentalpayments/plans')->load($Product->getId(),'product_id');
        if (strlen($priceStr->getFirstTermPrice())) {
            return true;
        }
        return false;
    }


    /**
     * TODO Remove this function from downloadable and simple subscription
     * Add Shipping cost to Recurringandrentalpayments subscription price
     * @param Mage_Catalog_Model_Product $product
     * @deprecated Use getShippingCost($product) for getting ShippingCost only
     * @return float
     */
    public function getRnrSubscriptionPrice($product, $includeCatalogRule)
    {
            $shippingCost = Mage::getStoreConfig(Indies_Recurringandrentalpayments_Helper_Config::XML_PATH_GENERAL_SHIPPING_COST);
      

        if ($product->isGrouped()) {
            $price = $this->__getGroupedPrice($product);
        }
        else {
            $price = $this->__getSimplePrice($product, $includeCatalogRule);
        }
        if ($product->getData('type_id') == 'downloadable') {
            return $price;
        }

        if (!is_numeric($shippingCost)) {
            return $price;
        }
        return $price + $shippingCost;
    }

    public function getRnrFirstPeriodPrice($product)
    {
        //$shippingCost = $product->getIndiesRecurringandrentalpaymentsShippingCost();
		$priceStr = Mage::getModel('recurringandrentalpayments/plans')->load($Product->getId(),'product_id');
        if ($shippingCost == '') {
            $shippingCost = Mage::getStoreConfig(Indies_Recurringandrentalpayments_Helper_Config::XML_PATH_GENERAL_SHIPPING_COST);
        }
		
        if ($priceStr->getFirstTermPrice() != '') {
            $price = $product->getPriceModel()->getPriceModel()->getFirstSubscriptionFinalPrice($product);
            //$price = $product->getIndiesRecurringandrentalpaymentsFirstPeriodPrice();
        } else {
            return null;
        }

        if ($product->getData('type_id') == 'downloadable') {
            return $price;
        }

        if (!is_numeric($shippingCost)) {
            return $price;
        }
        return $price + $shippingCost;
    }

    /**
     * Get Shipping cost for product
     * @param Mage_Catalog_Model_Product $product
     * @return float
     */
    public function getShippingCost($product)
    {
        $baseProduct = Mage::getModel('catalog/product')->load($product->getId());
        $shippingCost = $baseProduct->getIndiesRecurringandrentalpaymentsShippingCost();
        if ($shippingCost == '') {
            $shippingCost = Mage::getStoreConfig(Indies_Recurringandrentalpayments_Helper_Config::XML_PATH_GENERAL_SHIPPING_COST);
        }

        if (!is_numeric($shippingCost)) $shippingCost = 0;
        return $shippingCost;
    }

    /**
     * Determines if product is ordered first time or not by the customer
     * @return
     */
    public function isOrderedFirstTime(Mage_Catalog_Model_Product $Product, $Customer = null)
    {
        $iteratorIsNotRunning = Mage::registry(Indies_Recurringandrentalpayments_Model_Subscription::ITERATE_STATUS_REGISTRY_NAME) != Indies_Recurringandrentalpayments_Model_Subscription::ITERATE_STATUS_RUNNING;
        return $iteratorIsNotRunning && !self::$doNotApplyFirstPeriodPrice;
    }

    public function getSubscriptionFinalPrice(Mage_Catalog_Model_Product $product)
    {
        return $this->getRnrFinalPrice($product, $product->getSubscriptionPrice());
    }

    public function getFirstSubscriptionFinalPrice(Mage_Catalog_Model_Product $product)
    {
		$plan = Mage::getModel('recurringandrentalpayments/plans')->load($product->getId(),'product_id');
		return $this->getRnrFinalPrice($product, $plan->getFirstTermPrice());
    }

    /*
      *	calculate Recurringandrentalpayments Product Final price by given Price
     */

    public function getRnrFinalPrice(Mage_Catalog_Model_Product $product, $price)
    {

        $resource = Mage::getSingleton('core/resource');
        $db = $resource->getConnection('core_read');

        $select = $db->select()
                ->from($resource->getTableName('catalogrule/rule_product'))
                ->where('from_time <= ?', time())
                ->where('IF(to_time > 0, to_time >= ?, to_time = 0)', time())
                ->where('product_id = ?', $product->getId())
                ->where('website_id = ?', Mage::app()->getWebsite()->getId())
                ->where('customer_group_id = ?', Mage::getSingleton('customer/session')->getCustomer()->getGroupId())
                ->order('sort_order ASC');
        foreach ($db->fetchAll($select) as $rule) {
            $price = $this->calcPriceRule($rule['action_operator'], $rule['action_amount'], $price);
            if ((int)$rule['action_stop'])
                break;
        }

        return $price;
    }

    public function calcPriceRule($actionOperator, $ruleAmount, $price)
    {
        switch ($actionOperator) {
            case 'to_fixed':
                $priceRule = $ruleAmount;
                break;
            case 'to_percent':
                $priceRule = $price * $ruleAmount / 100;
                break;
            case 'by_fixed':
                $priceRule = $price - $ruleAmount;
                break;
            case 'by_percent':
                $priceRule = $price * (1 - $ruleAmount / 100);
                break;
        }
        return max($priceRule, 0);
    }

    private function __getSimplePrice($product, $includeCatalogRule)
    {
		$priceStr = Mage::getModel('recurringandrentalpayments/plans')->load($product->getId(),'product_id');
        
		if ($this->isSubscriptionPriceSet($product)) {
            if ($includeCatalogRule)
                $price = $product->getPriceModel()->getPriceModel()->getSubscriptionFinalPrice($product);
            else
                $price = $priceStr->getFirstTermPrice();
        } else {
            $price = $product->getData('price');
        }
        return $price;
    }

    private function __getGroupedPrice($product)
    {
        $_prices = array();
        $childs = $product->getTypeInstance()->getAssociatedProducts();
        foreach($childs as $item) {
            $_prices[$item->getId()] = number_format($item->getSubscriptionPrice(), 4);
        }
        $price = min($_prices);
        if (!is_numeric($price))
            $price = $product->getData('price');
        return $price;
    }

}