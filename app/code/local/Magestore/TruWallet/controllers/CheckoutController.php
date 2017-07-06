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
 * TruWallet Controller
 * 
 * @category    Magestore
 * @package     Magestore_TruWallet
 * @author      Magestore Developer
 */
class Magestore_TruWallet_CheckoutController extends Mage_Core_Controller_Front_Action
{

    /**
     * change use customer credit to spend
     */
    public function setAmountPostAction()
    {
        $request = $this->getRequest();
        $session = Mage::getSingleton('checkout/session');
        $account = Mage::helper('truwallet/account')->getCurrentAccount();

        if ($request->isPost()) {
            if (is_numeric($request->getParam('credit_amount'))) {
                if(Mage::helper('custompromotions')->truWalletInCart())
                    $amount = 0;
                else 
                    $amount = $request->getParam('credit_amount');

                $session->setCancelCredit(true);

                $base_amount = Mage::getModel('truwallet/customer')
                    ->getConvertedToBaseTruwalletCredit($amount);

                $base_customer_credit = $account->getTruwalletCredit();
                $base_credit_amount = ($base_amount > $base_customer_credit) ? $base_customer_credit : $base_amount;

                $session->setBaseTruwalletCreditAmount($base_credit_amount);
                $session->setUseTruwalletCredit(true);
                
                $result = array();
                $result['success'] = 1;
                $result['price0'] = 0;

                $state = $request->getParam('state');

                $moduleOnestepActive = Mage::getConfig()->getModuleConfig('Magestore_Onestepcheckout')->is('active', 'true');
                $moduleWebposActive = Mage::getConfig()->getModuleConfig('Magestore_Webpos')->is('active', 'true');
                if ($moduleOnestepActive || ($moduleWebposActive && $state == 'webpos')) {
                    if (Mage::getStoreConfig('onestepcheckout/general/active') == '1' && $state == 'onestepcheckout') {
                        $result['saveshippingurl'] = Mage::getUrl('onestepcheckout/index/save_shipping', array('_secure' => true));
                    }
                    if ($state == 'webpos') {
                        $result['saveshippingurl'] = Mage::getUrl('webpos/index/save_shipping', array('_secure' => true));
                    }
                    $result['amount'] = Mage::getModel('truwallet/customer')
                        ->getConvertedFromBaseTruwalletCredit($session->getBaseTruwalletCreditAmount());

                    $result['current_balance'] = $account->getTruwalletCredit() - $amount <= 0 ? 0 : $account->getTruwalletCredit() - $amount;
                } else {
                    Mage::getSingleton('checkout/type_onepage')->getQuote()->collectTotals()->save();
                    $result['amount'] = Mage::getModel('truwallet/customer')
                        ->getConvertedFromBaseTruwalletCredit($session->getBaseTruwalletCreditAmount());
                    if ($session->getBaseTruwalletCreditAmount() == Mage::getSingleton('checkout/session')->getQuote()->getBaseGrandTotal())
                        $result['price0'] = 1;
                    $result['current_balance'] = Mage::getModel('truwallet/customer')->getAvaiableTruwalletCreditLabel();
                    $html = $this->_getPaymentMethodsHtml();
                    $result['payment_html'] = $html;
                }
                $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
            }
        }
    }

    /**
     * @return mixed
     */
    protected function _getPaymentMethodsHtml()
    {
        $layout = $this->getLayout();
        $update = $layout->getUpdate();
        $update->load('checkout_onepage_paymentmethod');
        $layout->generateXml();
        $layout->generateBlocks();
        $output = $layout->getOutput();
        return $output;
    }

}
