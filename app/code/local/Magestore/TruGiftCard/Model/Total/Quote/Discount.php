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
 * TruGiftCard Model
 *
 * @category    Magestore
 * @package     Magestore_TruGiftCard
 * @author      Magestore Developer
 */
class Magestore_TruGiftCard_Model_Total_Quote_Discount extends Mage_Sales_Model_Quote_Address_Total_Abstract
{

    /**
     * Magestore_TruGiftCard_Model_Total_Quote_Discount constructor.
     */
    public function __construct()
    {
        $this->setCode('trugiftcard_after_tax');
    }

    /**
     * @param Mage_Sales_Model_Quote_Address $address
     * @return $this
     */
    public function fetch(Mage_Sales_Model_Quote_Address $address)
    {
        $quote = $address->getQuote();
        if (Mage::getStoreConfig('trugiftcard/spend/tax', $quote->getStoreId()) == 0) {
            return $this;
        }
        if (!$quote->isVirtual() && $address->getData('address_type') == 'billing')
            return $this;
        $session = Mage::getSingleton('checkout/session');
        $customer_credit_discount = $address->getTruGiftCardDiscount();
        if ($session->getBaseTrugiftcardCreditAmount())
            $customer_credit_discount = $session->getBaseTrugiftcardCreditAmount();
        if ($customer_credit_discount > 0) {
            $address->addTotal(array(
                'code' => $this->getCode(),
                'title' => Mage::helper('trugiftcard')->getSpendConfig('discount_label'),
                'value' => -Mage::helper('core')->currency($customer_credit_discount, false, false)
            ));
        }

        return $this;
    }
}