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
 * @package     Magestore_Storecredit
 * @module      Storecredit
 * @author      Magestore Developer
 *
 * @copyright   Copyright (c) 2016 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 *
 */

/**
 * TruWallet Block
 * 
 * @category    Magestore
 * @package     Magestore_TruWallet
 * @author      Magestore Developer
 */
class Magestore_TruWallet_Block_Payment_Form extends Mage_Payment_Block_Form
{

    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('truwallet/payment/form.phtml');
    }

    /**
     * @return mixed
     */
    public function getUseCustomerCredit()
    {
        return Mage::getSingleton('checkout/session')->getUseTruwalletCredit();
    }

    /**
     * @return bool
     */
    public function hasCustomerCreditItemOnly()
    {
        return true;
    }

    /**
     * @return bool
     */
    public function hasCustomerCreditItem()
    {
        return true;
    }

    public function getCurrentAccount()
    {
        $customer_id = Mage::helper('truwallet/account')->getCustomerId();
        if($customer_id != null)
        {
            return Mage::helper('truwallet/account')->loadByCustomerId($customer_id);
        } else {
            return null;
        }
    }

    /**
     * @return mixed
     */
    public function getCustomerCredit()
    {
        if($this->getCurrentAccount() != null)
            return $this->getCurrentAccount()->getTruwalletCredit();
        else
            return null;
    }

    /**
     * @return mixed
     */
    public function getCustomerCreditLabel()
    {
        if($this->getCurrentAccount() != null)
            return Mage::helper('truwallet')->formatTruwallet($this->getCurrentAccount()->getTruwalletCredit());
        else
            return null;
    }

    /**
     * @return mixed
     */
    public function getAvaiableCustomerCreditLabel()
    {
        if($this->getCurrentAccount() != null)
            return Mage::helper('truwallet')->formatTruwallet($this->getCurrentAccount()->getTruwalletCredit());
        else
            return null;
    }

    /**
     * @return mixed
     */
    public function getCurrentCreditAmount()
    {
        $base_amount = Mage::getSingleton('checkout/session')->getBaseTruwalletCreditAmount();
        return Mage::getModel('truwallet/customer')->getConvertedFromBaseTruwalletCredit($base_amount);
    }

    /**
     * @return mixed
     */
    public function getCurrentCreditAmountLabel()
    {
        $base_amount = Mage::getSingleton('checkout/session')->getBaseTruwalletCreditAmount();
        return Mage::getModel('truwallet/customer')->getConvertedFromBaseTruwalletCredit($base_amount);
    }

    /**
     * @return mixed
     */
    public function getUpdateUrl()
    {
        return $this->getUrl('truwallet/checkout/setAmountPost', array('_secure' => true));
    }

    public function getCurrentGrandTotal()
    {
        $quote = Mage::getModel('checkout/session')->getQuote();
        $quoteData = $quote->getData();
        return $quoteData['grand_total'];
    }

    public function getCreditUsed()
    {
        $account_credit = $this->getCustomerCredit();
        if($account_credit != null)
        {
            $used_credit = $account_credit <= $this->getCurrentGrandTotal() ? $account_credit : $this->getCurrentGrandTotal();
            if($this->getCurrentCreditAmount() > 0)
                return $this->getCurrentCreditAmount();
            else
                return $used_credit;
        } else {
            return 0;
        }
    }

    public function isReloadAutomatically()
    {
        if(Mage::getSingleton('checkout/session')->getCancelCredit())
            return false;
        else
            return true;
    }

    public function getLabelCurrent()
    {
        return Mage::helper('truwallet')->getSpendConfig('current_label');
    }

    public function getLabelApplied()
    {
        return Mage::helper('truwallet')->getSpendConfig('applied_label');
    }

    public function getBackgroundColor()
    {
        return Mage::helper('truwallet')->getSpendConfig('background_color');
    }

    public function getTextColor()
    {
        return Mage::helper('truwallet')->getSpendConfig('text_color');
    }

}
