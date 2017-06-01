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

class AW_Eventdiscount_Block_Adminhtml_Timer_Edit_Tab_General extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $this->setForm($form);
        $_fieldset = $form->addFieldset('timer_form', array('legend' => $this->__('General')));
        $_data = Mage::registry('timer_data');

        $_fieldset->addField('timer_name', 'text', array(
            'name'     => 'timer_name',
            'label'    => $this->__('Timer name'),
            'title'    => $this->__('Timer name'),
            'required' => true,
            'note'     => $this->__('admin visible only'),
        ));
        $_fieldset->addField('title', 'text', array(
            'name'     => 'title',
            'label'    => $this->__('Title'),
            'title'    => $this->__('Title'),
            'required' => true,
            'note'     => $this->__('frontend only'),
            'onchange' => "$$('.aw_eventdiscount_title')[0].innerHTML=this.value;",
            'onkeyup'  => "$$('.aw_eventdiscount_title')[0].innerHTML=this.value;"
        ));

        if (Mage::app()->isSingleStoreMode()) {
            $_data->setStoreIds(0);
            $_fieldset->addField('store_ids', 'hidden', array(
                'name' => 'store_ids[]'
            ));
        } else {
            $_fieldset->addField('store_ids', 'multiselect', array(
                'name'     => 'store_ids[]',
                'label'    => $this->__('Store view'),
                'title'    => $this->__('Store view'),
                'required' => true,
                'values'   => Mage::getSingleton('adminhtml/system_store')->getStoreValuesForForm(false, true),
            ));
        }
        if ($_data->getData('status') === null) {
            $_data->setData('status', 1);
        }
        $_fieldset->addField('status', 'select', array(
            'name'   => 'status',
            'label'  => $this->__('Status'),
            'title'  => $this->__('Status'),
            'values' => Mage::getModel('aweventdiscount/source_timer_status')->toOptionArray(),
        ));

        $outputFormat = Mage::app()->getLocale()->getDateTimeFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT);
        if ($_data->getData('active_from') === null) {
            $_data->setData('active_from', Mage::app()->getLocale()->date());
        } else {
           $_data->setData('active_from',
               Mage::app()->getLocale()->date($_data->getData('active_from'), Varien_Date::DATETIME_INTERNAL_FORMAT)
           );
        }
        $_fieldset->addField('active_from', 'date', array(
            'name'         => 'active_from',
            'label'        => $this->__('Active from'),
            'title'        => $this->__('Active from'),
            'class'        => 'required-entry',
            'image'        => $this->getSkinUrl('images/grid-cal.gif'),
            'input_format' => Varien_Date::DATETIME_INTERNAL_FORMAT,
            'format'       => $outputFormat,
            'time'         => true,
            'required'     => true,
        ));
        if ($_data->getData('active_to') === null) {
            $_data->setData('active_to', Mage::app()->getLocale()->date()->addDay(1));
        } else {
            $_data->setData('active_to',
                Mage::app()->getLocale()->date($_data->getData('active_to'), Varien_Date::DATETIME_INTERNAL_FORMAT)
            );
        }
        $_fieldset->addField('active_to', 'date', array(
            'name'         => 'active_to',
            'label'        => $this->__('Active to'),
            'title'        => $this->__('Active to'),
            'class'        => 'required-entry',
            'input_format' => Varien_Date::DATETIME_INTERNAL_FORMAT,
            'format'       => $outputFormat,
            'image'        => $this->getSkinUrl('images/grid-cal.gif'),
            'time'         => true,
            'required'     => true,
        ));
        $_duration = array(1,0,0);
        if ($_data->getData('duration') !== null) {
            $timeDuration = $_data->getData('duration');
            $_duration = array();
            $_duration[0] = intval($timeDuration / 3600);
            $_duration[1] = intval(($timeDuration - $_duration[0] * 3600) / 60);
            $_duration[2] = intval($timeDuration - ($_duration[0] * 3600 + $_duration[1] * 60));
        }
        $_data->setData('duration', implode(',', $_duration));
        $_fieldset->addField('duration', 'time', array(
            'name'     => 'duration',
            'label'    => $this->__('Duration'),
            'title'    => $this->__('Duration'),
            'class'    => 'required-entry',
            'required' => true,
            'format'   => $outputFormat,
            'image'    => $this->getSkinUrl('images/grid-cal.gif'),
            'time'     => true,
            'note'     => $this->__('hours : minutes : seconds')
        ));
        if ($_data->getData('limit_per_customer') === null) {
            $_data->setData('limit_per_customer', 0);
        }
        $_fieldset->addField('limit_per_customer', 'text', array(
            'name'     => 'limit_per_customer',
            'label'    => $this->__('Run per customer'),
            'title'    => $this->__('Run per customer'),
            'required' => true,
            'note'     => $this->__('0 = unlimited'),
        ));
        if ($_data->getData('limit') === null) {
            $_data->setData('limit', 0);
        }
        $_fieldset->addField('limit', 'text', array(
            'name'     => 'limit',
            'label'    => $this->__('Total runs'),
            'title'    => $this->__('Total runs'),
            'required' => true,
            'note'     => $this->__('0 = unlimited'),
        ));
        $_fieldset->addField('url', 'text', array(
            'name'  => 'url',
            'label' => $this->__('URL'),
            'title' => $this->__('URL'),
        ));
        if ($_data->getData('url_type') === null) {
            $_data->setData('url_type', 0);
        }
        $_fieldset->addField('url_type', 'checkbox', array(
            'name'    => 'url_type',
            'label'   => $this->__('Open URL in new window'),
            'title'   => $this->__('Open URL in new window'),
            'checked' => $_data->getData('url_type'),
        ));
        $form->setValues($_data->getData());
        return parent::_prepareForm();
    }
}