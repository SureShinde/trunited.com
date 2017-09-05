<?php

/**
 * Created by PhpStorm.
 * User: anthony
 * Date: 1/17/17
 * Time: 2:16 PM
 */
class Magestore_Interest_Block_Adminhtml_Renderer_Interest_Item extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
        $value = $row->getData($this->getColumn()->getIndex());
        $html = '<a href="'.Mage::helper("adminhtml")->getUrl("interestadmin/adminhtml_interest/edit", array('id'=>$row->getData('interest_id'))).'">'.$value.'</a>';
        return '<span>' . $html . '</span>';
    }

}
