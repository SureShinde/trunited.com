<?php

/**
 * Created by PhpStorm.
 * User: anthony
 * Date: 1/17/17
 * Time: 2:16 PM
 */
class Magestore_TruBox_Block_Adminhtml_Renderer_Items_Price extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
        $value = $row->getData();
        $_data = Mage::helper('trubox/item')->getItemData($value['item_id']);
        return Mage::helper('core')->currency($_data['item_price'],true,false);
    }

}