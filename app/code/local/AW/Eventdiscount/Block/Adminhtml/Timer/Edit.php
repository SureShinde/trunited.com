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
class AW_Eventdiscount_Block_Adminhtml_Timer_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
        $this->_objectId = 'timerid';
        $this->_blockGroup = 'eventdiscount';
        $this->_controller = 'adminhtml_timer';
        $this->_addButton('saveandcontinue', array(
            'label' => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick' => 'saveAndContinueEdit(\'' . $this->_getSaveAndContinueUrl() . '\')',
            'class' => 'save',
        ), -100);
        $this->_formScripts[] = "
            window.onload = function(){
            var ruleEventValue = $('rule_event').options[$('rule_event').selectedIndex].value;
            if(ruleEventValue == '" . AW_Eventdiscount_Model_Event::ORDER . "' || ruleEventValue == '" . AW_Eventdiscount_Model_Event::CARTUPDATE . "'
            ){
                $('rule_conditions_fieldset').up().show();
                $('rule_giftcard_fieldset').previous().hide();
                $('rule_giftcard_fieldset').hide();
                $('rule_cms_page').up().up().hide();
                $('rule_english_cms_page').up().up().hide();
                    $('rule_spanish_cms_page').up().up().hide();
            } else if(ruleEventValue == '" . AW_Eventdiscount_Model_Event::PROMOTION . "'){
                $('rule_conditions_fieldset').up().hide();
                $('rule_giftcard_fieldset').previous().show();
                $('rule_giftcard_fieldset').show();
                $('rule_cms_page').up().up().hide();
                $('rule_english_cms_page').up().up().show();
                    $('rule_spanish_cms_page').up().up().show();
            } else if(ruleEventValue == '" . AW_Eventdiscount_Model_Event::LOGIN . "'){
                $('rule_conditions_fieldset').up().hide();
                $('rule_giftcard_fieldset').previous().show();
                $('rule_giftcard_fieldset').show();
                $('rule_cms_page').up().up().show();
                $('rule_english_cms_page').up().up().hide();
                    $('rule_spanish_cms_page').up().up().hide();
            } else {
                $('rule_conditions_fieldset').up().hide();
                $('rule_giftcard_fieldset').previous().hide();
                $('rule_giftcard_fieldset').hide();
                $('rule_cms_page').up().up().hide();
                $('rule_english_cms_page').up().up().hide();
                    $('rule_spanish_cms_page').up().up().hide();
            }

            new Control.ColorPicker('rule_color', {
                IMAGE_BASE : '" . $this->getSkinUrl('aw_eventdiscount/images/') . "',
             onUpdate :function(value){
             if($('preview_font').checked){
             $$('.aw_eventdiscount_timer')[0].style.color='#'+value}
             }
             });
            $('rule_color').onblur = function(){ $('colorpicker-okbutton').click();
            if($('preview_font').checked){
             $$('.aw_eventdiscount_timer')[0].style.color='#'+$('rule_color').value;
            }
                };
            };
            function saveAndContinueEdit(url) {
                editForm.submit(url.replace(/{{tab_id}}/ig,timer_tabsJsTabs.activeTab.id));
            }
            function checkEventField(elem) {
                if(elem.options[elem.selectedIndex].value == '" . AW_Eventdiscount_Model_Event::ORDER . "'
                    || elem.options[elem.selectedIndex].value == '" . AW_Eventdiscount_Model_Event::CARTUPDATE . "'
                ){
                    $('rule_conditions_fieldset').up().show();
                    $('rule_giftcard_fieldset').previous().hide();
                    $('rule_giftcard_fieldset').hide();
                    $('rule_cms_page').up().up().hide();
                    $('rule_english_cms_page').up().up().hide();
                    $('rule_spanish_cms_page').up().up().hide();
                } else if(elem.options[elem.selectedIndex].value == '" . AW_Eventdiscount_Model_Event::PROMOTION . "'){
                    $('rule_conditions_fieldset').up().hide();
                    $('rule_giftcard_fieldset').previous().show();
                    $('rule_giftcard_fieldset').show();
                    $('rule_cms_page').up().up().hide();
                    $('rule_english_cms_page').up().up().show();
                    $('rule_spanish_cms_page').up().up().show();
                } else if(elem.options[elem.selectedIndex].value == '" . AW_Eventdiscount_Model_Event::LOGIN . "'){
                    $('rule_conditions_fieldset').up().hide();
                    $('rule_giftcard_fieldset').previous().show();
                    $('rule_giftcard_fieldset').show();
                    $('rule_cms_page').up().up().show();
                    $('rule_english_cms_page').up().up().hide();
                    $('rule_spanish_cms_page').up().up().hide();
                } else {
                    $('rule_conditions_fieldset').up().hide();
                    $('rule_giftcard_fieldset').previous().hide();
                    $('rule_giftcard_fieldset').hide();
                    $('rule_cms_page').up().up().hide();
                    $('rule_english_cms_page').up().up().hide();
                    $('rule_spanish_cms_page').up().up().hide();
                }
            }

            function switchFonts(input) {
                if(input.checked){
                    $$('.aw_eventdiscount_timer')[0].style.color='#'+$('rule_color').value;
                }else {
                    $$('.aw_eventdiscount_timer')[0].style.color='';
                }
            }
        ";
    }

    protected function _getSaveAndContinueUrl()
    {
        return $this->getUrl('*/*/save', array(
            '_current' => true,
            'back' => 'edit',
            'tab' => '{{tab_id}}'
        ));
    }

    public function getHeaderText()
    {
        if (is_null($id = $this->getRequest()->getParam('id'))) {
            return $this->__('New Timer');
        }
        $data = Mage::getModel('aweventdiscount/timer')->load($id);
        return $this->__("Edit Timer '%s'", $data->getTimerName());
    }

    public function getBackUrl()
    {
        if ($this->getRequest()->getParam('back') == 'stat')
            return $this->getUrl('*/aweventdiscount_stat/index/');
        return parent::getBackUrl();
    }
}