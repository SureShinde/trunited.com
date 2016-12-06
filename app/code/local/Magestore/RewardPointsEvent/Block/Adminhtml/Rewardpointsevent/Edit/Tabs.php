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
 * Rewardpointsevent Edit Tabs Block
 * 
 * @category    Magestore
 * @package     Magestore_RewardPointsEvent
 * @author      Magestore Developer
 */
class Magestore_RewardPointsEvent_Block_Adminhtml_Rewardpointsevent_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs {

    public function __construct() {
        parent::__construct();
        $this->setId('rewardpointsevent_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('rewardpointsevent')->__('Event Information'));
    }

    /**
     * prepare before render block to html
     *
     * @return Magestore_RewardPointsEvent_Block_Adminhtml_Rewardpointsevent_Edit_Tabs
     */
    protected function _beforeToHtml() {
        $this->addTab('form_section', array(
            'label' => Mage::helper('rewardpointsevent')->__('General Information'),
            'title' => Mage::helper('rewardpointsevent')->__('General Information'),
            'content' => $this->getLayout()
                    ->createBlock('rewardpointsevent/adminhtml_rewardpointsevent_edit_tab_form')
                    ->toHtml(),
        ));
        $this->addTab('conditions_section', array(
            'label' => Mage::helper('rewardpointsevent')->__('Customers\' Conditions'),
            'title' => Mage::helper('rewardpointsevent')->__('Customers\' Conditions'),
            'content' => $this->getLayout()->createBlock('rewardpointsevent/adminhtml_rewardpointsevent_edit_tab_conditions')->toHtml(),
        ));

        $this->addTab('actions_section', array(
            'label' => Mage::helper('rewardpointsevent')->__('Actions'),
            'title' => Mage::helper('rewardpointsevent')->__('Actions'),
            'content' => $this->getLayout()->createBlock('rewardpointsevent/adminhtml_rewardpointsevent_edit_tab_actions')->toHtml(),
        ));
        return parent::_beforeToHtml();
    }

}