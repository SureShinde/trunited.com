<?php
/**
 * Created by PhpStorm.
 * User: BeoBinhThuong
 * Date: 11/24/2016
 * Time: 1:44 PM
 */

class Magestore_TruBox_Block_Adminhtml_System_Configuration_Date extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        $date = new Varien_Data_Form_Element_Date;
        $format = 'MM/d/y';

        $data = array(
            'name'      => $element->getName(),
            'html_id'   => $element->getId(),
            'image'     => $this->getSkinUrl('images/grid-cal.gif'),
        );

        $date->setData($data);
        $date->setValue($element->getValue(), $format);
        $date->setFormat($format);
        $date->setForm($element->getForm());

        return $date->getElementHtml();
    }
}