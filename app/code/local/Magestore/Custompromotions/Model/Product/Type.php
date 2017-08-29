<?php

class Magestore_Custompromotions_Model_Product_Type
{
    public function toOptionArray()
    {
        $types = Mage::getModel('catalog/product_type')->getOptionArray();
        $result = array();
        foreach ($types as $k => $v) {
            $result[] = array(
                'value' => $k,
                'label' => $v,
            );
        }

        return $result;
    }

}
