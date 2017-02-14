<?php

/**
 * Created by PhpStorm.
 * User: anthony
 * Date: 2/14/17
 * Time: 10:40 AM
 */
class Magestore_TruWallet_Block_Adminhtml_Renderer_Transaction_Grid extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
        $value = $row->getData($this->getColumn()->getIndex());
        if($value != null && $value > 0)
            return '<a target="_blank" href="'.Mage::helper('adminhtml')->getUrl('adminhtml/sales_order/view',array('order_id'=>$value)).'">'.$value.'</a>';
        else
            return '';

    }

}

?>