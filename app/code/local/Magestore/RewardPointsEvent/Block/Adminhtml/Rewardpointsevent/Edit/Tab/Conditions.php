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
class Magestore_RewardpointsEvent_Block_Adminhtml_Rewardpointsevent_Edit_Tab_Conditions extends Mage_Adminhtml_Block_Widget_Form {

    /**
     * prepare tab form's condion
     *
     * @return Magestore_RewardPointsReferFriends_Block_Adminhtml_Rewardpointsreferfriends_Edit_Tab_Form
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

        $renderer = Mage::getBlockSingleton('adminhtml/widget_form_renderer_fieldset')
                ->setTemplate('promo/fieldset.phtml')
                ->setNewChildUrl($this->getUrl('adminhtml/promo_quote/newConditionHtml/form/event_conditions_fieldset'));

        $fieldset = $form->addFieldset('conditions_fieldset', array('legend' => Mage::helper('rewardpointsevent')->__('Apply the rule only if the following conditions are met (leave blank for all customers)')))->setRenderer($renderer);

        $fieldset->addField('conditions', 'text', array(
            'name' => 'conditions',
            'label' => Mage::helper('rewardpointsevent')->__('Conditions'),
            'title' => Mage::helper('rewardpointsevent')->__('Conditions'),
            'required' => true,
        ))->setRule($model)->setRenderer(Mage::getBlockSingleton('rule/conditions'));

        $form->setValues($data);
        $this->setForm($form);
        return parent::_prepareForm();
    }

}