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
 * Affiliateplusdirectlink Edit Form Content Tab Block
 * 
 * @category    Magestore
 * @package     Magestore_AffiliateplusDirectLink
 * @author      Magestore Developer
 */
class Magestore_AffiliateplusDirectLink_Block_Adminhtml_Affiliateplusdirectlink_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form {

    /**
     * prepare tab form's information
     *
     * @return Magestore_AffiliateplusDirectLink_Block_Adminhtml_Affiliateplusdirectlink_Edit_Tab_Form
     */
    protected function _prepareForm() {
        $form = new Varien_Data_Form();
        $this->setForm($form);

        if (Mage::getSingleton('adminhtml/session')->getAffiliateplusDirectLinkData()) {
            $data = Mage::getSingleton('adminhtml/session')->getAffiliateplusDirectLinkData();
            Mage::getSingleton('adminhtml/session')->setAffiliateplusDirectLinkData(null);
        } elseif (Mage::registry('affiliateplusdirectlink_data')) {
            $data = Mage::registry('affiliateplusdirectlink_data')->getData();
        }
        $fieldset = $form->addFieldset('affiliateplusdirectlink_form', array(
            'legend' => Mage::helper('affiliateplusdirectlink')->__('Direct Link information')
                ));

        $bannerarr = array();
        $bannercollection = Mage::getModel('affiliateplus/banner')->getCollection()
                ->addFieldToFilter('status', 1);
        foreach ($bannercollection as $value) {
            $bannerarr[$value->getId()] = $value->getTitle();
        }

        $accountarr = array();
        $accountarr[0] = '';
        $accountcollection = Mage::getModel('affiliateplus/account')->getCollection()
                ->addFieldToFilter('status', 1)
                ->addFieldToFilter('approved', 1);
        foreach ($accountcollection as $value) {
            $accountarr[$value->getId()] = $value->getName();
        }
        $domain = Mage::getModel('affiliateplusdirectlink/domain')->load($data['domain_id']);
        $data['account_id'] = $domain->getAccountId();

        $fieldset->addField('account_id', 'select', array(
            'label' => Mage::helper('affiliateplusdirectlink')->__('Account'),
            'class' => 'required-entry',
            'required' => true,
            'name' => 'account_id',
            'values' => $accountarr,
        ));
        if ($this->getRequest()->getParam('id')) {
            //Changed By Adam 29/10/2015: Fix issue of SUPEE 6788 - in Magento 1.9.2.2
            $note = '<a href="' . $this->getUrl('adminhtml/affiliateplusdirectlink_domain/edit', array('id' => $domain->getId())) . '">' . Mage::helper('affiliateplusdirectlink')->__($domain->getDomain()) . '</a>';
            $fieldset->addField('domain', 'note', array(
                'label' => Mage::helper('affiliateplusdirectlink')->__('Domain'),
                'name' => 'domain',
                'text' => $note,
            ));
        }
        if ($this->getRequest()->getParam('id'))
            $data['source_page'] = $domain->getDomain() . '/' . $data['source_page'];
        $fieldset->addField('source_page', 'text', array(
            'label' => Mage::helper('affiliateplusdirectlink')->__('Page To Show Banner'),
            'name' => 'source_page',
        ));

        $fieldset->addField('banner_id', 'select', array(
            'label' => Mage::helper('affiliateplusdirectlink')->__('Banner'),
            'class' => 'required-entry',
            'required' => true,
            'name' => 'banner_id',
            'values' => $bannerarr,
        ));

        $fieldset->addField('link', 'text', array(
            'label' => Mage::helper('affiliateplusdirectlink')->__('Direct Link'),
            'name' => 'link',
            'note' => 'If left blank, it will take banner link',
        ));
        if ($this->getRequest()->getParam('id')) {
            $status = Mage::getSingleton('affiliateplusdirectlink/status')->getOptionArray();
            $fieldset->addField('status', 'note', array(
                'label' => Mage::helper('affiliateplusdirectlink')->__('Status'),
                'name' => 'status',
                'text' => $status[$data['status']],
                'values' => Mage::getSingleton('affiliateplusdirectlink/status')->getOptionArray(),
            ));
        }
        $form->setValues($data);
        return parent::_prepareForm();
    }

}