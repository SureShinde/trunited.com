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
 * Truwallet Model
 * 
 * @category    Magestore
 * @package     Magestore_TruWallet
 * @author      Magestore Developer
 */
class Magestore_TruWallet_Model_Total_Order_Invoice_Discount extends Mage_Sales_Model_Order_Invoice_Total_Abstract
{
    public function collect(Mage_Sales_Model_Order_Invoice $invoice)
    {
        $order = $invoice->getOrder();
        if ($order->getTruwalletDiscount() < 0.0001) {
            return;
        }

        $invoice->setBaseTruwalletDiscount(0);
        $invoice->setTruwalletDiscount(0);

        $totalDiscountInvoiced = 0;
        $totalBaseDiscountInvoiced = 0;

        $totalDiscountAmount = 0;
        $totalBaseDiscountAmount = 0;

        $totalHiddenTax = 0;
        $totalBaseHiddenTax = 0;

        $hiddenTaxInvoiced = 0;
        $baseHiddenTaxInvoiced = 0;
        $checkAddShipping = true;

        foreach ($order->getInvoiceCollection() as $previousInvoice) {
            if ($previousInvoice->getTruwalletDiscount()) {
                $checkAddShipping = false;
                $totalBaseDiscountInvoiced += $previousInvoice->getBaseTruwalletDiscount();
                $totalDiscountInvoiced += $previousInvoice->getTruwalletDiscount();

                $hiddenTaxInvoiced += $previousInvoice->getTruwalletHiddenTax();
                $baseHiddenTaxInvoiced += $previousInvoice->getBaseTruwalletHiddenTax();
            }
        }

        if ($checkAddShipping) {
            $totalBaseDiscountAmount += $order->getBaseTruwalletDiscountForShipping();
            $totalDiscountAmount += $order->getTruwalletDiscountForShipping();

            $totalBaseHiddenTax += $order->getBaseTruwalletShippingHiddenTax();
            $totalHiddenTax += $order->getTruwalletShippingHiddenTax();
        }


        $totalBaseDiscountAmount = $order->getBaseTruwalletDiscount() - $totalBaseDiscountInvoiced;
        $totalDiscountAmount = $order->getTruwalletDiscount() - $totalDiscountInvoiced;

        $totalHiddenTax = $order->getTruwalletHiddenTax() - $hiddenTaxInvoiced;
        $totalBaseHiddenTax = $order->getBaseTruwalletHiddenTax() - $baseHiddenTaxInvoiced;

        $invoice->setBaseTruwalletDiscount($totalBaseDiscountAmount);
        $invoice->setTruwalletDiscount($totalDiscountAmount);

        $invoice->setBaseTruwalletHiddenTax($totalBaseHiddenTax);
        $invoice->setTruwalletHiddenTax($totalHiddenTax);

        // if($_SERVER['REMOTE_ADDR'] == '42.112.152.68'){
        //     zend_debug::dump($totalBaseDiscountAmount);
        //     zend_debug::dump($totalBaseHiddenTax);
        //     var_dump('aaaaa');

        //     exit;
        // }

        $invoice->setBaseGrandTotal($invoice->getBaseGrandTotal() - $totalBaseDiscountAmount + $totalBaseHiddenTax);
        $invoice->setGrandTotal($invoice->getGrandTotal() - $totalDiscountAmount + $totalHiddenTax);
    }

}
