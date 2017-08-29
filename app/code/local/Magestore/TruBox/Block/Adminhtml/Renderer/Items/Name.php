<?php

/**
 * Created by PhpStorm.
 * User: anthony
 * Date: 1/17/17
 * Time: 2:16 PM
 */
class Magestore_TruBox_Block_Adminhtml_Renderer_Items_Name extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
        $value = $row->getData();
        $_data = Mage::helper('trubox/item')->getItemData($value['entity_id']);
        return $_data['item_name'];
    }

}