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
 * Rewardpointscoupon Edit Block
 * 
 * @category     Magestore
 * @package     Magestore_RewardPointsCoupon
 * @author      Magestore Developer
 */
class Magestore_RewardPointsCoupon_Block_Adminhtml_Rewardpointscoupon_Edit extends Mage_Adminhtml_Block_Widget_Form_Container {

    public function __construct() {
        parent::__construct();
        $this->_objectId = 'id';
        $this->_blockGroup = 'rewardpointscoupon';
        $this->_controller = 'adminhtml_rewardpointscoupon';

        $this->_updateButton('save', 'label', Mage::helper('rewardpointscoupon')->__('Save'));
        $this->_updateButton('delete', 'label', Mage::helper('rewardpointscoupon')->__('Delete'));

        $this->_addButton('saveandcontinue', array(
            'label' => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick' => 'saveAndContinueEdit()',
            'class' => 'save',
                ), -100);
        if (Mage::registry('current_rewardpoints_coupon_template') && Mage::registry('current_rewardpoints_coupon_template')->getId()) {
            $this->_addButton('saveandprint', array(
                'label' => Mage::helper('adminhtml')->__('Print'),
                'onclick' => 'saveAndprint()',
                'class' => 'save',
                    ), -120);
        }

        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('rewardpointscoupon_content') == null)
                    tinyMCE.execCommand('mceAddControl', false, 'rewardpointscoupon_content');
                else
                    tinyMCE.execCommand('mceRemoveControl', false, 'rewardpointscoupon_content');
            }

            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
            function saveAndprint(){
                editForm.submit($('edit_form').action+'print/edit/');
            }
        ";
    }

    /**
     * get text to show in header when edit an item
     *
     * @return string
     */
    public function getHeaderText() {
        if (Mage::registry('current_rewardpoints_coupon_template') && Mage::registry('current_rewardpoints_coupon_template')->getId()
        ) {
            return Mage::helper('rewardpointscoupon')->__("Edit Reward Coupon '%s'", $this->htmlEscape(Mage::registry('current_rewardpoints_coupon_template')->getName())
            );
        }
        return Mage::helper('rewardpointscoupon')->__('Add Reward Coupon');
    }

}
