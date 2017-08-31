<?php
/**
 * Created by PhpStorm.
 * User: longvuxuan
 * Date: 8/30/17
 * Time: 11:21 AM
 */

class Magestore_SpecialOccasion_Block_Adminhtml_System_Config_Form_Fieldset_Occasion extends
    Mage_Adminhtml_Block_System_Config_Form_Field_Array_Abstract
{
    public function _prepareToRender()
    {
        $this->addColumn('occasion_name', array(
            'label' => Mage::helper('specialoccasion')->__('Occasion Name'),
            'style' => 'width:300px',
        ));
        $this->_addAfter = false;
        $this->_addButtonLabel = Mage::helper('specialoccasion')->__('Add');
    }

}
