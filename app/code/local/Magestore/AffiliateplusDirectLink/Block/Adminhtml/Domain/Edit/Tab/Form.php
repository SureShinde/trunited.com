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
 * @package     Magestore_AffiliateplusDirectLink
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * Domain Edit Form Content Tab Block
 * 
 * @category    Magestore
 * @package     Magestore_AffiliateplusDirectLink
 * @author      Magestore Developer
 */
class Magestore_AffiliateplusDirectLink_Block_Adminhtml_Domain_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form {

    /**
     * prepare tab form's information
     *
     * @return Magestore_AffiliateplusDirectLink_Block_Adminhtml_Domain_Edit_Tab_Form
     */
    protected function _prepareForm() {
        $form = new Varien_Data_Form();
        $this->setForm($form);

        if (Mage::getSingleton('adminhtml/session')->getDomainData()) {
            $data = Mage::getSingleton('adminhtml/session')->getDomainData();
            Mage::getSingleton('adminhtml/session')->setDomainData(null);
        } elseif (Mage::registry('domain_data')) {
            $data = Mage::registry('domain_data')->getData();
        }
        $fieldset = $form->addFieldset('domain_form', array(
            'legend' => Mage::helper('affiliateplusdirectlink')->__('Domain information')
                ));
        $accountarr = array();
        $accountcollection = Mage::getModel('affiliateplus/account')->getCollection()
                ->addFieldToFilter('status', 1)
                ->addFieldToFilter('approved', 1);
        foreach ($accountcollection as $value) {
            $accountarr[$value->getId()] = $value->getName();
        }
        $fieldset->addField('account_id', 'select', array(
            'label' => Mage::helper('affiliateplusdirectlink')->__('Account'),
            'name' => 'account_id',
            'values' => $accountarr,
//            'onchange' => 'onchangeOptionClick()',
//            'after_element_html' => '<script type="text/javascript">
//                $account = Mage::getModel("affiliateplus/account")->load($data["account_id"]);
//                $email = "<a href="mailto:" . $account->getEmail() . ">" . $account->getEmail() . "</a>";
//                function onchangeOptionClick(){
//                    var des = $("email");
//                    var tag = $("account_id");
//                    tag.value;
//                    
//                }
//                Event.observe(window,\'load\',onchangeOptionClick);
//            </script>',
        ));

//        $account = Mage::getModel('affiliateplus/account')->load($data['account_id']);
//        $email = '<a href="mailto:' . $account->getEmail() . '">' . $account->getEmail() . '</a>';
//        $fieldset->addField('email', 'note', array(
//            'label' => Mage::helper('affiliateplusdirectlink')->__('Email'),
//            'text' => $email,
//        ));

        $fieldset->addField('domain', 'text', array(
            'label' => Mage::helper('affiliateplusdirectlink')->__('Domain'),
            'class' => 'required-entry',
            'required' => true,
            'name' => 'domain',
        ));

        $fieldset->addField('status', 'select', array(
            'label' => Mage::helper('affiliateplusdirectlink')->__('Status'),
            'name' => 'status',
            'values' => Mage::getSingleton('affiliateplusdirectlink/status')->getOptionHash(),
        ));

        $form->setValues($data);
        return parent::_prepareForm();
    }

}
