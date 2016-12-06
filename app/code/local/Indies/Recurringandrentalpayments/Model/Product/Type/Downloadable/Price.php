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

class Indies_Recurringandrentalpayments_Model_Product_Type_Downloadable_Price extends Mage_Downloadable_Model_Product_Price
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
        $plan=Mage::getModel('recurringandrentalpayments/plans')->load($product->getId(),'product_id');
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
        $finalPrice += $this->getLinksPrice($product);
        return $finalPrice;
    }

    /**
     * Adds links prices to base product price (only if they can be purchased separately)
     * @return float
     */
    public function getLinksPrice($product)
    {
        $linkPrice = 0;
        if ($product->getLinksPurchasedSeparately()) {
            if ($linksIds = $product->getCustomOption('downloadable_link_ids')) {
                $links = $product->getTypeInstance(true)
                        ->getLinks($product);
                foreach (explode(',', $linksIds->getValue()) as $linkId)
                {
                    if (isset($links[$linkId])) {
                        $linkPrice += $links[$linkId]->getPrice();
                    }
                }
            }
        }
        return $linkPrice;
    }

    public function getFinalPrice($qty = null, $product)
    {

        try {
            $baseProduct = Mage::getModel('catalog/product')->setStoreId($product->getStoreId())->load($product->getId());
            if ($this->getPriceModel()->isSubscriptionPriceSet($baseProduct)) {
                if ($product->getCustomOption('indies_recurringandrentalpayments_subscription_type') && ($typeId = $product->getCustomOption('indies_recurringandrentalpayments_subscription_type')->getValue())) {
                    if ($typeId == Indies_Recurringandrentalpayments_Model_Terms::PERIOD_TYPE_NONE) {
                        return parent::getFinalPrice($qty, $product);
                    } else
                    {
                        $plan = Mage::getModel('recurringandrentalpayments/plans')->load($product->getId(),'product_id');
						if ($this->isOrderedFirstTime($baseProduct)
                            && is_numeric($plan->getFirstTermPrice())
                            && $plan->getFirstTermPrice() >= 0
                            && !Mage::helper('recurringandrentalpayments')->isReordered($product->getId())
                        ) {
                            return $this->calculateFinalPrice($product, $qty, $plan->getFirstTermPrice());
                        }
                        else
                        {
                            return $this->calculateFinalPrice($product, $qty, Mage::helper('recurringandrentalpayments')->getRnrSubscriptionPrice($baseProduct));
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
		$plan = Mage::getModel('recurringandrentalpayments/plans')->load($product->getId(),'product_id');
        if ($this->getPriceModel()->isSubscriptionPriceSet($product) && $plan->getPlanStatus()) {
            $priceWithShippingCost = Mage::helper('recurringandrentalpayments')->getRnrSubscriptionPrice($product);
            if ($product->getCustomOption('indies_recurringandrentalpayments_subscription_type') && ($typeId = $product->getCustomOption('indies_recurringandrentalpayments_subscription_type')->getValue())) {
                if ($typeId ==  Indies_Recurringandrentalpayments_Model_Terms::PERIOD_TYPE_NONE) {
                    return parent::getPrice($product);
                } else {
                    return $priceWithShippingCost + $this->getLinksPrice($product);
                }
            } elseif ($product->getTypeInstance()->getDefaultSubscriptionPeriodId() !=  Indies_Recurringandrentalpayments_Model_Terms::PERIOD_TYPE_NONE) {
                return $priceWithShippingCost + $this->getLinksPrice($product);
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
