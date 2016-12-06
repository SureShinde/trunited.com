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

class Indies_Recurringandrentalpayments_Model_Product_Type_Configurable_Price extends Mage_Catalog_Model_Product_Type_Configurable_Price
{
    /**
     * Return price helper
     * @todo optimize load
     * @return Mage_Core_Model_Abstract
     */
    public function getPriceModel()
    {
        return Mage::getSingleton('recurringandrentalpayments/product_price');
    }

    protected function _applySpecialPrice($product, $finalPrice)
    {
        $plan = Mage::getModel('recurringandrentalpayments/plans')->load($product->getId(),'product_id');
		if ($plan->getPlanStatus() &&
            $product->getTypeInstance()->getDefaultSubscriptionPeriodId() != Indies_Recurringandrentalpayments_Model_Terms::PERIOD_TYPE_NONE
        ) {
            return $finalPrice;
        }
        return parent::_applySpecialPrice($product, $finalPrice);
    }

    /**
     * Add different params (options price, link price) to price
     * @param Mage_Catalog_Model_Product $product
     * @param int $qty
     * @param float $price
     * @return float
     */

    public function calculateFinalPrice($product, $qty, $price)
    {
        $finalPrice = $this->_applyOptionsPrice($product, $qty, $price);
        $finalPrice += $this->getConfigPrice($qty, $product, $price);
        return $finalPrice;
    }

    /**
     * Add configurable attributes price
     * @param int $qty
     * @param Mage_Catalog_Model_Product $product
     * @return float
     */
    public function getConfigPrice($qty, $product, $price)
    {
        $product->getTypeInstance(true)
                ->setStoreFilter($product->getStore(), $product);
        $attributes = $product->getTypeInstance(true)
                ->getConfigurableAttributes($product);

        $selectedAttributes = array();
        if ($product->getCustomOption('attributes')) {
            $selectedAttributes = unserialize($product->getCustomOption('attributes')->getValue());
        }

        $basePrice = $price;
        $finalPrice = 0;
        foreach ($attributes as $attribute) {
            $attributeId = $attribute->getProductAttribute()->getId();
            $value = $this->_getValueByIndex(
                $attribute->getPrices() ? $attribute->getPrices() : array(),
                isset($selectedAttributes[$attributeId]) ? $selectedAttributes[$attributeId] : null
            );
            if ($value) {
                if ($value['pricing_value'] != 0) {
                    $finalPrice += $this->_calcSelectionPrice($value, $basePrice);
                }
            }
        }

        return $finalPrice;
    }

    public function getFinalPrice($qty, $product)
    {
        try {

            $baseProduct = Mage::getModel('catalog/product')->setStoreId($product->getStoreId())->load($product->getId());
			$plan = Mage::getModel('recurringandrentalpayments/plans')->load($product->getId(),'product_id');
            if ($this->getPriceModel()->isSubscriptionPriceSet($baseProduct)) {
                if (
                    $product->getCustomOption('indies_recurringandrentalpayments_subscription_type')
                    && ($typeId = $product->getCustomOption('indies_recurringandrentalpayments_subscription_type')->getValue())
                    && $plan->getPlanStatus()
                ) {
                    if ($typeId == Indies_Recurringandrentalpayments_Model_Terms::PERIOD_TYPE_NONE) {
                        return parent::getFinalPrice($qty, $product);
                    } else {
						 $plan = Mage::getModel('recurringandrentalpayments/plans')->load($product->getId(),'product_id');
						  if (
                            $this->isOrderedFirstTime($baseProduct)
                            && is_numeric($plan->getFirstTermPrice())
                            && $plan->getFirstTermPrice() >= 0
                            && !Mage::helper('recurringandrentalpayments')->isReordered($product->getId())
                        ) {
                            return $this->calculateFinalPrice($product, $qty,  $plan->getFirstTermPrice())
                                   + Mage::getModel('recurringandrentalpayments/product_price')->getShippingCost($product);
                        }
                        else {
                            return
                                    $this->calculateFinalPrice($product, $qty, $baseProduct->getSubscriptionPrice())
                                    + Mage::getModel('recurringandrentalpayments/product_price')->getShippingCost($product);
                        }
                    }
                }
            }
        } catch (Exception $e) {
            echo $e->getMessage();
            die();

        }
        return parent::getFinalPrice($qty, $product);
    }


    public function getPrice($product)
    {

        $plan=Mage::getModel('recurringandrentalpayments/plans')->load($product->getId(),'product_id');
		if ($this->getPriceModel()->isSubscriptionPriceSet($product) && $plan->getPlanStatus()) {
            $priceWithShippingCost = Mage::helper('recurringandrentalpayments')->getRnrSubscriptionPrice($product);
            if (
                $product->getCustomOption('indies_recurringandrentalpayments_subscription_type')
                && ($typeId = $product->getCustomOption('indies_recurringandrentalpayments_subscription_type')->getValue())
            ) {
                if ($typeId == Indies_Recurringandrentalpayments_Model_Terms::PERIOD_TYPE_NONE) {
                    return parent::getPrice($product);
                } else {
                    return $priceWithShippingCost;
                }
            } elseif ($product->getTypeInstance()->getDefaultSubscriptionPeriodId() != Indies_Recurringandrentalpayments_Model_Terms::PERIOD_TYPE_NONE) {
                return $priceWithShippingCost;
            }
        } else {
            // Probably category page
            $_product = Mage::getModel('catalog/product')->load($product->getId());
            $priceWithShippingCost = Mage::helper('recurringandrentalpayments')->getRnrSubscriptionPrice($_product);
            if ($_product->getTypeInstance()->requiresSubscriptionOptions($_product)) {
                if ($priceWithShippingCost) {
                    $price = $priceWithShippingCost;
                    $product->setData('final_price', $price);
                    return $price;
                }
            }
        }

        return parent::getPrice($product);
    }

    /**
     * Determines if product is ordered first time or not by the customer
     * @return
     */
    public function isOrderedFirstTime(Mage_Catalog_Model_Product $Product, $Customer = null)
    {
        return $this->getPriceModel()->isOrderedFirstTime($Product, $Customer);
    }

}
