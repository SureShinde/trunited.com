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
class Magestore_TruGiftCard_CheckoutController extends Mage_Core_Controller_Front_Action
{

    /**
     * change use customer credit to spend
     */
    public function setAmountPostAction()
    {
        $request = $this->getRequest();
        $session = Mage::getSingleton('checkout/session');
        $account = Mage::helper('trugiftcard/account')->getCurrentAccount();

        if ($request->isPost()) {
            if (($request->getParam('is_checked'))) {
                $is_check = $request->getParam('is_checked');

                $check = false;
                if($is_check == 'true')
                {
                    $amount = Mage::getModel('checkout/session')->getQuote()->getGrandTotal();
                    $check = true;
                }
                else
                    $amount = 0;

                $session->setCancelCredit(true);

                $base_amount = Mage::getModel('trugiftcard/customer')
                    ->getConvertedToBaseTrugiftcardCredit($amount);

                $base_customer_credit = $account->getTrugiftcardCredit();
                $base_credit_amount = ($base_amount > $base_customer_credit) ? $base_customer_credit : $base_amount;

                $session->setBaseTrugiftcardCreditAmount($base_credit_amount);
                $session->setUseTrugiftcardCredit(true);

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
                    $result['amount'] = Mage::getModel('trugiftcard/customer')
                        ->getConvertedFromBaseTrugiftcardCredit($session->getBaseTrugiftcardCreditAmount());

                    $result['current_balance'] = $account->getTrugiftcardCredit() - $amount <= 0 ? 0 : $account->getTrugiftcardCredit() - $amount;
                } else {
                    Mage::getSingleton('checkout/type_onepage')->getQuote()->collectTotals()->save();
                    $result['amount'] = Mage::getModel('trugiftcard/customer')
                        ->getConvertedFromBaseTrugiftcardCredit($session->getBaseTrugiftcardCreditAmount());
                    if ($session->getBaseTrugiftcardCreditAmount() == Mage::getSingleton('checkout/session')->getQuote()->getBaseGrandTotal())
                        $result['price0'] = 1;
                    $result['current_balance'] = Mage::getModel('trugiftcard/customer')->getAvaiableTrugiftcardCreditLabel();
                    $html = $this->_getPaymentMethodsHtml();
                    $result['payment_html'] = $html;
                }

                $result['check'] = !$check ? 1 : 2;
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
