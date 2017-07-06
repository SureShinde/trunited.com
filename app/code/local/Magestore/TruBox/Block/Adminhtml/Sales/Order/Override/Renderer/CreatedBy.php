<?php
/**
 * Created by PhpStorm.
 * User: gacoi
 * Date: 5/10/17
 * Time: 10:57 PM
 */

class Magestore_TruBox_Block_Adminhtml_Sales_Order_Override_Renderer_CreatedBy extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
        $value = $row->getData('entity_id');
        $order = Mage::getModel('sales/order')->load($value);
        return $order->getData('created_by') == 1 ? 'Yes' : 'No';
    }
}