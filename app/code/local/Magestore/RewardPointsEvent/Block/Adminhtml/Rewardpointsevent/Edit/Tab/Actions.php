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
 * RewardPointsEvent Edit Form Content Tab Block
 * 
 * @category    Magestore
 * @package     Magestore_RewardPointsEvent
 * @author      Magestore Developer
 */
class Magestore_RewardpointsEvent_Block_Adminhtml_Rewardpointsevent_Edit_Tab_Actions extends Mage_Adminhtml_Block_Widget_Form {

    /**
     * prepare form action tab
     * @return type Form
     */
    protected function _prepareForm() {
        if (Mage::getSingleton('adminhtml/session')->getFormData()) {
            $data = Mage::getSingleton('adminhtml/session')->getFormData();
            $model = Mage::getModel('rewardpointsevent/rewardpointsevent')
                    ->load($data['event_id'])
                    ->setData($data);
            Mage::getSingleton('adminhtml/session')->setFormData(null);
        } elseif (Mage::registry('rewardpointsevent_data')) {
            $model = Mage::registry('rewardpointsevent_data');
            $data = Mage::registry('rewardpointsevent_data')->getData();
        }

        $form = new Varien_Data_Form();
        $form->setHtmlIdPrefix('event_');
        $this->setForm($form);
        $fieldset = $form->addFieldset('gift_fieldset', array('legend' => Mage::helper('rewardpointsevent')->__('Gift Points')));
        
//        $fieldset->addField('repeat_time', 'text', array(
//            'label' => Mage::helper('rewardpointsevent')->__('Time loop'),
//            'title' => Mage::helper('rewardpointsevent')->__('Time loop'),
//            'name' => 'repeat_time',
//            'class' => 'validate-number',
////            'required' => true,
////            'note' => Mage::helper('rewardpointsevent')->__('day(s). If empty or zero, there is no limitation.')
//        ));
        $fieldset->addField('point_amount', 'text', array(
            'label' => Mage::helper('rewardpointsevent')->__('Points Amount'),
            'title' => Mage::helper('rewardpointsevent')->__('Points Amount'),
            'name' => 'point_amount',
            'class' => 'validate-number',
            'required' => true,
        ));
        $fieldset->addField('expire_day', 'text', array(
            'label' => Mage::helper('rewardpointsevent')->__('Points expire after'),
            'title' => Mage::helper('rewardpointsevent')->__('Points expire after'),
            'name' => 'expire_day',
            'note' => Mage::helper('rewardpointsevent')->__('day(s). If empty or zero, there is no limitation.')
        ));
        $form->setValues($data);
        return parent::_prepareForm();
    }

}
