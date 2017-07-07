<?php

/**
 * Created by PhpStorm.
 * User: anthony
 * Date: 1/17/17
 * Time: 2:16 PM
 */
class Magestore_TruBox_Block_Adminhtml_Renderer_TruBox_Qty extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
        $value = $row->getData();
        $product_id = $value['entity_id'];
        $resource = Mage::getSingleton('core/resource');
        $readConnection = $resource->getConnection('core_read');
        $table = $resource->getTableName('trubox/item');
        $query = 'SELECT SUM(qty) FROM ' . $table . ' WHERE product_id = '. (int)$product_id . ' GROUP BY product_id';
        $qty = $readConnection->fetchOne($query);
        return $qty;
    }

}