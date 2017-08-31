<?php
/**
 * Created by PhpStorm.
 * User: longvuxuan
 * Date: 8/31/17
 * Time: 5:39 PM
 */

class Magestore_SpecialOccasion_Block_Adminhtml_Renderer_History_Email extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
        $value = $row->getData($this->getColumn()->getIndex());
        $html = '<a href="mailto:'.$value.'">'.$value.'</a>';
        return '<span>' . $html . '</span>';

    }

}
