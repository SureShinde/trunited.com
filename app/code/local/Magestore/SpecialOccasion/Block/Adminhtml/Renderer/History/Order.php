<?php
/**
 * Created by PhpStorm.
 * User: longvuxuan
 * Date: 8/31/17
 * Time: 5:39 PM
 */

class Magestore_SpecialOccasion_Block_Adminhtml_Renderer_History_Order extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
        $value = $row->getData($this->getColumn()->getIndex());
        $html = '<a href="'.Mage::helper("adminhtml")->getUrl("adminhtml/sales_order/view", array('order_id'=>$row->getData('order_id'))).'">'.$value.'</a>';
        return '<span>' . $html . '</span>';

    }

}
