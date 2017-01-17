<?php
/**
 * Magestore
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magestore.com license that is
 * available through the world-wide-web at this URL:
 * http://www.magestore.com/license-agreement.html
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Magestore
 * @package     Magestore_TruBox
 * @module      TruBox
 * @author      Magestore Developer
 *
 * @copyright   Copyright (c) 2016 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 *
 */

/**
 * TruBox Helper
 *
 * @category    Magestore
 * @package     Magestore_TruBox
 * @module      TruBox
 * @author      Magestore Developer
 */
class Magestore_TruBox_Helper_Item extends Mage_Core_Helper_Abstract
{
    public function getItemData($item_id)
    {
        $item = Mage::getModel('trubox/item')->load($item_id);
        $product = Mage::getModel('catalog/product')->load($item->getProductId());
        $price_options = 0;

        $option_params = json_decode($item->getOptionParams(), true);
        $product_url = Mage::helper("adminhtml")->getUrl("adminhtml/catalog_product/edit",array("id"=>$product->getId()));

        $name = '<a href="'.$product_url.'"><strong>'.$product->getName().'</strong></a>';
        $name .= '<dl class="item-options">';
        if ($product->getTypeId() == 'configurable') {
            $_options = Mage::helper('trubox')->getConfigurableOptionProduct($product);
            if ($_options && sizeof($option_params) > 0){
                foreach ($_options as $_option){
                    $_attribute_value = 0;
                    foreach($option_params as $k=>$v){
                        if($k == $_option['attribute_id']){
                            $_attribute_value = $v;
                            break;
                        }
                    }

                    if($_attribute_value > 0){
                        $name .= '<dt>'.$_option['label'].'</dt>';
                        foreach($_option['values'] as $val){

                            if($val['value_index'] == $_attribute_value){
                                $name .= '<dd>'.$val['default_label'].'</dd>';
                                break;
                            }
                        }
                    }
                }
            }
        } else {
            foreach ($product->getOptions() as $o) {
                $values = $o->getValues();
                $_attribute_value = 0;

                foreach($option_params as $k=>$v){
                    if($k == $o->getOptionId()){
                        $_attribute_value = $v;
                        break;
                    }
                }
                if($_attribute_value > 0){
                    $name .= '<dt>'.$o->getTitle().'</dt>';
                    foreach($values as $val){
                        if(is_array($_attribute_value)){
                            if(in_array($val->getOptionTypeId(), $_attribute_value)) {
                                $name .= '<dd>'.$val->getTitle().'</dd>';
                                $price_options += $val->getPrice();
                            }
                        } else if($val->getOptionTypeId() == $_attribute_value){
                            $name .= '<dd>'.$val->getTitle().'</dd>';
                            $price_options += $val->getPrice();
                        }
                    }
                }
            }
        }
        $name .= '</dl>';

        $price = ($product->getFinalPrice() + $price_options)*$item->getQty();

        return array(
            'item_name' => $name,
            'item_price' => $price,
            'product_id' => $product->getId(),
        );
    }

    public function updatePrice()
    {
        $items = Mage::getModel('trubox/item')->getCollection()->addFieldToSelect('*');
        $transactionSave = Mage::getModel('core/resource_transaction');
        foreach ($items as $item)
        {
            zend_debug::dump($item->getData('price'));
            $_data = $this->getItemData($item->getId());
            $item->setPrice($_data['product_price']);
            $transactionSave->addObject($item);
            zend_debug::dump($item->debug());
        }
        $transactionSave->save();

        echo 'success';
    }
}
