<?php
/**
 * aheadWorks Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://ecommerce.aheadworks.com/AW-LICENSE.txt
 *
 * =================================================================
 *                 MAGENTO EDITION USAGE NOTICE
 * =================================================================
 * This software is designed to work with Magento community edition and
 * its use on an edition other than specified is prohibited. aheadWorks does not
 * provide extension support in case of incorrect edition use.
 * =================================================================
 *
 * @category   AW
 * @package    AW_Eventdiscount
 * @version    1.0.5
 * @copyright  Copyright (c) 2010-2012 aheadWorks Co. (http://www.aheadworks.com)
 * @license    http://ecommerce.aheadworks.com/AW-LICENSE.txt
 */

class AW_Eventdiscount_Block_Adminhtml_Timer_Edit_Tab_Design extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $form->setHtmlIdPrefix('rule_');
        $this->setForm($form);

        $_fieldset = $form->addFieldset('timer_form', array('legend' => Mage::helper('eventdiscount')->__('Design')));
        $_data = Mage::registry('timer_data');
        $_fieldset->addField('design', 'select', array(
            'name' => 'design',
            'label' => Mage::helper('eventdiscount')->__('Design package'),
            'title' => Mage::helper('eventdiscount')->__('Design package'),
            'values' => Mage::getModel('aweventdiscount/source_design')->toOptionArray(),
            'required' => true,
            'onchange' => "$('preview_timer').className = 'aw_eventdiscount_timer_container_'+this.value;
                $('aw_timer_wraper').className = 'aw_timer_wraper_'+this.value; return false;"
        ));
        $_fieldset->addField('appearing', 'select', array(
            'name'     => 'appearing',
            'label'    => Mage::helper('eventdiscount')->__('Appearing'),
            'title'    => Mage::helper('eventdiscount')->__('Appearing'),
            'values'   => array(
                'fade'     => Mage::helper('eventdiscount')->__('Fade'),
                'slide'    => Mage::helper('eventdiscount')->__('Slide'),
                'blink'    => Mage::helper('eventdiscount')->__('Blink'),
                'none'     => Mage::helper('eventdiscount')->__('None')),
            'required' => true,
        ));
        if ($_data->getData('position') === null) {
            $_data->setData('position', 'TC');
        }
        $renderer = new  AW_Eventdiscount_Block_Adminhtml_Renderer_Position_Renderer();

        $_fieldset->addField('position', 'select', array(
            'name' => 'position',
            'label' => Mage::helper('eventdiscount')->__('Timer position'),
            'title' => Mage::helper('eventdiscount')->__('Timer position'),
            'required' => true,
            'values' => array(
                array('label' => $this->__('Top-Left'), 'value' => 'TL'),
                array('label' => $this->__('Top-Center'), 'value' => 'TC'),
                array('label' => $this->__('Top-Right'), 'value' => 'TR'),
                array('label' => $this->__('Middle-Left'), 'value' => 'ML'),
                array('label' => $this->__('Middle-Center'), 'value' => 'MC'),
                array('label' => $this->__('Middle-Right'), 'value' => 'MR'),
                array('label' => $this->__('Bottom-Left'), 'value' => 'BL'),
                array('label' => $this->__('Bottom-Center'), 'value' => 'BC'),
                array('label' => $this->__('Bottom-Right'), 'value' => 'BR'),
            ),
        ))->setRenderer($renderer);

        if (is_null($_data->getData('color'))) {
            $_data->setData('color', 'BC2323');
        }
        $_fieldset->addField('color', 'text', array(
            'name' => 'color',
            'label' => Mage::helper('eventdiscount')->__('Font color on ending'),
            'title' => Mage::helper('eventdiscount')->__('Font color on ending'),
            'maxlength' => 7,
            'note' => $this->__('e.g. DF0F62'),
            'value' => '000000'
        ));
        $_fieldset->addField('notice', 'textarea', array(
            'name' => 'notice',
            'label' => Mage::helper('eventdiscount')->__('Timer notice'),
            'title' => Mage::helper('eventdiscount')->__('Timer notice'),
            'onchange' => "$$('.aw_eventdiscount_notice')[0].innerHTML=this.value;",
            'onkeyup' => "$$('.aw_eventdiscount_notice')[0].innerHTML=this.value;",
            'note' => $this->__('HTML tags are allowed'),
        ));
        $rendererPreview = new  AW_Eventdiscount_Block_Adminhtml_Renderer_Preview_Renderer();

        $_fieldset->addField('preview', 'text', array(
                'name' => 'preview',
                'values' => array('title' => $_data->getData('title'),
                    'notice' => $_data->getData('notice'),
                    'color' => '#' . $_data->getData('color'))
            )
        )->setRenderer($rendererPreview);
        $form->setValues($_data->getData());
        return parent::_prepareForm();
    }
}