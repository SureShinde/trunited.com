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
class AW_Eventdiscount_Block_Adminhtml_Timer_Edit_Tab_Rules extends Mage_Adminhtml_Block_Widget_Form
    implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
    }

    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $form->setHtmlIdPrefix('rule_');
        $this->setForm($form);

        $_fieldset = $form->addFieldset('timer_form', array('legend' => $this->__('Rules')));
        $_data = Mage::registry('timer_data');

        Mage::getSingleton('adminhtml/session')->setTimerData(null);

        $customerGroups = AW_Eventdiscount_Helper_Data::customerGroupsToArray();

        $_fieldset->addField('customer_group_ids', 'multiselect', array(
            'name' => 'customer_group_ids[]',
            'label' => $this->__('Customer Groups'),
            'title' => $this->__('Customer Groups'),
            'required' => true,
            'values' => $customerGroups,
        ));

        $_fieldset->addField('event', 'select', array(
            'name' => 'event',
            'label' => $this->__('Activation Event'),
            'title' => $this->__('Activation Event'),
            'values' => Mage::getModel('aweventdiscount/event')->eventsToArray(),
            'onchange' => 'checkEventField(this)'
        ));

        $_fieldset->addField('point_type', 'select', array(
            'label' => $this->__('Award Point Type '),
            'required' => false,
            'name' => 'point_type',
            'values' => array(
                1 => $this->__('Fixed amount discount'),
                2 => $this->__('Percent of product point discount'),
            ),
            'disabled' => false,
            'readonly' => false,
        ));

        $_fieldset->addField('point_amount', 'text', array(
            'label' => $this->__('Award Point Amount'),
            'required' => false,
            'name' => 'point_amount',
        ));

        $renderer = Mage::getBlockSingleton('adminhtml/widget_form_renderer_fieldset')
            ->setTemplate('promo/fieldset.phtml')
            ->setNewChildUrl($this->getUrl('*/aweventdiscount_timer/newConditionHtml/form/rule_conditions_fieldset'));

        $conditionsFieldset = $form->addFieldset('conditions_fieldset', array(
            'legend' => $this->__('Conditions')
        ))->setRenderer($renderer);

        $conditionsFieldset->addField('conditions', 'text', array(
            'name' => 'conditions',
            'label' => $this->__('Conditions'),
            'title' => $this->__('Conditions'),
            'required' => false,
        ))->setRule($_data)->setRenderer(Mage::getBlockSingleton('rule/conditions'));

        if ($actionValues = $_data->getData('action_values')) {
            if (is_array($actionValues)) {
                $_data->setData('action', $actionValues[0]['action']);
                $_data->setData('type', $actionValues[0]['type']);
            }
        }

        if ($_data->getData('status') === null)
            $_data->setData('status', 1);

        $rendererGiftCard = new  AW_Eventdiscount_Block_Adminhtml_Renderer_Giftcard_Renderer();
        $giftCardFieldset = $form->addFieldset('giftcard_fieldset', array(
            'legend' => $this->__('Reward Trunited Gift Card')
        ));
        $giftCardFieldset->addField('giftcard_type', 'text', array('name' => 'giftcard_type'))->setRenderer($rendererGiftCard);

        $rendererActions = new  AW_Eventdiscount_Block_Adminhtml_Renderer_Actions_Renderer();
        $actionsFieldset = $form->addFieldset('actions_fieldset', array(
            'legend' => $this->__('Action')
        ));
        $actionsFieldset->addField('type', 'text', array('name' => 'type'))->setRenderer($rendererActions);


        $form->setValues($_data->getData());
        return parent::_prepareForm();
    }

    public function getTabLabel()
    {
        return $this->__('Conditions');
    }

    public function getTabTitle()
    {
        return $this->__('Conditions Information');
    }

    public function canShowTab()
    {
        return true;
    }

    public function isHidden()
    {
        return false;
    }
}