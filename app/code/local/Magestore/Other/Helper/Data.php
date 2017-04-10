<?php

class Magestore_Other_Helper_Data extends Mage_Core_Helper_Abstract
{
    public function getEnableOther()
    {
        return Mage::getStoreConfig('other/general/enable', Mage::app()->getStore());
    }

    public function enableDropShip()
    {
        return Mage::getStoreConfig('other/drop_ship/enable', Mage::app()->getStore());
    }

    public function skuDropShip()
    {
        return Mage::getStoreConfig('other/drop_ship/sku', Mage::app()->getStore());
    }

    public function getListDropShipSku()
    {
        $list = $this->skuDropShip();
        $result = array();
        if ($list != null) {
            $data = explode(',', $list);
            foreach ($data as $sku) {
                $result[] = trim(strtolower($sku));
            }
        }

        return $result;

    }

    public function isInDropShipList($product)
    {

        $product_exclusion = $this->getListDropShipSku();

        if (sizeof($product_exclusion) == 0)
            return false;
        else {
            if (in_array(strtolower(trim($product->getSku())), $product_exclusion))
                return true;
            else
                return false;
        }
    }

    public function dropShipInCart()
    {
        $cart = Mage::getModel('checkout/session')->getQuote();
        $dropShips = $this->getListDropShipSku();
        if($dropShips == null)
            return false;

        $items = $cart->getAllItems();
        if(sizeof($items) > 0){
            $is_normal = 0;
            $is_drop_ship = false;
            foreach ($cart->getAllItems() as $item) {
                $product = Mage::getModel('catalog/product')->load($item->getProduct()->getId());
                if(in_array(strtolower($product->getSku()), $dropShips))
                {
                    $is_drop_ship = true;
                } else if(strcasecmp($item->getProduct()->getTypeId(),Mage_Catalog_Model_Product_Type::TYPE_VIRTUAL) != 0)
                    $is_normal++;
            }

            return ($is_drop_ship && $is_normal == 0 && $this->enableDropShip());
        } else {
            return false;
        }
    }
}