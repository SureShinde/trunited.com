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
 * Rewardpointsevent Edit Block
 * 
 * @category     Magestore
 * @package     Magestore_RewardPointsEvent
 * @author      Magestore Developer
 */
class Magestore_RewardPointsEvent_Block_Adminhtml_Rewardpointsevent_Edit extends Mage_Adminhtml_Block_Widget_Form_Container {

    public function __construct() {
        parent::__construct();

        $this->_objectId = 'id';
        $this->_blockGroup = 'rewardpointsevent';
        $this->_controller = 'adminhtml_rewardpointsevent';

        $this->_updateButton('save', 'label', Mage::helper('rewardpointsevent')->__('Save Event'));
        $this->_updateButton('delete', 'label', Mage::helper('rewardpointsevent')->__('Delete Event'));
        $this->_removeButton('save');
        $this->_addButton('save', array(
                'label' => Mage::helper('adminhtml')->__('Save'),
                'onclick' => 'saveEvent()',
                'class' => 'save',
                    ), -10);
        $this->_addButton('saveandcontinue', array(
            'label' => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick' => 'saveAndContinueEdit()',
            'class' => 'save',
                ), -100);

        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('rewardpointsevent_content') == null)
                    tinyMCE.execCommand('mceAddControl', false, 'rewardpointsevent_content');
                else
                    tinyMCE.execCommand('mceRemoveControl', false, 'rewardpointsevent_content');
            }

            function saveAndContinueEdit(){
                if(validateInput()) 
                editForm.submit($('edit_form').action+'back/edit/');
            }
            function changeScope(el){
                    if(parseInt(el.value)==' " . Magestore_RewardPointsEvent_Model_Scope::SCOPE_CUSTOMER . "'){
                        $('rewardpointsevent_tabs_conditions_section').up('li').show();
                    }
                    else {
                         $('rewardpointsevent_tabs_conditions_section').up('li').hide();
                       
                    }
                    if (parseInt(el.value) == '" . Magestore_RewardPointsEvent_Model_Scope::SCOPE_GROUPS . "'){
                        $('customer_group_ids').up('tr').show();
                        $('website_ids').up('tr').show();
                        $('website_ids').disabled=false;
                        $('customer_group_ids').disabled=false;
                    } else {
                        $('website_ids').up('tr').hide();
                        $('customer_group_ids').up('tr').hide();
                        $('customer_group_ids').disabled=true;
                        $('website_ids').disabled=true;
                    }
                    if(parseInt(el.value)=='" . Magestore_RewardPointsEvent_Model_Scope::SCOPE_CSV . "'){
                         $('filename').up('tr').show();
//                       $('sample').up('tr').show();
                         $('allow_create').up('tr').show();
                         if($('current_csv')){
                         $('current_csv').up('tr').show();
                         }
                         
                    }
                    else 
                    {
                    $('filename').up('tr').hide();
//                    $('sample').up('tr').hide();
                    $('allow_create').up('tr').hide();
                    if($('current_csv')){
                         $('current_csv').up('tr').hide();
                         }
                    }
		}
           function enableEmail(el){
                if(parseInt(el.value)==1)$('email_template_id').up('tr').show();
                else $('email_template_id').up('tr').hide();
           }
           function changeRepeat(el){
                if(parseInt(el.value)==0)
                    {
                    $('apply_from').disabled=false;
                    $('apply_from').up('tr').show();
                    $('apply_to').up('tr').show();
                    }
                else {
                     $('apply_from').disabled=true;
                     $('apply_from').up('tr').hide();
                     $('apply_to').up('tr').hide();
                }
                if(parseInt(el.value)==1)
                    {
                    $('year_from').up('tr').show();
                    $('year_to').up('tr').show();
                    }
                else {
                     $('year_from').up('tr').hide();
                     $('year_to').up('tr').hide();
                }
                if(parseInt(el.value)==2)
                    {
                    $('day_from').up('tr').show();
                    $('day_to').up('tr').show();
                    }
                else {
                     $('day_from').up('tr').hide();
                     $('day_to').up('tr').hide();
                }
                if(parseInt(el.value)==3)
                    {
                    $('week_from').up('tr').show();
                    $('week_to').up('tr').show();
                    }
                else {
                     $('week_from').up('tr').hide();
                     $('week_to').up('tr').hide();
                }
                return true;
           }
           changeRepeat($('repeat_type'));
           changeScope($('customer_apply'));
           enableEmail($('enable_email'));
           function validateInput(){
           if(parseInt($('repeat_type').value)==1){
                    if(parseInt($('month_from').value)>parseInt($('month_to').value)){
                    $('validate_year_to').show();
                    return false;
                   }
                    if(parseInt($('month_from').value)==parseInt($('month_to').value)&&parseInt($('daym_from').value)>parseInt($('daym_to').value)){
                    $('validate_year_to').show();
                    return false;
                   } 
                  }
            else $('validate_year_to').hide();
            if(parseInt($('repeat_type').value)==2){
                    if(parseInt($('day_from').value)>parseInt($('day_to').value)){
                    $('validate_day_to').show();
                    return false;
                   }
                  }
            else $('validate_day_to').hide();
//            if(parseInt($('repeat_type').value)==2){
//                    if(parseInt($('week_from').value)>parseInt($('week_to').value)){
//                    $('validate_week_to').show();
//                    return false;
//                   }
//                  }
//            else $('validate_week_to').hide();
                  return true;
           }
           function saveEvent(){
                  if(validateInput())
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
        if (Mage::registry('rewardpointsevent_data') && Mage::registry('rewardpointsevent_data')->getId()
        ) {
            return Mage::helper('rewardpointsevent')->__("Edit Event '%s'", $this->htmlEscape(Mage::registry('rewardpointsevent_data')->getTitle())
            );
        }
        return Mage::helper('rewardpointsevent')->__('Add New Event');
    }

}