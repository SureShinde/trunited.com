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
 * @package     Magestore_RewardPointsCoupon
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * Rewardpointscoupon Edit Form Content Tab Block
 * 
 * @category    Magestore
 * @package     Magestore_RewardPointsCoupon
 * @author      Magestore Developer
 */
class Magestore_RewardPointsCoupon_Block_Adminhtml_Rewardpointscoupon_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form implements Mage_Adminhtml_Block_Widget_Tab_Interface {

    public function getTabLabel() {
        return Mage::helper('rewardpointscoupon')->__('Template');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle() {
        return Mage::helper('rewardpointscoupon')->__('Template');
    }

    /**
     * Returns status flag about this tab can be showed or not
     *
     * @return true
     */
    public function canShowTab() {
        return true;
    }

    /**
     * Returns status flag about this tab hidden or not
     *
     * @return true
     */
    public function isHidden() {
        return false;
    }

    /**
     * prepare tab form's information
     *
     * @return Magestore_RewardPointsCoupon_Block_Adminhtml_Rewardpointscoupon_Edit_Tab_Form
     */
    protected function _prepareForm() {
        $form = new Varien_Data_Form();
        $this->setForm($form);
        $form->setHtmlIdPrefix('template_');
        if (Mage::getSingleton('adminhtml/session')->getRewardPointsCouponData()) {
            $data = Mage::getSingleton('adminhtml/session')->getRewardPointsCouponData();
            Mage::getSingleton('adminhtml/session')->setRewardPointsCouponData(null);
        } elseif (Mage::registry('current_rewardpoints_coupon_template')) {
            $data = Mage::registry('current_rewardpoints_coupon_template')->getData();
        }
        $fieldset = $form->addFieldset('rewardpointscoupon_form', array(
            'legend' => Mage::helper('rewardpointscoupon')->__('Reward Coupon Information')
        ));

        $fieldset->addField('name', 'text', array(
            'label' => Mage::helper('rewardpointscoupon')->__('Title'),
            'class' => 'required-entry',
            'required' => true,
            'name' => 'name',
        ));
        if(!isset($data['status'])){
            $data['status']=1;
        }
        $fieldset->addField('status', 'select', array(
            'label' => Mage::helper('rewardpointscoupon')->__('Status'),
            'title' => Mage::helper('rewardpointscoupon')->__('Status'),
            'name' => 'status',
            'required' => true,
            'options' => array(
                '1' => Mage::helper('rewardpointscoupon')->__('Active'),
                '0' => Mage::helper('rewardpointscoupon')->__('Inactive'),
            ),
            'value'=>$data['status'],
        ));
        if (Mage::app()->isSingleStoreMode()) {
            $websiteId = Mage::app()->getStore(true)->getWebsiteId();
            $fieldset->addField('website_ids', 'hidden', array(
                'name' => 'website_ids[]',
                'value' => $websiteId
            ));
        } else {
            $field = $fieldset->addField('website_ids', 'multiselect', array(
                'name' => 'website_ids[]',
                'label' => Mage::helper('rewardpointscoupon')->__('Websites'),
                'title' => Mage::helper('rewardpointscoupon')->__('Websites'),
                'required' => true,
                'values' => Mage::getSingleton('adminhtml/system_store')->getWebsiteValuesForForm()
            ));
            $renderer = $this->getLayout()->createBlock('adminhtml/store_switcher_form_renderer_fieldset_element');
//            $field->setRenderer($renderer);
        }
        $fieldset->addField('customer_group_ids', 'multiselect', array(
            'name' => 'customer_group_ids[]',
            'label' => Mage::helper('rewardpointscoupon')->__('Customer Groups'),
            'title' => Mage::helper('rewardpointscoupon')->__('Customer Groups'),
            'required' => true,
            'values' => Mage::getResourceModel('customer/group_collection')
			->addFieldToFilter('customer_group_id', array('gt' => 0))->load()->toOptionArray(),
        ));
        $fieldset->addField('coupon_code', 'text', array(
            'label' => Mage::helper('rewardpointscoupon')->__('Reward Code Pattern'),
            'required' => true,
            'name' => 'coupon_code',
        ));
        $autoGenerationCheckbox = $fieldset->addField('use_auto_generation', 'checkbox', array(
            'name' => 'use_auto_generation',
            'label' => Mage::helper('rewardpointscoupon')->__('Use Auto Generation'),
            'note' => Mage::helper('rewardpointscoupon')->__('If you select and save the template, you will be able to generate multiple reward coupon codes.'),
            'onclick' => 'handleCouponsTabContentActivity()',
            'checked' => (int) $data['use_auto_generation'] > 0 ? 'checked' : '',
            'value' => 1,
        ));
        $fieldset->addField('point_balance', 'text', array(
            'label' => Mage::helper('rewardpointscoupon')->__('Point Balance'),
            'name' => 'point_balance',
            'class' => 'validate-zero-or-greater',
        ));

        $autoGenerationCheckbox->setRenderer(
                $this->getLayout()->createBlock('rewardpointscoupon/adminhtml_rewardpointscoupon_edit_tab_main_renderer_checkbox')
        );

        $fieldset->addField('uses_per_coupon', 'text', array(
            'label' => Mage::helper('rewardpointscoupon')->__('Number of uses per code'),
            'name' => 'uses_per_coupon',
            'class' => 'validate-zero-or-greater',
        ));
        $fieldset->addField('uses_per_customer', 'text', array(
            'label' => Mage::helper('rewardpointscoupon')->__('Number of uses per customer'),
            'name' => 'uses_per_customer',
            'class' => 'validate-zero-or-greater',
        ));
        $dateFormatIso = Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT);
        $fieldset->addField('from_date', 'date', array(
            'name' => 'from_date',
            'label' => Mage::helper('rewardpointscoupon')->__('From Date'),
            'title' => Mage::helper('rewardpointscoupon')->__('From Date'),
            'image' => $this->getSkinUrl('images/grid-cal.gif'),
            'input_format' => Varien_Date::DATE_INTERNAL_FORMAT,
            'format' => $dateFormatIso
        ));
        $fieldset->addField('to_date', 'date', array(
            'name' => 'to_date',
            'label' => Mage::helper('rewardpointscoupon')->__('To Date'),
            'title' => Mage::helper('rewardpointscoupon')->__('To Date'),
            'image' => $this->getSkinUrl('images/grid-cal.gif'),
            'input_format' => Varien_Date::DATE_INTERNAL_FORMAT,
            'format' => $dateFormatIso
        ));
        $fieldset->addField('point_expired', 'text', array(
            'name' => 'point_expired',
            'label' => Mage::helper('rewardpointscoupon')->__('Points expire after'),
            'title' => Mage::helper('rewardpointscoupon')->__('Points expire after'),
            'note' => 'day(s). If empty or zero, there is no limitation.',
        ));
        $form->setValues($data);
        return parent::_prepareForm();
    }

}
