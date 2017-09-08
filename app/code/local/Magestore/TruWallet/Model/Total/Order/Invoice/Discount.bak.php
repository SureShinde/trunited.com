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

        $totalDiscountInvoicedTrugiftcard = 0;
        $totalBaseDiscountInvoicedTrugiftcard = 0;

        $totalDiscountAmountTrugiftcard = 0;
        $totalBaseDiscountAmountTrugiftcard = 0;

        $totalHiddenTaxTrugiftcard = 0;
        $totalBaseHiddenTaxTrugiftcard = 0;

        $hiddenTaxInvoicedTrugiftcard = 0;
        $baseHiddenTaxInvoicedTrugiftcard = 0;
        $checkAddShippingTrugiftcard = true;


        foreach ($order->getInvoiceCollection() as $previousInvoice) {
            if ($previousInvoice->getTruwalletDiscount()) {
                $checkAddShipping = false;
                $totalBaseDiscountInvoiced += $previousInvoice->getBaseTruwalletDiscount();
                $totalDiscountInvoiced += $previousInvoice->getTruwalletDiscount();

                $hiddenTaxInvoiced += $previousInvoice->getTruwalletHiddenTax();
                $baseHiddenTaxInvoiced += $previousInvoice->getBaseTruwalletHiddenTax();
            }

            if ($previousInvoice->getTrugiftcardDiscount()) {
                $checkAddShippingTrugiftcard = false;
                $totalBaseDiscountInvoicedTrugiftcard += $previousInvoice->getBaseTrugiftcardDiscount();
                $totalDiscountInvoicedTrugiftcard += $previousInvoice->getTrugiftcardDiscount();

                $hiddenTaxInvoicedTrugiftcard += $previousInvoice->getTrugiftcardHiddenTax();
                $baseHiddenTaxInvoicedTrugiftcard += $previousInvoice->getBaseTrugiftcardHiddenTax();
            }
        }

        if ($checkAddShipping) {
            $totalBaseDiscountAmount += $order->getBaseTruwalletDiscountForShipping();
            $totalDiscountAmount += $order->getTruwalletDiscountForShipping();

            $totalBaseHiddenTax += $order->getBaseTruwalletShippingHiddenTax();
            $totalHiddenTax += $order->getTruwalletShippingHiddenTax();
        }

        if ($checkAddShippingTrugiftcard) {
            $totalBaseDiscountAmountTrugiftcard += $order->getBaseTrugiftcardDiscountForShipping();
            $totalDiscountAmountTrugiftcard += $order->getTrugiftcardDiscountForShipping();

            $totalBaseHiddenTaxTrugiftcard += $order->getBaseTrugiftcardShippingHiddenTax();
            $totalHiddenTaxTrugiftcard += $order->getTrugiftcardShippingHiddenTax();
        }

        zend_debug::dump($invoice->isLast());
        zend_debug::dump($totalBaseDiscountInvoiced);
        zend_debug::dump($invoice->debug());
        if ($invoice->isLast()) {
            $totalBaseDiscountAmount = $order->getBaseTruwalletDiscount() - $totalBaseDiscountInvoiced;
            $totalDiscountAmount = $order->getTruwalletDiscount() - $totalDiscountInvoiced;

            $totalHiddenTax = $order->getTruwalletHiddenTax() - $hiddenTaxInvoiced;
            $totalBaseHiddenTax = $order->getBaseTruwalletHiddenTax() - $baseHiddenTaxInvoiced;

            $totalBaseDiscountAmountTrugiftcard = $order->getBaseTrugiftcardDiscount() - $totalBaseDiscountInvoicedTrugiftcard;
            $totalDiscountAmountTrugiftcard = $order->getTrugiftcardDiscount() - $totalDiscountInvoicedTrugiftcard;

            $totalHiddenTaxTrugiftcard = $order->getTrugiftcardHiddenTax() - $hiddenTaxInvoicedTrugiftcard;
            $totalBaseHiddenTaxTrugiftcard = $order->getBaseTrugiftcardHiddenTax() - $baseHiddenTaxInvoicedTrugiftcard;
        } else {
            zend_debug::dump(sizeof($invoice->getAllItems()));
            foreach ($invoice->getAllItems() as $item) {
                $orderItem = $item->getOrderItem();
                if ($orderItem->isDummy()) {
                    continue;
                }
                $baseOrderItemTruwalletDiscount = (float) $orderItem->getBaseTruwalletDiscount();
                $orderItemTruwalletDiscount = (float) $orderItem->getTruwalletDiscount();

                $baseOrderItemHiddenTax = (float) $orderItem->getBaseTruwalletHiddenTax();
                $orderItemHiddenTax = (float) $orderItem->getTruwalletHiddenTax();

                $orderItemQty = $orderItem->getQtyOrdered();
                $invoiceItemQty = $item->getQty();

                zend_debug::dump($item->debug());
                zend_debug::dump($baseOrderItemTruwalletDiscount);
                if ($baseOrderItemTruwalletDiscount && $orderItemQty) {
                    if (version_compare(Mage::getVersion(), '1.7.0.0', '>=')) {
                        $totalBaseDiscountAmount += $invoice->roundPrice($baseOrderItemTruwalletDiscount / $orderItemQty * $invoiceItemQty, 'base', true);
                        $totalDiscountAmount += $invoice->roundPrice($orderItemTruwalletDiscount / $orderItemQty * $invoiceItemQty, 'regular', true);

                        $totalHiddenTax += $invoice->roundPrice($orderItemHiddenTax / $orderItemQty * $invoiceItemQty, 'regular', true);
                        $totalBaseHiddenTax += $invoice->roundPrice($baseOrderItemHiddenTax / $orderItemQty * $invoiceItemQty, 'base', true);
                    } else {
                        $totalBaseDiscountAmount += $baseOrderItemTruwalletDiscount / $orderItemQty * $invoiceItemQty;
                        $totalDiscountAmount += $orderItemTruwalletDiscount / $orderItemQty * $invoiceItemQty;

                        $totalHiddenTax += $orderItemHiddenTax / $orderItemQty * $invoiceItemQty;
                        $totalBaseHiddenTax += $baseOrderItemHiddenTax / $orderItemQty * $invoiceItemQty;
                    }
                }

                /* calculate Trunited Gift Card */
                $baseOrderItemTrugiftcardDiscountTrugiftcard = (float) $orderItem->getBaseTrugiftcardDiscount();
                $orderItemTrugiftcardDiscountTrugiftcard = (float) $orderItem->getTrugiftcardDiscount();

                $baseOrderItemHiddenTaxTrugiftcard = (float) $orderItem->getBaseTrugiftcardHiddenTax();
                $orderItemHiddenTaxTrugiftcard = (float) $orderItem->getTrugiftcardHiddenTax();

                if ($baseOrderItemTrugiftcardDiscountTrugiftcard && $orderItemQty) {
                    if (version_compare(Mage::getVersion(), '1.7.0.0', '>=')) {
                        $totalBaseDiscountAmountTrugiftcard += $invoice->roundPrice($baseOrderItemTrugiftcardDiscountTrugiftcard / $orderItemQty * $invoiceItemQty, 'base', true);
                        $totalDiscountAmountTrugiftcard += $invoice->roundPrice($orderItemTrugiftcardDiscountTrugiftcard / $orderItemQty * $invoiceItemQty, 'regular', true);

                        $totalHiddenTaxTrugiftcard += $invoice->roundPrice($orderItemHiddenTax / $orderItemQty * $invoiceItemQty, 'regular', true);
                        $totalBaseHiddenTaxTrugiftcard += $invoice->roundPrice($baseOrderItemHiddenTax / $orderItemQty * $invoiceItemQty, 'base', true);
                    } else {
                        $totalBaseDiscountAmountTrugiftcard += $baseOrderItemTrugiftcardDiscountTrugiftcard / $orderItemQty * $invoiceItemQty;
                        $totalDiscountAmountTrugiftcard += $orderItemTrugiftcardDiscountTrugiftcard / $orderItemQty * $invoiceItemQty;

                        $totalHiddenTaxTrugiftcard += $orderItemHiddenTax / $orderItemQty * $invoiceItemQty;
                        $totalBaseHiddenTaxTrugiftcard += $baseOrderItemHiddenTax / $orderItemQty * $invoiceItemQty;
                    }
                }
                /* END calculate Trunited Gift Card */
            }
        }
        $invoice->setBaseTruwalletDiscount($totalBaseDiscountAmount);
        $invoice->setTruwalletDiscount($totalDiscountAmount);

        $invoice->setBaseTruwalletHiddenTax($totalBaseHiddenTax);
        $invoice->setTruwalletHiddenTax($totalHiddenTax);

        $invoice->setBaseTrugiftcardDiscount($totalBaseDiscountAmountTrugiftcard);
        $invoice->setTrugiftcardDiscount($totalDiscountAmountTrugiftcard);

        $invoice->setBaseTrugiftcardHiddenTax($totalBaseHiddenTaxTrugiftcard);
        $invoice->setTrugiftcardHiddenTax($totalHiddenTaxTrugiftcard);

        // if($_SERVER['REMOTE_ADDR'] == '42.112.152.68'){
        //     zend_debug::dump($totalBaseDiscountAmount);
        //     zend_debug::dump($totalBaseHiddenTax);
        //     var_dump('aaaaa');

        //     exit;
        // }
        exit;
        $invoice->setBaseGrandTotal($invoice->getBaseGrandTotal() - $totalBaseDiscountAmount - $totalBaseDiscountAmountTrugiftcard + $totalBaseHiddenTax + $totalBaseHiddenTaxTrugiftcard);
        $invoice->setGrandTotal($invoice->getGrandTotal() - $totalDiscountAmount - $totalDiscountAmountTrugiftcard + $totalHiddenTax + $totalHiddenTaxTrugiftcard);
    }

}
