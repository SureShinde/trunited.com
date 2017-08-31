<?php
/**
 * Created by PhpStorm.
 * User: longvuxuan
 * Date: 8/31/17
 * Time: 5:39 PM
 */

class Magestore_SpecialOccasion_Block_Adminhtml_Renderer_History_Product extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
        $value = $row->getData($this->getColumn()->getIndex());
        $data = json_decode($value, true);
        $html = 'ID: '.$data[0]['product_id'].' - ';
        $html .= '<a href="'.Mage::helper("adminhtml")->getUrl("adminhtml/catalog_product/edit", array('id'=>$data[0]['product_id'])).'">'.$data[0]['product_name'].'</a> - ';
        $html .= 'Qty: '.$data[0]['qty'].' - ';
        $html .= 'Price: '.$data[0]['price'].'<br />';
        return '<span>' . $html . '</span>';

    }

}
