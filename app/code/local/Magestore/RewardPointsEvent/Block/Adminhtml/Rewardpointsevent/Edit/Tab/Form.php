<?php

/**
 * Magestore
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the Magestore.com license that is
 * available through the world-wide-web at this URL:
 * http://www.magestore.com/license-agreement.html
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category    Magestore
 * @package     Magestore_RewardPointsEvent
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * Rewardpointsevent Edit Form Content Tab Block
 * 
 * @category    Magestore
 * @package     Magestore_RewardPointsEvent
 * @author      Magestore Developer
 */
class Magestore_RewardPointsEvent_Block_Adminhtml_Rewardpointsevent_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form {

    /**
     * prepare tab form's information
     *
     * @return Magestore_RewardPointsEvent_Block_Adminhtml_Rewardpointsevent_Edit_Tab_Form
     */
    protected function _prepareForm() {
        $form = new Varien_Data_Form();
        $this->setForm($form);

        if (Mage::getSingleton('adminhtml/session')->getRewardPointsEventData()) {
            $data = Mage::getSingleton('adminhtml/session')->getRewardPointsEventData();
            Mage::getSingleton('adminhtml/session')->setRewardPointsEventData(null);
        } elseif (Mage::registry('rewardpointsevent_data')) {
            $data = Mage::registry('rewardpointsevent_data')->getData();
        }
        $fieldset = $form->addFieldset('rewardpointsevent_form', array(
            'legend' => Mage::helper('rewardpointsevent')->__('General Information')
        ));


        $fieldset->addField('title', 'text', array(
            'label' => Mage::helper('rewardpointsevent')->__('Title'),
            'class' => 'required-entry',
            'required' => true,
            'name' => 'title',
        ));
        $fieldset->addField('message', 'editor', array(
            'label' => Mage::helper('rewardpointsevent')->__('Message'),
            'title' => Mage::helper('rewardpointsevent')->__('Message'),
            'class' => 'required-entry',
            'required' => true,
            'name' => 'message',
            // 'wysiwyg' => true,
        ));
        $fieldset->addField('enable_email', 'select', array(
            'label' => Mage::helper('rewardpointsevent')->__('Enable notification email'),
            'name' => 'enable_email',
            'values' => Mage::getModel('adminhtml/system_config_source_yesno')->toOptionArray(),
            'onchange' => 'enableEmail(this)',
        ));
        $fieldset->addField('email_template_id', 'select', array(
            'label' => Mage::helper('rewardpointsevent')->__('Email template'),
            'name' => 'email_template_id',
            'values' => Mage::getModel('adminhtml/system_config_source_email_template')->toOptionArray(),
        ));
        $dateFormatIso = Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT);
        $fieldset->addField('status', 'select', array(
            'label' => Mage::helper('rewardpointsevent')->__('Status'),
            'name' => 'status',
            'values' => Mage::getSingleton('rewardpointsevent/status')->getOptionHash(),
        ));
        $fieldset->addField('repeat_type', 'select', array(
            'name' => 'repeat_type',
            'label' => Mage::helper('rewardpointsevent')->__('Event repeated'),
            'required' => true,
            'values' => Mage::getSingleton('rewardpointsevent/repeattype')->toOptionArray(),
            'onchange' => 'changeRepeat(this)',
        ));

        foreach (Mage::getModel('rewardpointsevent/date')->getMonthArray() as $key => $value) {
            $monthbegin = '';
            if (in_array('month_from', $data) && $key == $data['month_from']) {
                $month_from_selected = 'selected';
            }
            else
                $month_from_selected = '';
            $monthbegin.='<option value="' . $key . '"  ' . $month_from_selected . '>' . $value . '</option>';
        }
        foreach (Mage::getModel('rewardpointsevent/date')->getDayArray() as $key => $value) {
            $daymbegin = '';
            if (in_array('daym_from', $data) && $key == $data['daym_from']) {
                $day_from_selected = 'selected';
            }
            else
                $day_from_selected = '';
            $daymbegin.='<option value="' . $key . '" ' . $day_from_selected . '>' . $value . '</option>';
        }
        foreach (Mage::getModel('rewardpointsevent/date')->getMonthArray() as $key => $value) {
            $monthend = '';
            if (in_array('month_to', $data) && $key == $data['month_to']) {
                $month_to_selected = 'selected';
            }
            else
                $month_to_selected = '';
            $monthend.='<option value="' . $key . '" ' . $month_to_selected . '>' . $value . '</option>';
        }
        foreach (Mage::getModel('rewardpointsevent/date')->getDayArray() as $key => $value) {
            $daymend = '';
            if (in_array('daym_to', $data) && $key == $data['daym_to']) {
                $day_to_selected = 'selected';
            }
            else
                $day_to_selected = '';
            $daymend.='<option value="' . $key . '" ' . $day_to_selected . '>' . $value . '</option>';
        }
        $fieldset->addField('year_from', 'note', array(
            'label' => Mage::helper('rewardpointsevent')->__('Effective from'),
            'text' => '<select name="month_from" style="width:100px" id="month_from">
                           ' . sprintf($monthbegin) . '
                       </select>
                       <select name="daym_from" style="width:50px;margin-left:5px" id="daym_from" >
                            ' . sprintf($daymbegin) . '
                       </select>',
                )
        );

