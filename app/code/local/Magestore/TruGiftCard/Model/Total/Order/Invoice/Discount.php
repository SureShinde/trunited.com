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
 * Trugiftcard Model
 * 
 * @category    Magestore
 * @package     Magestore_TruGiftCard
 * @author      Magestore Developer
 */
class Magestore_TruGiftCard_Model_Total_Order_Invoice_Discount extends Mage_Sales_Model_Order_Invoice_Total_Abstract
{
    public function collect(Mage_Sales_Model_Order_Invoice $invoice)
    {
        $order = $invoice->getOrder();
        if ($order->getTrugiftcardDiscount() < 0.0001) {
            return;
        }

        $invoice->setBaseTrugiftcardDiscount(0);
        $invoice->setTrugiftcardDiscount(0);

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
            if ($previousInvoice->getTrugiftcardDiscount()) {
                $checkAddShipping = false;
                $totalBaseDiscountInvoiced += $previousInvoice->getBaseTrugiftcardDiscount();
                $totalDiscountInvoiced += $previousInvoice->getTrugiftcardDiscount();

                $hiddenTaxInvoiced += $previousInvoice->getTrugiftcardHiddenTax();
                $baseHiddenTaxInvoiced += $previousInvoice->getBaseTrugiftcardHiddenTax();
            }
        }

        if ($checkAddShipping) {
            $totalBaseDiscountAmount += $order->getBaseTrugiftcardDiscountForShipping();
            $totalDiscountAmount += $order->getTrugiftcardDiscountForShipping();

            $totalBaseHiddenTax += $order->getBaseTrugiftcardShippingHiddenTax();
            $totalHiddenTax += $order->getTrugiftcardShippingHiddenTax();
        }

        $totalBaseDiscountAmount = $order->getBaseTrugiftcardDiscount() - $totalBaseDiscountInvoiced;
        $totalDiscountAmount = $order->getTrugiftcardDiscount() - $totalDiscountInvoiced;

        $totalHiddenTax = $order->getTrugiftcardHiddenTax() - $hiddenTaxInvoiced;
        $totalBaseHiddenTax = $order->getBaseTrugiftcardHiddenTax() - $baseHiddenTaxInvoiced;

        $invoice->setBaseTrugiftcardDiscount($totalBaseDiscountAmount);
        $invoice->setTrugiftcardDiscount($totalDiscountAmount);

        $invoice->setBaseTrugiftcardHiddenTax($totalBaseHiddenTax);
        $invoice->setTrugiftcardHiddenTax($totalHiddenTax);

        // if($_SERVER['REMOTE_ADDR'] == '42.112.152.68'){
        //     zend_debug::dump($totalBaseDiscountAmount);
        //     zend_debug::dump($totalBaseHiddenTax);
        //     zend_debug::dump($order->debug());
        //     var_dump('bbbbb');

        //     exit;
        // }

        $invoice->setBaseGrandTotal($invoice->getBaseGrandTotal() - $totalBaseDiscountAmount + $totalBaseHiddenTax);
        $invoice->setGrandTotal($invoice->getGrandTotal() - $totalDiscountAmount + $totalHiddenTax);


    }

}
