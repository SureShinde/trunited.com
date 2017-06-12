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
 * @package     Magestore_trugiftcard
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * trugiftcard Tab on Customer Edit Form Block
 * 
 * @category    Magestore
 * @package     Magestore_trugiftcard
 * @author      Magestore Developer
 */
class Magestore_TruGiftCard_Block_Adminhtml_Customer_Edit_Tab_Trugiftcard
    extends Mage_Adminhtml_Block_Widget_Form
    implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    protected $_trugiftcardAccount = null;

    /**
     * @return Mage_Core_Model_Abstract|null
     * @throws Exception
     */
    public function getTruwalletAccount()
    {
        if (is_null($this->_trugiftcardAccount)) {
            $customerId = $this->getRequest()->getParam('id');
            $this->_trugiftcardAccount = Mage::getModel('trugiftcard/customer')
                ->load($customerId, 'customer_id');
        }
        return $this->_trugiftcardAccount;
    }

    public function getTruGiftCardCredit()
    {
        return Mage::helper('core')->currency(
            $this->getTruwalletAccount()->getTrugiftcardCredit(),
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
        $form->setHtmlIdPrefix('trugiftcard_');
        $this->setForm($form);
        
        $fieldset = $form->addFieldset('trugiftcard_form', array(
            'legend' => Mage::helper('trugiftcard')->__('truGiftCard Information')
        ));

        $fieldset->addField('trugiftcard_balance', 'note', array(
            'label' => Mage::helper('trugiftcard')->__('truGiftCard Balance'),
            'title' => Mage::helper('trugiftcard')->__('truGiftCard Balance'),
            'text'  => '<strong>' . $this->getTruGiftCardCredit() . '</strong>',
        ));

        $fieldset->addField('trugiftcard_credit', 'text', array(
            'label' => Mage::helper('trugiftcard')->__('Change truGiftCard Balance'),
            'title' => Mage::helper('trugiftcard')->__('Change truGiftCard Balance'),
            'name'  => 'truGiftCard[credit]',
            'class' => 'validate-number',
            'note'  => Mage::helper('trugiftcard')->__('Add or subtract customer\'s trugiftcard balance. For ex: 99 or -99 trugiftcard balance.'),
        ));

        $fieldset->addField('title_credit', 'textarea', array(
            'label' => Mage::helper('trugiftcard')->__('Change Transaction Title'),
            'title' => Mage::helper('trugiftcard')->__('Change Transaction Title'),
            'name'  => 'truGiftCard[title]',
            'style' => 'height: 5em;'
        ));

        $fieldset = $form->addFieldset('trugiftcard_history_fieldset', array(
            'legend' => Mage::helper('trugiftcard')->__('Transaction History')
        ))->setRenderer($this->getLayout()->createBlock('adminhtml/widget_form_renderer_fieldset')->setTemplate(
            'trugiftcard/history.phtml'
        ));
        
        return parent::_prepareForm();
    }
    
    public function getTabLabel()
    {
        return Mage::helper('trugiftcard')->__('TruGiftCard');
    }
    
    public function getTabTitle()
    {
        return Mage::helper('trugiftcard')->__('TruGiftCard');
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