        $fieldset->addField('year_to', 'note', array(
            'label' => Mage::helper('rewardpointsevent')->__('Effective to'),
            'text' => '<select name="month_to" style="width:100px" id="month_to">
                            ' . sprintf($monthend) . '
                       </select>
                       <select name="daym_to" style="width:50px;margin-left:5px" id="daym_to">
                            ' . sprintf($daymend) . '
                       </select>',
            'after_element_html' => '
          
            <div class="validation-advice" id="validate_year_to" style="display:none;" >' . $this->helper('rewardpointsevent')->__("End date must be later than Start date!") . '</div>
            ',
                )
        );
        $fieldset->addField('day_from', 'select', array(
            'name' => 'day_from',
            'label' => Mage::helper('rewardpointsevent')->__('Effective from'),
//            'required' => true,
            'values' => Mage::getSingleton('rewardpointsevent/date')->getDayOptionHash(),
            'note' => Mage::helper('rewardpointsevent')->__('Select the start day'),
        ));
        $fieldset->addField('day_to', 'select', array(
            'name' => 'day_to',
            'label' => Mage::helper('rewardpointsevent')->__('Effective to'),
//            'required' => true,
            'values' => Mage::getSingleton('rewardpointsevent/date')->getDayOptionHash(),
            'note' => Mage::helper('rewardpointsevent')->__('Select the end day'),
            'after_element_html' => '
          
            <div class="validation-advice" id="validate_day_to" style="display:none;" >' . $this->helper('rewardpointsevent')->__("End day must be later than Start day") . '</div>
            ',
        ));
        $fieldset->addField('week_from', 'select', array(
            'name' => 'week_from',
            'label' => Mage::helper('rewardpointsevent')->__('Effective from'),
//            'required' => true,
            'values' => Mage::getSingleton('rewardpointsevent/date')->getWeekOptionHash(),
            'note' => Mage::helper('rewardpointsevent')->__('Select the start day'),
        ));
        $fieldset->addField('week_to', 'select', array(
            'name' => 'week_to',
            'label' => Mage::helper('rewardpointsevent')->__('Effective to'),
//            'required' => true,
            'values' => Mage::getSingleton('rewardpointsevent/date')->getWeekOptionHash(),
            'note' => Mage::helper('rewardpointsevent')->__('Select the end day'),
//            'after_element_html' => '          
//            <div class="validation-advice" id="validate_week_to" style="display:none;" >' . $this->helper('rewardpointsevent')->__("Week end must be greater week start!") . '</div>
//            ',
        ));
        $fieldset->addField('apply_from', 'date', array(
            'name' => 'apply_from',
            'label' => Mage::helper('rewardpointsevent')->__('Effective from'),
            'format' => $dateFormatIso,
            'image' => $this->getSkinUrl('images/grid-cal.gif'),
            'input_format' => Varien_Date::DATE_INTERNAL_FORMAT,
            'required' => true,
        ));
        $dateFormatIso = Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT);
        $fieldset->addField('apply_to', 'date', array(
            'name' => 'apply_to',
            'label' => Mage::helper('rewardpointsevent')->__('Effective to'),
            'format' => $dateFormatIso,
            'image' => $this->getSkinUrl('images/grid-cal.gif'),
            'input_format' => Varien_Date::DATE_INTERNAL_FORMAT,
            'note' => Mage::helper('rewardpointsevent')->__('If empty, the event will be effective for 1 day.')
//            'required' => true,
        ));



        $customerGroups = Mage::getResourceModel('customer/group_collection')->load()->toOptionArray();
        $found = false;

        foreach ($customerGroups as $group) {
            if ($group['value'] == 0) {
                $found = true;
            }
        }
        if (!$found) {
            array_unshift($customerGroups, array(
                'value' => 0,
                'label' => Mage::helper('rewardpointsevent')->__('NOT LOGGED IN'))
            );
        }


        $fieldset->addField('customer_apply', 'select', array(
            'name' => 'customer_apply',
            'label' => Mage::helper('rewardpointsevent')->__('Application scope'),
            'required' => true,
            'values' => Mage::getSingleton('rewardpointsevent/scope')->toOptionArray(),
            'onchange' => 'changeScope(this)',
            'note' => 'Choose eligible Customers for the event. If you choose the Customer Conditions option, a new tab named Customers Conditions will appear on the left navigation for you to configure.',
        ));
        $fieldset->addField('filename', 'file', array(
            'label' => Mage::helper('rewardpointsevent')->__('Input CSV'),
            'required' => false,
            'name' => 'filename',
            'note' => '<a href="' .
            $this->getUrl('*/*/downloadSample') .
            '" title="' .
            Mage::helper('rewardpointsevent')->__('Download sample CSV file') .
            '">Download CSV Template File</a>'
        ));
        if (isset($data['file_name']) && $data['file_name']) {
            $dir_csv = Mage::getBaseDir('var') . DS . 'tmp' . DS . $data['file_name'];
            if (file_exists($dir_csv)) {
                $fieldset->addField('current_csv', 'note', array(
                    'label' => Mage::helper('rewardpointsevent')->__('Current CSV file'),
                    'text' => '<a href="' .
                    $this->getUrl('*/*/downloadCurrent/name/' . $data['file_name']) .
                    '" title="' .
                    $data['file_name'] .
                    '">' . $data['file_name'] . '</a>',
                        )
                );
            }
        }

        if (!in_array('status', $data)) {
            $data['status'] = 2;
        }
        $fieldset->addField('file_name', 'hidden', array('name' => 'file_name'));

