<?php

class Magestore_TruGiftCard_Helper_Data extends Mage_Core_Helper_Abstract
{
    const XML_PATH_ENABLE = 'trugiftcard/general/enable';

    public function isEnable($store = null)
    {
        return Mage::getStoreConfigFlag(self::XML_PATH_ENABLE, $store);
    }

    public function isEnableModule(){
        return Mage::helper('core')->isModuleOutputEnabled('Magestore_TruGiftCard');
    }

    public function getMyTruGiftCardLabel()
    {
        $image = '<img src="'.Mage::getDesign()->getSkinUrl('images/trugiftcard/point.png').'" />';
        return $this->__('My Trunited Gift Card') . ' ' . $image;
    }

    public function getShareTruGiftCardLabel()
    {
        $image = '<img src="'.Mage::getDesign()->getSkinUrl('images/trugiftcard/point.png').'" />';
        return $this->__('Share Trunited Gift Card Money') . ' ' . $image;
    }

    public function formatTrugiftcard($credit)
    {
        return Mage::helper('core')->currency($credit, true, false);
    }

    public function getSpendConfig($code, $store = null)
    {
        return Mage::getStoreConfig('trugiftcard/spending/' . $code, $store);
    }

    public function getWarningMessage($store = null)
    {
        return Mage::getStoreConfig('trugiftcard/general/warning_message', $store);
    }

    public function isEnableTruGiftCardProduct($store = null)
    {
        return Mage::getStoreConfig('trugiftcard/product/enable', $store);
    }

    public function getTruGiftCardOrderStatus($store = null)
    {
        return Mage::getStoreConfig('trugiftcard/product/order_status', $store);
    }

    public function getTruGiftCardSku($store = null)
    {
        return Mage::getStoreConfig('trugiftcard/product/trugiftcard_sku', $store);
    }

    public function getTruGiftCardValue($store = null)
    {
        return Mage::getStoreConfig('trugiftcard/product/trugiftcard_value', $store);
    }

    public function getEnableChangeBalance($store = null)
    {
        return Mage::getStoreConfig('trugiftcard/general/enable_change_balance', $store);
    }

    public function getTruGiftCardPaymentEnable($store = null)
    {
        return Mage::getStoreConfig('trugiftcard/trugiftcard_payment/enable', $store);
    }

    public function getTruGiftCardPayment($store = null)
    {
        return Mage::getStoreConfig('trugiftcard/trugiftcard_payment/payment', $store);
    }

    public function getTruGiftCardOrderAmount($store = null)
    {
        return Mage::getStoreConfig('trugiftcard/trugiftcard_payment/order_amount', $store);
    }

    public function getTruGiftCardPaymentPoint($store = null)
    {
        return Mage::getStoreConfig('trugiftcard/trugiftcard_payment/reward_point', $store);
    }

    public function getEnableTransferBonus($store = null)
    {
        return Mage::getStoreConfig('trugiftcard/transfer/enable', $store);
    }

    public function getTransferBonus($store = null)
    {
        return Mage::getStoreConfig('trugiftcard/transfer/bonus', $store);
    }

    public function getMessageTransferBonus($store = null)
    {
        return Mage::getStoreConfig('trugiftcard/transfer/message', $store);
    }


    public function isShowWarningMessage()
    {
        if(Mage::helper('core')->isModuleOutputEnabled('Magestore_TruBox'))
        {
            $truBoxCollection = Mage::helper('trubox')->getCurrentTruBoxCollection();
            if(sizeof($truBoxCollection) <= 0)
                return false;

            $totalPrice = 0;
            foreach ($truBoxCollection as $item) {
                $product = Mage::getModel('catalog/product')->load($item->getProductId());
                $option_params = json_decode($item->getOptionParams(), true);
                $price_options = 0;

                if($product->getTypeId() != 'configurable')
                {
                    foreach ($product->getOptions() as $o)
                    {
                        $values = $o->getValues();
                        $_attribute_value = 0;

                        foreach($option_params as $k=>$v)
                        {
                            if($k == $o->getOptionId())
                            {
                                $_attribute_value = $v;
                                break;
                            }
                        }
                        if($_attribute_value > 0)
                        {
                            foreach ($values as $val) {
                                if(is_array($_attribute_value)){
                                    if(in_array($val->getOptionTypeId(), $_attribute_value)) {
                                        echo $val->getTitle().' ';
                                        $price_options += $val->getPrice();

                                    }
                                } else if($val->getOptionTypeId() == $_attribute_value){
                                    echo $val->getTitle().' ';
                                    $price_options += $val->getPrice();
                                }
                            }
                        }
                    }
                }

                $itemPrice = ($product->getFinalPrice() + $price_options) * $item->getQty();
                $totalPrice += $itemPrice;
            }

            if($totalPrice == 0)
                return false;

            $current_truGiftCard_balance = Mage::helper('trugiftcard/account')->getTruGiftCardCredit(false);
            if($current_truGiftCard_balance == null)
                return false;

            if($current_truGiftCard_balance < $totalPrice)
                return true;
            else
                return false;
        } else {
            return false;
        }


    }

    public function isShowTrunitedDiscountSection()
    {
        if(!Mage::getSingleton('customer/session')->isLoggedIn())
            return false;

        if(!$this->isEnable() && !Mage::helper('truwallet')->isEnable())
            return false;

        $account = Mage::helper('trugiftcard/account')->loadByCustomerId(Mage::getSingleton('customer/session')->getCustomer()->getId());
        $account_truWallet = Mage::helper('truwallet/account')->loadByCustomerId(Mage::getSingleton('customer/session')->getCustomer()->getId());

        if(!isset($account) && !isset($account_truWallet))
            return false;

        if ($account->getTrugiftcardCredit() == 0 && $account_truWallet->getTruwalletCredit() == 0)
            return false;

        return true;
    }

    public function isShowTruWallet()
    {
        if(!Mage::getSingleton('customer/session')->isLoggedIn())
            return false;

        if(!Mage::helper('truwallet')->isEnable())
            return false;

        $account_truWallet = Mage::helper('truwallet/account')->loadByCustomerId(Mage::getSingleton('customer/session')->getCustomer()->getId());

        if(!isset($account_truWallet))
            return false;

        if ($account_truWallet->getTruwalletCredit() == 0)
            return false;

        return true;
    }

    public function isShowTruGiftCard()
    {
        $session = Mage::getSingleton('checkout/session');
        if(!Mage::getSingleton('customer/session')->isLoggedIn())
            return false;

        if(!$this->isEnable())
            return false;

        $account = Mage::helper('trugiftcard/account')->loadByCustomerId(Mage::getSingleton('customer/session')->getCustomer()->getId());

        if(!isset($account))
            return false;

//        if($session->getBaseTruwalletCreditAmount() >= (Mage::getModel('checkout/session')->getQuote()->getGrandTotal() + $wrapTotal))
//        if(Mage::getModel('checkout/session')->getQuote()->getGrandTotal() <= 0)
//            return false;

        if ($account->getTrugiftcardCredit() == 0)
            return false;

        return true;
    }

}
