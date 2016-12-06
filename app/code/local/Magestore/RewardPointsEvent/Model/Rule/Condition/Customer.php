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
 * RewardpointsEvent Model
 * 
 * @category    Magestore
 * @package     Magestore_RewardPointsEvent
 * @author      Magestore Developer
 * 
 */
class Magestore_RewardPointsEvent_Model_Rule_Condition_Customer extends Mage_Rule_Model_Condition_Abstract {

    public function loadAttributeOptions() {
        $this->setAttributeOption(array(
            'store_id' => Mage::helper('rewardpointsevent')->__('Create In'),
            'website_id' => Mage::helper('rewardpointsevent')->__('Associate to Website'),
            'group_id' => Mage::helper('rewardpointsevent')->__('Customer Group'),
            'created_at' => Mage::helper('rewardpointsevent')->__('Created At'),
            'email' => Mage::helper('rewardpointsevent')->__('Email'),
            'gender' => Mage::helper('rewardpointsevent')->__('Gender'),
            'prefix' => Mage::helper('rewardpointsevent')->__('Prefix'),
            'suffix' => Mage::helper('rewardpointsevent')->__('Suffix'),
            'dob' => Mage::helper('rewardpointsevent')->__('Date Of Birth'),
        ));
        return $this;
    }

    public function getAttributeElement() {
        $attributeElement = parent::getAttributeElement();
        $attributeElement->setShowAsText(true);
        return $attributeElement;
    }

    public function getInputType() {
        switch ($this->getAttribute()) {
            case 'store_id':
            case 'website_id':
            case 'gender':
                return 'select';
            case 'group_id':
                return 'multiselect';
            case 'dob':
            case 'created_at':
                return 'date';
        }
        return 'string';
    }

    public function getValueElementType() {
        switch ($this->getAttribute()) {
            case 'gender':
            case 'website_id':
            case 'store_id':
                return 'select';
            case 'group_id':
                return 'multiselect';
            case 'dob':
            case 'created_at':
                return 'date';
            default:
                return 'text';
        }
    }

    public function getValueSelectOptions() {
        if (!$this->hasData('value_select_options')) {
            $options = array();
            switch ($this->getAttribute()) {
                case 'group_id':
                    $options = Mage::getResourceModel('customer/group_collection')
                            ->addFieldToFilter('customer_group_id', array('gt' => 0))
                            ->load()
                            ->toOptionArray();
                    break;
                case 'website_id':
                    $options = Mage::getResourceSingleton('customer/customer')->getAttribute('website_id')->getSource()->getAllOptions();
                    break;
                case 'store_id':
                    $options = Mage::getSingleton('rewardpointsevent/store')->getOptionHash();
                    break;
                case 'gender':
                    $options = Mage::getResourceSingleton('customer/customer')->getAttribute('gender')->getSource()->getAllOptions();
            }
            $this->setData('value_select_options', $options);
        }
        return $this->getData('value_select_options');
    }

    public function getValueAfterElementHtml() {
        $html = '';
        if (!empty($image)) {
            $html = '<a href="javascript:void(0)" class="rule-chooser-trigger"><img src="' . $image . '" alt="" class="v-middle rule-chooser-trigger" title="' . Mage::helper('rule')->__('Open Chooser') . '" /></a>';
        }
        return $html;
    }

    /**
     * Retrieve value element chooser URL
     *
     * @return string
     */
    public function getValueElementChooserUrl() {
        $url = false;
        return $url !== false ? Mage::helper('adminhtml')->getUrl($url) : '';
    }

    /**
     * Retrieve value element
     *
     * @return Varien_Data_Form_Element_Abstract
     */
    public function getValueElement() {
        $element = parent::getValueElement();
        switch ($this->getAttribute()) {
            case 'dob':
            case 'created_at':
                $element->setImage(Mage::getDesign()->getSkinUrl('images/grid-cal.gif'));
                break;
        }
        return $element;
    }

    /**
     * Retrieve Explicit Apply
     *
     * @return bool
     */
    public function getExplicitApply() {
        switch ($this->getAttribute()) {
            case 'dob':
            case 'created_at':
                return true;
        }
        return false;
    }

    public function validate(Varien_Object $object) {
        $attrCode = $this->getAttribute();
        $attr = $object->getResource()->getAttribute($attrCode);
        if ($attr && $attr->getBackendType() == 'datetime') {
            $this->setValue(strtotime($this->getValue()));
            $value = strtotime($object->getData($attrCode));
            return $this->validateAttribute($value);
        }

        if ($attr && $attr->getFrontendInput() == 'multiselect') {
            $value = $object->getData($attrCode);
            $value = strlen($value) ? explode(',', $value) : array();
            return $this->validateAttribute($value);
        }

        return parent::validate($object);
    }

}