//        $fieldset->addField('sample', 'note', array(
//            'label' => Mage::helper('rewardpointsevent')->__('Download Sample CSV File'),
//            'text' => '<a href="' .
//            $this->getUrl('*/*/downloadSample') .
//            '" title="' .
//            Mage::helper('rewardpointsevent')->__('Download Sample CSV File') .
//            '">import_customer_sample.csv</a>'
//        ));
        $fieldset->addField('allow_create', 'select', array(
            'name' => 'allow_create',
            'label' => Mage::helper('rewardpointsevent')->__('Allow creating Customers outside the System'),
//            'required' => true,
            'values' => Mage::getModel('adminhtml/system_config_source_yesno')->toOptionArray(),
//            'onchange' => 'showInputPass(this)',
        ));
        if (!Mage::app()->isSingleStoreMode()) {
            $fieldset->addField('website_ids', 'multiselect', array(
                'name' => 'website_ids[]',
                'label' => Mage::helper('rewardpointsevent')->__('Websites'),
                'title' => Mage::helper('rewardpointsevent')->__('Websites'),
                'required' => true,
                'values' => Mage::getSingleton('adminhtml/system_config_source_website')->toOptionArray(),
            ));
        } else {
            $fieldset->addField('website_ids', 'hidden', array(
                'name' => 'website_ids[]',
                'value' => Mage::app()->getStore(true)->getWebsiteId()
            ));
            $data['website_ids'] = Mage::app()->getStore(true)->getWebsiteId();
        }
        $fieldset->addField('customer_group_ids', 'multiselect', array(
            'name' => 'customer_group_ids',
            'label' => Mage::helper('rewardpointsevent')->__('Customer Groups'),
            'values' => Mage::getResourceModel('customer/group_collection')
                    ->addFieldToFilter('customer_group_id', array('gt' => 0))
                    ->load()
                    ->toOptionArray(),
            'required' => true,
        ));

//        if (isset($data['file_name']) && $data['file_name']) {
//            $dir_csv = Mage::getBaseDir('var') . DS . 'tmp' . DS . $data['file_name'];
//            if (file_exists($dir_csv)) {
//                $data['filename'] = Mage::getBaseUrl() . 'var/tmp/' . $data['file_name'];;
//            }
//            else
//                $data['filename'] = '';
//        }

        $fieldset->addField('apply_success', 'note', array(
            'label' => Mage::helper('rewardpointsevent')->__('Event applied for '),
            'text' => sprintf('<strong>%s</strong>', in_array('apply_success', $data) ? $data['apply_success'] : 0),
            'note' => Mage::helper('rewardpointsevent')->__('(times)'),
                )
        );
//        $fieldset->addField('content', 'editor', array(
//            'name' => 'content',
//            'label' => Mage::helper('rewardpointsevent')->__('Content'),
//            'title' => Mage::helper('rewardpointsevent')->__('Content'),
//            'style' => 'width:700px; height:500px;',
//            'wysiwyg' => false,
//            'required' => true,
//        ));

        $form->setValues($data);
        return parent::_prepareForm();
    }

}