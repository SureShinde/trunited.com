<?php
class Magestore_CustomProduct_Model_Globalbrand_Product_Price extends Mage_Catalog_Model_Product_Type_Price {
    public function getFinalPrice($qty = null, $product) {
        if (is_null($qty) && !is_null($product->getCalculatedFinalPrice())) {
            return $product->getCalculatedFinalPrice();
        }
        $finalPrice = parent::getFinalPrice($qty, $product);
        return $finalPrice;
    }
}
