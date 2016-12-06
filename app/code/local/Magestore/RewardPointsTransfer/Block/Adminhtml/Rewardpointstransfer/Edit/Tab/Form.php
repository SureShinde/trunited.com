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
 * Rewardpointstransfer Edit Form Content Tab Block
 * 
 * @category    Magestore
 * @package     Magestore_RewardPointsTransfer
 * @author      Magestore Developer
 */
class Magestore_RewardPointsTransfer_Block_Adminhtml_Rewardpointstransfer_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form {

    /**
     * prepare tab form's information
     *
     * @return Magestore_RewardPointsTransfer_Block_Adminhtml_Rewardpointstransfer_Edit_Tab_Form
     */
    protected function _prepareForm() {
        $form = new Varien_Data_Form();
        $this->setForm($form);

        if (Mage::getSingleton('adminhtml/session')->getRewardPointsTransferData()) {
            $model = Mage::getSingleton('adminhtml/session')->getRewardPointsTransferData();
            Mage::getSingleton('adminhtml/session')->setRewardPointsTransferData(null);
        } elseif (Mage::registry('rewardpointstransfer_data')) {
            $model = Mage::registry('rewardpointstransfer_data');
        }
        $fieldset = $form->addFieldset('rewardpointstransfer_form', array(
            'legend' => Mage::helper('rewardpointstransfer')->__('Transfer Information')
        ));

        if ($model->getId()) {
            if ($model->getStatus() >= Magestore_RewardPointsTransfer_Model_Status::STATUS_COMPLETED) {
                $fieldset->addField('sender_email', 'note', array(
                    'label' => Mage::helper('rewardpointstransfer')->__('Sender\'s Email'),
                    'text' => sprintf('<a target="_blank" href="%s">%s</a>', $this->getUrl('adminhtml/customer/edit', array('id' => $model->getSenderCustomerId())), $model->getSenderEmail()
                    ),
                ));
                $fieldset->addField('receiver_email', 'note', array(
                    'label' => Mage::helper('rewardpointstransfer')->__('Recipient\'s Email'),
                    'text' => sprintf('<a target="_blank" href="%s">%s</a>', $this->getUrl('adminhtml/customer/edit', array('id' => $model->getReceiverCustomerId())), $model->getReceiverEmail()
                    ),
                ));

                $statusHash = Mage::getModel('rewardpointstransfer/status')->getOptionArray();
                $fieldset->addField('status', 'note', array(
                    'label' => Mage::helper('rewardpoints')->__('Status'),
                    'text' => isset($statusHash[$model->getStatus()]) ? '<strong>' . $statusHash[$model->getStatus()] . '</strong>' : '',
                ));

                $fieldset->addField('point_amount', 'note', array(
                    'label' => Mage::helper('rewardpointstransfer')->__('Point Amount Transferred'),
                    'text' => '<strong>' . Mage::helper('rewardpoints/point')->format(
                            $model->getPointAmount(), $model->getStoreId()
                    ) . '</strong>',
                ));
                $fieldset->addField('title', 'note', array(
                    'label' => Mage::helper('rewardpointstransfer')->__('Comment/Note'),
                    'text' => $model->getExtraContent(),
                ));
                $fieldset->addField('created_time', 'note', array(
                    'label' => Mage::helper('rewardpointstransfer')->__('Created On'),
                    'text' => $this->formatTime($model->getCreatedTime(), Mage_Core_Model_Locale::FORMAT_TYPE_MEDIUM, true
                    ),
                ));

                $fieldset->addField('updated_time', 'note', array(
                    'label' => Mage::helper('rewardpointstransfer')->__('Updated On'),
                    'text' => $this->formatTime($model->getUpdatedTime(), Mage_Core_Model_Locale::FORMAT_TYPE_MEDIUM, true
                    ),
                ));
                $form->setValues($model->getData());
                return parent::_prepareForm();
            } else {
                $fieldset->addField('sender_email', 'note', array(
                    'label' => Mage::helper('rewardpointstransfer')->__('Sender\'s Email'),
                    'text' => sprintf('<a target="_blank" href="%s">%s</a>', $this->getUrl('adminhtml/customer/edit', array('id' => $model->getSenderCustomerId())), $model->getSenderEmail()
                    ),
                ));
                $fieldset->addField('receiver_email', 'note', array(
                    'label' => Mage::helper('rewardpointstransfer')->__('Recipient\'s Email'),
                    'text' => sprintf('<a target="_blank" href="%s">%s</a>', $this->getUrl('adminhtml/customer/edit', array('id' => $model->getReceiverCustomerId())), $model->getReceiverEmail()
                    ),
                ));

                $statusHash = Mage::getModel('rewardpointstransfer/status')->getOptionArray();
                $fieldset->addField('status', 'note', array(
                    'label' => Mage::helper('rewardpoints')->__('Status'),
                    'text' => isset($statusHash[$model->getStatus()]) ? '<strong>' . $statusHash[$model->getStatus()] . '</strong>' : '',
                ));

                $fieldset->addField('point_amount', 'note', array(
                    'label' => Mage::helper('rewardpointstransfer')->__('Points'),
                    'text' => '<strong>' . Mage::helper('rewardpoints/point')->format(
                            $model->getPointAmount(), $model->getStoreId()
                    ) . '</strong>',
                ));
                $fieldset->addField('holding_day', 'note', array(
                    'label' => Mage::helper('rewardpointstransfer')->__('Hold point transfer for'),
                    'text' => '<strong>' . $model->getHoldingDay() . ' day(s)</strong>',
                ));
                $fieldset->addField('title', 'note', array(
                    'label' => Mage::helper('rewardpointstransfer')->__('Comment/Note'),
                    'text' => $model->getExtraContent(),
                ));
                $fieldset->addField('created_time', 'note', array(
                    'label' => Mage::helper('rewardpointstransfer')->__('Created On'),
                    'text' => $this->formatTime($model->getCreatedTime(), Mage_Core_Model_Locale::FORMAT_TYPE_MEDIUM, true
                    ),
                ));

                $fieldset->addField('updated_time', 'note', array(
                    'label' => Mage::helper('rewardpointstransfer')->__('Updated On'),
                    'text' => $this->formatTime($model->getUpdatedTime(), Mage_Core_Model_Locale::FORMAT_TYPE_MEDIUM, true
                    ),
                ));
                $form->setValues($model->getData());
                return parent::_prepareForm();
            }
        }

        $fieldset->addField('sender_email', 'text', array(
            'label' => Mage::helper('rewardpointstransfer')->__('Sender\'s Email'),
            'title' => Mage::helper('rewardpointstransfer')->__('Sender\'s Email'),
            'class' => 'required-entry',
            'required' => true,
            'name' => 'sender_email',
            'readonly' => true,
            'onclick' => 'showSelectCustomer(\'sender\')',
            'after_element_html' => '</td><td class="label"><a id="sender" href="javascript:showSelectCustomer(\'sender\')" title="'
            . Mage::helper('rewardpointstransfer')->__('Select') . '">'
            . Mage::helper('rewardpointstransfer')->__('Select') . '</a>'
            . '<script type="text/javascript">
                    function showSelectCustomer(element) {
                        $("validate_receiver_email").hide();
                        if(element=="sender")
                        {
                            cus_selected=$("sender_id");
                        }
                        else
                        {
                            cus_selected=$("receiver_id");
                        }
                        new Ajax.Request("'
            . $this->getUrl('*/*/customer', array('_current' => true))
            . '", {
                            parameters: {
                                         form_key: FORM_KEY,
                                         selected_customer_id: cus_selected.value || 0,
                                         element:element
                                         },
                            evalScripts: true,
                            onSuccess: function(transport) {
                                TINY.box.show("");
                                $("tinycontent").update(transport.responseText);
                            }
                        });
                    }
                </script>',
        ));
        $pending_day=Mage::helper('rewardpointstransfer')->getTransferConfig('pending_day',Mage::app()->getStore()->getId());
        if($pending_day==''||$pending_day==0){
            $pending_day=30;
        }
        $fieldset->addField('receiver_email', 'text', array(
            'label' => Mage::helper('rewardpointstransfer')->__('Recipient\'s Email'),
            'title' => Mage::helper('rewardpointstransfer')->__('Recipient\'s Email'),
            'class' => 'validate-email',
            'required' => true,
            'name' => 'receiver_email',
            'onchange' => 'checkExistEmail()',
            'after_element_html' => '
          
            <div class="validation-advice" id="validate_receiver_email" style="display:none;" ></div>
            </td><td class="label"><a id="sender" href="javascript:showSelectCustomer(\'receiver\')" title="'
            . Mage::helper('rewardpointstransfer')->__('Select') . '">'
            . Mage::helper('rewardpointstransfer')->__('Select') . '</a>'
            . '<script type="text/javascript">
                    function checkExistEmail() {
                        var validate=$("validate_receiver_email");
                        if(Object.extend(Validation.validate($("receiver_email")))){
                        var receiver_email=$("receiver_email");
                        var sender_email=$("sender_email");
                        
                       
                      
                        var website_id=' . Mage::app()->getStore()->getWebsiteId() . ';
                        new Ajax.Request("'
            . $this->getUrl('*/*/checkCustomer', array('_current' => true))
            . '", {
                            parameters: {
                                         receiver_email: receiver_email.value,
                                         sender_email:sender_email.value,
                                         website_id:website_id
                                         },
//                            evalScripts: true,
                            onSuccess: function(transport) {
                            var response=transport.responseText;
                            switch(response)
                            {
                                case "success":
                                    validate.hide();break;
                                
                                case "notexist":
                                    validate.show();
                                    validate.innerHTML="' . $this->helper('rewardpointstransfer')->__("The email does not exist on the website! If you continue, a notification email will be sent to that email. This transfer will be canceled after ").$pending_day.Mage::helper('rewardpointstransfer')->__(" days if this person does not create an account on your store.")  . '";
                                    break;
                                
                                case "duplicate":
                                    $("receiver_email").select();
                                    validate.show();
                                    validate.innerHTML="' . $this->helper('rewardpointstransfer')->__("Duplicated email error!") . '";
                                    break;
                            }
                            }
                            
                        });
                    }
                    else validate.hide();
                        }
                        
                </script>',
        ));
        $fieldset->addField('sender_id', 'hidden', array('name' => 'sender_id'));
        $fieldset->addField('receiver_id', 'hidden', array('name' => 'receiver_id'));
        $fieldset->addField('check_point', 'hidden', array('name' => 'check_point'));
        $fieldset->addField('point_amount', 'text', array(
            'label' => Mage::helper('rewardpointstransfer')->__('Point Amount'),
            'title' => Mage::helper('rewardpointstransfer')->__('Point Amount'),
            'name' => 'point_amount',
            'class' => 'validate-number',
            'required' => true,
            'onchange' => 'checkPointAmount()',
            'after_element_html' => '
            <div class="validation-advice" id="validate_point_amount" style="display:none;" ></div>
            <script type="text/javascript">
                    function checkPointAmount() {
                        var validate=$("validate_point_amount");
                        if(Object.extend(Validation.validate($("point_amount")))){
                            
                        var point_amount=$("point_amount");
                        if(point_amount.value<=0)
                        {
                            validate.show(); 
                            validate.innerHTML="' . Mage::helper('rewardpointstransfer')->__("The transferred point amount must be greater than 0").'";
                            point_amount.select();
                            return false;
                        }
                        var check_point=$("check_point");
                        if(check_point.value=="") check_point.value=0;
                        if($("sender_email").value.length>0&&parseInt(check_point.value)<parseInt(point_amount.value)){
                            validate.show(); 
                            validate.innerHTML="' . Mage::helper('rewardpointstransfer')->__("The sender\'s curent point balance is ") . '"+check_point.value+"' . Mage::helper('rewardpointstransfer')->__(". You cannot enter a greater number than this!") . '";
                            point_amount.select();
                            return false;
                        }
                        else{
                        validate.hide();
                        return true;
                        }
                     }
                     else validate.hide();
                   }
                        
                        
                </script>',
        ));
