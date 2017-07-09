<?php

/**
 * Created by PhpStorm.
 * User: anthony
 * Date: 1/17/17
 * Time: 2:16 PM
 */
class Magestore_TruBox_Block_Adminhtml_Renderer_Product_Stock extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
        $value = $row->getData();
        $product = Mage::getModel('catalog/product')->load($value['product_id']);
        $option_params = json_decode($value['origin_params'], true);
        if($product->getTypeId() == 'configurable')
        {
            $flag = false;
            $main_child_product = Mage::getModel('catalog/product_type_configurable')->getProductByAttributes($option_params, $product);
            if(isset($main_child_product) && $main_child_product->getId())
            {
                $childProducts = Mage::getModel('catalog/product_type_configurable')->getUsedProducts(null,$product);

                foreach ($childProducts as $childProduct) {
                    $qty = Mage::getModel('cataloginventory/stock_item')->loadByProduct($childProduct)->getQty();
                    if ($childProduct->getId() == $main_child_product->getId()) {
                        if($qty <= 0)
                        {
                            $flag = true;
                        }
                        break;
                    }
                }
            }


            if($flag)
                return $this->__('Out of Stock');
            else
                return $this->__('In Stock');
        }

        return $value['stock_status'] == 1 ? $this->__('In Stock') : $this->__('Out of Stock');
    }

}