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
class Magestore_RewardPointsEvent_Model_Rule_Condition_Address extends Mage_Rule_Model_Condition_Abstract {

    public function loadAttributeOptions() {
        $this->setAttributeOption(array(
            'company' => Mage::helper('rewardpointsevent')->__('Company'),
            'city' => Mage::helper('rewardpointsevent')->__('City'),
            'country_id' => Mage::helper('rewardpointsevent')->__('Country'),
            'region_id' => Mage::helper('rewardpointsevent')->__('State/Province'),
            'street' => Mage::helper('rewardpointsevent')->__('Street Address'),
            'fax' => Mage::helper('rewardpointsevent')->__('Fax'),
            'telephone' => Mage::helper('rewardpointsevent')->__('Telephone'),
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
            case 'country_id':
            case 'region_id':
                return 'select';
        }
        return 'string';
    }

    public function getValueElementType() {
        switch ($this->getAttribute()) {
            case 'country_id':
            case 'region_id':
                return 'select';
            default:
                return 'text';
        }
    }

    public function getValueSelectOptions() {
        if (!$this->hasData('value_select_options')) {
            $options = array();
            switch ($this->getAttribute()) {
                case 'country_id':
                    $options = Mage::getResourceSingleton('customer/address')->getAttribute('country_id')->getSource()->getAllOptions();
                    break;
                case 'region_id':
                    $options = Mage::getResourceSingleton('customer/address')->getAttribute('region_id')->getSource()->getAllOptions();
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
//        if (is_object($this->getAttributeObject())) {
        switch ($this->getAttribute()) {
            case 'dob':
            case 'created_at':
                $element->setImage(Mage::getDesign()->getSkinUrl('images/grid-cal.gif'));
                break;
        }
//        }

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
        $customer=$object;
        return parent::validate($customer);
    }

}
