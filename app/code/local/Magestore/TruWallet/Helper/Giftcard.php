<?php

class Magestore_TruWallet_Helper_Giftcard extends Mage_Core_Helper_Abstract
{
    public function getPlasticGiftCards()
    {
        $collection = Mage::getModel('catalog/product')->getCollection()
            ->addAttributeToSelect('entity_id')
            ->addAttributeToSelect('sku')
            ->addAttributeToSelect('status')
            ->addAttributeToSelect('visibility')
            ->addAttributeToFilter('type_id', array('eq' => 'simple'))
            ->addAttributeToFilter('SKU', array('like' => '%GP%'))
            ->addAttributeToFilter('status', array('eq' => Mage_Catalog_Model_Product_Status::STATUS_ENABLED))
            ->setOrder('entity_id', 'desc')
        ;

        Mage::getSingleton('cataloginventory/stock')->addInStockFilterToCollection($collection);
        Mage::getSingleton('catalog/product_visibility')->addVisibleInCatalogFilterToCollection($collection);

        if(sizeof($collection) > 0)
            return $collection->getColumnValues('entity_id');
        else
            return null;
    }

    public function plasticInCart()
    {
        $cart = Mage::getModel('checkout/session')->getQuote();
        $plastics = $this->getPlasticGiftCards();
        if($plastics == null)
            return false;

        $items = $cart->getAllItems();
        if(sizeof($items) > 0){
            $flag = false;
            foreach ($cart->getAllItems() as $item) {
                $product_id = $item->getProduct()->getId();
                if(in_array($product_id, $plastics))
                {
                    $flag = true;
                } else if(strcasecmp($item->getProduct()->getTypeId(),Mage_Catalog_Model_Product_Type::TYPE_VIRTUAL) != 0)
                    $flag = false;
            }

            return $flag;
        } else {
            return false;
        }
    }
}
