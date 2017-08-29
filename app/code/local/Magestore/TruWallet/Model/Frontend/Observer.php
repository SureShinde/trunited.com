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
 * @package     Magestore_RewardPoints
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * RewardPoints Frontend Observer Model
 * 
 * @category    Magestore
 * @package     Magestore_RewardPoints
 * @author      Magestore Developer
 */
class Magestore_TruWallet_Model_Frontend_Observer {

    /**
     * add speding points block into payment method
     * 
     * @param type $observer
     */
    public function truwalletPaymentMethod($observer) {
        $block = $observer['block'];
        if ($block instanceof Mage_Checkout_Block_Onepage_Payment_Methods || $block instanceof Magestore_Webpos_Block_Onepage_Payment_Methods) {
            /*$transport = $observer['transport'];
            $html_addcredit = $block->getLayout()->createBlock('truwallet/payment_form')->renderView();
            $html = $transport->getHtml();
            $html .= '<script type="text/javascript">checkOutLoadCustomerCredit(' . Mage::helper('core')->jsonEncode(array('html' => $html_addcredit)) . ');enableCheckbox();</script>';
            $transport->setHtml($html);*/
        }
    }

    /**
     * @param $observer
     * @return $this
     */
    public function paypalPrepareLineItems($observer) {
        if (version_compare(Mage::getVersion(), '1.4.2', '>=')) {
            if ($paypalCart = $observer->getPaypalCart()) {
                $salesEntity = $paypalCart->getSalesEntity();

                $baseDiscount = $salesEntity->getRewardpointsBaseDiscount();
                if ($baseDiscount < 0.0001 && $salesEntity instanceof Mage_Sales_Model_Quote) {
                    $helper = Mage::helper('truwallet/spending');
                    $baseDiscount = $helper->getPointItemDiscount();
                    $baseDiscount += $helper->getCheckedRuleDiscount();
                    $baseDiscount += $helper->getSliderRuleDiscount();
                }
                //$baseDiscount -= $salesEntity->getRewardpointsBaseHiddenTaxAmount();
                if ($baseDiscount > 0.0001) {
                    $paypalCart->updateTotal(
                            Mage_Paypal_Model_Cart::TOTAL_DISCOUNT, (float) $baseDiscount, Mage::helper('truwallet')->__('Use points on spend')
                    );
                }
            }
            return $this;
        }
        $salesEntity = $observer->getSalesEntity();
        $additional = $observer->getAdditional();
        if ($salesEntity && $additional) {
            $baseDiscount = $salesEntity->getRewardpointsBaseDiscount();
            if ($baseDiscount < 0.0001 && $salesEntity instanceof Mage_Sales_Model_Quote) {
                $helper = Mage::helper('truwallet/spending');
                $baseDiscount = $helper->getPointItemDiscount();
                $baseDiscount += $helper->getCheckedRuleDiscount();
                $baseDiscount += $helper->getSliderRuleDiscount();
            }

            if ($baseDiscount > 0.0001) {
                $items = $additional->getItems();
                $items[] = new Varien_Object(array(
                    'name' => Mage::helper('truwallet')->__('Truwallet on spend'),
                    'qty' => 1,
                    'amount' => -(float) $baseDiscount,
                ));
                $additional->setItems($items);
            }
        }
    }

}
