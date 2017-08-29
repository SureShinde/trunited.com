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
 * @package     Magestore_RewardPointsTransfer
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * Rewardpointstransfer Edit Block
 * 
 * @category     Magestore
 * @package     Magestore_RewardPointsTransfer
 * @author      Magestore Developer
 */
class Magestore_RewardPointsTransfer_Block_Adminhtml_Rewardpointstransfer_Edit extends Mage_Adminhtml_Block_Widget_Form_Container {

    public function __construct() {
        parent::__construct();

        $this->_objectId = 'id';
        $this->_blockGroup = 'rewardpointstransfer';
        $this->_controller = 'adminhtml_rewardpointstransfer';
        $this->_removeButton('delete');
        $this->_removeButton('save');
        $transfer = Mage::registry('rewardpointstransfer_data');
        if ($transfer->getId()) {
            $this->_addButton('createnew', array('label' => Mage::helper('adminhtml')->__('Create A New Transfer'),
                'onclick' => 'window.location=\'' . $this->getUrl('*/*/new', array('back' => $this->_backController)) . '\''), -20);
            $this->_removeButton('save');
            $this->_removeButton('reset');
            if ($transfer->getStatus() <= Magestore_RewardPointsTransfer_Model_Status::STATUS_COMPLETED) { //xuanbinh
                if ($transfer->getStatus() == Magestore_RewardPointsTransfer_Model_Status::STATUS_HOLDING) {
                    $this->_addButton('completetransfer', array('label' => Mage::helper('adminhtml')->__('Complete'),
                        'onclick' => 'window.location=\'' . $this->getUrl('*/*/complete', array(
                            'id' => $transfer->getId())) . '\'',
                        'class' => 'save'
                            ), -100);
                }
                $this->_addButton('canceltransfer', array('label' => Mage::helper('adminhtml')->__('Cancel'),
                    'onclick' => 'window.location=\'' . $this->getUrl('*/*/cancel', array(
                        'id' => $transfer->getId())) . '\'',
                    'class' => 'delete'
                        ), -110);
            }
        } else {
            $this->_addButton('save', array(
                'label' => Mage::helper('adminhtml')->__('Save'),
                'onclick' => 'saveTransfer()',
                'class' => 'save',
                    ), -10);
            $this->_addButton('saveandcontinue', array(
                'label' => Mage::helper('adminhtml')->__('Save And Continue View'),
                'onclick' => 'saveAndContinueEdit()',
                'class' => 'save',
                    ), -10);
        }


        $this->_updateButton('save', 'label', Mage::helper('rewardpointstransfer')->__('Save'));
        $this->_formScripts[] = "
            function revokeTransfer() {
                window.location='" . $this->getUrl('*/*/cancel', array('id' => $transfer->getId())) . "';
            }
            function toggleEditor() {
                if (tinyMCE.getInstanceById('rewardpointstransfer_content') == null)
                    tinyMCE.execCommand('mceAddControl', false, 'rewardpointstransfer_content');
                else
                    tinyMCE.execCommand('mceRemoveControl', false, 'rewardpointstransfer_content');
            }

            function saveAndContinueEdit(){
                    Object.extend(Validation.validate($('sender_email')));
                    Object.extend(Validation.validate($('receiver_email')));
                    Object.extend(Validation.validate($('point_amount')));
                    if(checkPointAmount())
                    editForm.submit($('edit_form').action+'back/edit/');
            
                   
                
                
            }
            function saveTransfer(){
                    Object.extend(Validation.validate($('sender_email')));
                    Object.extend(Validation.validate($('receiver_email')));
                    Object.extend(Validation.validate($('point_amount')));
                  if(checkPointAmount())
                  editForm.submit();    
                    
                        
            }
            
        ";
    }

    /**
     * get text to show in header when edit an item
     *
     * @return string
     */
    public function getHeaderText() {
        if (Mage::registry('rewardpointstransfer_data') && Mage::registry('rewardpointstransfer_data')->getId()
        ) {
            //return Mage::helper('rewardpointstransfer')->__("View Transfer #'%s'", $this->htmlEscape(Mage::registry('rewardpointstransfer_data')->getId()));
        	return Mage::helper('rewardpointstransfer')->__("View Transfer");
        }
        return Mage::helper('rewardpointstransfer')->__('Add New Transfer');
    }

}