//        if ($model->getStatus() == Magestore_RewardPointsTransfer_Model_Status::STATUS_PENDING) {
//            $status = array(array(
//                    'label' => Mage::helper('rewardpointstransfer')->__('Complete'),
//                    'value' => Magestore_RewardPointsTransfer_Model_Status::STATUS_COMPLETED
//                ),
//                array('label' => Mage::helper('rewardpointstransfer')->__('Pending'),
//                    'value' => Magestore_RewardPointsTransfer_Model_Status::STATUS_PENDING));
//        } else {
//            $status = array(array(
//                    'label' => Mage::helper('rewardpointstransfer')->__('Complete'),
//                    'value' => Magestore_RewardPointsTransfer_Model_Status::STATUS_COMPLETED
//                ),
//                array('label' => Mage::helper('rewardpointstransfer')->__('Pending'),
//                    'value' => Magestore_RewardPointsTransfer_Model_Status::STATUS_HOLDING));
//        }
//        $fieldset->addField('status', 'select', array(
//            'label' => Mage::helper('rewardpointstransfer')->__('Status'),
//            'title' => Mage::helper('rewardpointstransfer')->__('Status'),
//            'name' => 'status',
//            'values' => $status,
//            'onchange' => 'showHodlingDay()',
//        ));
        $fieldset->addField('holding_day', 'text', array(
            'label' => Mage::helper('rewardpointstransfer')->__('Hold point transfer for'),
            'title' => Mage::helper('rewardpointstransfer')->__('Hold point transfer for'),
            'name' => 'holding_day',
            'class' => 'validate-number',
            'required' => false,
            'note' => Mage::helper('rewardpointstransfer')->__('day(s). If empty or zero, transfers are completed immediately.')
        ));
        $fieldset->addField('extra_content', 'textarea', array(
            'label' => Mage::helper('rewardpointstransfer')->__('Comment/Note'),
            'title' => Mage::helper('rewardpointstransfer')->__('Comment/Note'),
            'name' => 'extra_content',
            'style' => 'height: 5em;'
        ));
        $form->setValues($model->getData());
        return parent::_prepareForm();
    }

}