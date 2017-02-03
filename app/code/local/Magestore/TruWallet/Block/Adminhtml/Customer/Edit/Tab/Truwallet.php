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
 * @package     Magestore_truwallet
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * truwallet Tab on Customer Edit Form Block
 * 
 * @category    Magestore
 * @package     Magestore_truwallet
 * @author      Magestore Developer
 */
class Magestore_TruWallet_Block_Adminhtml_Customer_Edit_Tab_Truwallet
    extends Mage_Adminhtml_Block_Widget_Form
    implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    protected $_truwalletAccount = null;

    /**
     * @return Mage_Core_Model_Abstract|null
     * @throws Exception
     */
    public function getTruwalletAccount()
    {
        if (is_null($this->_truwalletAccount)) {
            $customerId = $this->getRequest()->getParam('id');
            $this->_truwalletAccount = Mage::getModel('truwallet/customer')
                ->load($customerId, 'customer_id');
        }
        return $this->_truwalletAccount;
    }

    public function getTruWalletCredit()
    {
        return Mage::helper('core')->currency(
            $this->getTruwalletAccount()->getTruwalletCredit(),
            true,
            false
        );
    }

    /**
     * @return Mage_Adminhtml_Block_Widget_Form
     */
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $form->setHtmlIdPrefix('truwallet_');
        $this->setForm($form);
        
        $fieldset = $form->addFieldset('truwallet_form', array(
            'legend' => Mage::helper('truwallet')->__('truWallet Information')
        ));

        $fieldset->addField('truwallet_balance', 'note', array(
            'label' => Mage::helper('truwallet')->__('truWallet Balance'),
            'title' => Mage::helper('truwallet')->__('truWallet Balance'),
            'text'  => '<strong>' . $this->getTruWalletCredit() . '</strong>',
        ));

        $fieldset->addField('truwallet_credit', 'text', array(
            'label' => Mage::helper('truwallet')->__('Change Product Credit'),
            'title' => Mage::helper('truwallet')->__('Change Product Credit'),
            'name'  => 'truWallet[credit]',
            'note'  => Mage::helper('truwallet')->__('Add or subtract customer\'s product credit balance. For ex: 99 or -99 product credit.'),
        ));

        $fieldset->addField('title_credit', 'textarea', array(
            'label' => Mage::helper('truwallet')->__('Change truWallet Title'),
            'title' => Mage::helper('truwallet')->__('Change truWallet Title'),
            'name'  => 'truWallet[title]',
            'style' => 'height: 5em;'
        ));

        $fieldset = $form->addFieldset('truwallet_history_fieldset', array(
            'legend' => Mage::helper('truwallet')->__('Transaction History')
        ))->setRenderer($this->getLayout()->createBlock('adminhtml/widget_form_renderer_fieldset')->setTemplate(
            'truwallet/history.phtml'
        ));
        
        return parent::_prepareForm();
    }
    
    public function getTabLabel()
    {
        return Mage::helper('truwallet')->__('TruWallet');
    }
    
    public function getTabTitle()
    {
        return Mage::helper('truwallet')->__('TruWallet');
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
