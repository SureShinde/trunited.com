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
 * @package     Magestore_TruBox
 * @module      TruBox
 * @author      Magestore Developer
 *
 * @copyright   Copyright (c) 2016 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 *
 */

/**
 * TruBox Helper
 *
 * @category    Magestore
 * @package     Magestore_TruBox
 * @module      TruBox
 * @author      Magestore Developer
 */
class Magestore_TruBox_Helper_Data extends Mage_Core_Helper_Abstract
{
    const XML_PATH_ENABLE = 'rewardpoints/general/enable';

    public function getEnableTestingMode()
    {
        return Mage::getStoreConfig('trubox/general/enable_testing_mode');
    }

    public function getEmailTestingMode()
    {
        return Mage::getStoreConfig('trubox/general/email_testing_mode');
    }

    public function isEnableOrdersSection()
    {
        return Mage::getStoreConfig('trubox/general/enable_orders_section');
    }

    public function getProductExclusionList()
    {
        return Mage::getStoreConfig('trubox/general/product_exclusion_list');
    }

    public function getEnableProductListing()
    {
        return Mage::getStoreConfig('trubox/general/enable_product_listing');
    }

    public function getShippingMethod($store = null)
    {
        return Mage::getStoreConfig('trubox/shipping/shipping_method', $store);
    }

    public function getShippingAmount($store = null)
    {
        return Mage::getStoreConfig('trubox/shipping/shipping_amount', $store);
    }

    public function getPhysicalOnlyProduct()
    {
        return Mage::getStoreConfig('trubox/general/physical_only_product');
    }

    public function isEnableCouponCode()
    {
        return Mage::getStoreConfig('trubox/general/enable_coupon_code');
    }

    public function getCouponCode()
    {
        return Mage::getStoreConfig('trubox/general/coupon_code');
    }

    public function getStartCouponCode()
    {
        return Mage::getStoreConfig('trubox/general/start_date_code');
    }

    public function getEndCouponCode()
    {
        return Mage::getStoreConfig('trubox/general/end_date_code');
    }

    public function getTypeCouponCode()
    {
        return Mage::getStoreConfig('trubox/general/type_code');
    }

    public function getAmountCouponCode()
    {
        return Mage::getStoreConfig('trubox/general/coupon_code_amount');
    }

    public function getCmsWhatIsTruBox()
    {
        return Mage::getStoreConfig('trubox/general/cms_trubox');
    }

    public function getDelaySecond()
    {
        return Mage::getStoreConfig('trubox/general/delay_second') != null ? Mage::getStoreConfig('trubox/general/delay_second') : 1;
    }

    public function getExclusionList()
    {
        $list = $this->getProductExclusionList();
        $result = array();
        if ($list != null) {
            $data = explode(',', $list);
            foreach ($data as $sku) {
                $result[] = trim(strtolower($sku));
            }
        }

        return $result;

    }

    public function isInExclusionList($product)
    {

        $product_exclusion = $this->getExclusionList();

        if (sizeof($product_exclusion) == 0)
            return false;
        else {
            if (in_array(strtolower(trim($product->getSku())), $product_exclusion))
                return true;
            else
                return false;
        }
    }

    public function getPhysicalList()
    {
        $list = $this->getPhysicalOnlyProduct();
        $result = array();
        if ($list != null) {
            $data = explode(',', $list);
            foreach ($data as $sku) {
                $result[] = trim(strtolower($sku));
            }
        }

        return $result;

    }

    public function isInPhysicalList($product)
    {

        $product_exclusion = $this->getPhysicalList();

        if (sizeof($product_exclusion) == 0)
            return false;
        else {
            if (in_array(strtolower(trim($product->getSku())), $product_exclusion))
                return true;
            else
                return false;
        }
    }

    public function isCurrentCheckingCustomer()
    {
        $enable_test_mode = $this->getEnableTestingMode();
        $email_test_mode = $this->getEmailTestingMode();
        $customer = Mage::getSingleton('customer/session')->getCustomer();
        if ($enable_test_mode) {
            if ($customer->getEmail() == $email_test_mode)
                return true;
            else
                return false;
        } else
            return true;
    }

    /**
     *
     * @return string
     */
    public function getTruboxLabel()
    {
        return $this->__('My TruBox');
    }

    public function getCurrentTruBox()
    {
        $truBox_id = $this->getCurrentTruBoxId();
        $truBox = Mage::getModel('trubox/trubox')->load($truBox_id);
        if ($truBox != null && $truBox->getId())
            return $truBox;
        else
            return null;
    }

    public function getCurrentTruBoxId($customer_id = null)
    {
        if ($customer_id == null)
            $customer_id = Mage::getSingleton('customer/session')->getCustomer()->getId();

        $truBox = Mage::getModel('trubox/trubox')->getCollection()
            ->addFieldToFilter('status', 'open')
            ->addFieldToFilter('customer_id', $customer_id)
            ->getFirstItem();

        if ($truBox->getId()) {
            $truBoxId = $truBox->getTruboxId();
        } else {
            $_truBox = Mage::getModel('trubox/trubox');
            $_truBox->setData('customer_id', $customer_id);
            $_truBox->setData('status', 'open');
            $_truBox->setData('created_at', now());
            $_truBox->setData('updated_at', now());
            $_truBox->save();
            $truBoxId = $_truBox->getId();
        }

        return $truBoxId;
    }

    public function getCurrentTruBoxCollection()
    {
        $truBoxId = $this->getCurrentTruBoxId();
        $collection = Mage::getModel('trubox/item')
            ->getCollection()
            ->addFieldToFilter('trubox_id', $truBoxId)
            ->setOrder('item_id', 'desc');
        return $collection;
    }

    public function firstCheckAddress()
    {
        $truBoxId = $this->getCurrentTruBoxId();
        $truBox_billing = Mage::getModel('trubox/address')->getCollection()
            ->addFieldToFilter('trubox_id', $truBoxId)
            ->addFieldToFilter('address_type', Magestore_TruBox_Model_Address::ADDRESS_TYPE_BILLING)
            ->getFirstItem();

        $customer = Mage::getModel('customer/customer')->load(Mage::getSingleton('customer/session')->getCustomer()->getId());
        if ($truBox_billing->getId() == null) {
            $customer_billing = $customer->getDefaultBillingAddress();
            if ($customer_billing != null && $customer_billing->getId()) {
                $_billing = Mage::getModel('trubox/address');
                $_billing->setData('address_type', Magestore_TruBox_Model_Address::ADDRESS_TYPE_BILLING);
                $_billing->setData('trubox_id', $truBoxId);
                $_billing->setData('firstname', $customer_billing->getFirstname());
                $_billing->setData('lastname', $customer_billing->getLastname());
                $_billing->setData('company', '');
                $_billing->setData('telephone', $customer_billing->getTelephone());
                $_billing->setData('fax', '');
                $_billing->setData('street', $customer_billing['street']);
                $_billing->setData('region_id', $customer_billing->getRegionId());
                $_billing->setData('region', $customer_billing->getRegion());
                $_billing->setData('city', $customer_billing->getCity());
                $_billing->setData('zipcode', $customer_billing->getPostcode());
                $_billing->setData('country', $customer_billing->getCountryId());
                $_billing->setData('created_at', now());
                $_billing->setData('updated_at', now());
                $_billing->save();
            }
        }

        $truBox_shipping = Mage::getModel('trubox/address')->getCollection()
            ->addFieldToFilter('trubox_id', $truBoxId)
            ->addFieldToFilter('address_type', Magestore_TruBox_Model_Address::ADDRESS_TYPE_SHIPPING)
            ->getFirstItem();

        if ($truBox_shipping->getId() == null) {
            $customer_shipping = $customer->getDefaultShippingAddress();

            if ($customer_shipping != null && $customer_shipping->getId()) {
                $_shipping = Mage::getModel('trubox/address');
                $_shipping->setData('address_type', Magestore_TruBox_Model_Address::ADDRESS_TYPE_SHIPPING);
                $_shipping->setData('trubox_id', $truBoxId);
                $_shipping->setData('firstname', $customer_shipping->getFirstname());
                $_shipping->setData('lastname', $customer_shipping->getLastname());
                $_shipping->setData('company', '');
                $_shipping->setData('telephone', $customer_shipping->getTelephone());
                $_shipping->setData('fax', '');
                $_shipping->setData('street', $customer_shipping['street']);
                $_shipping->setData('region_id', $customer_shipping->getRegionId());
                $_shipping->setData('region', $customer_shipping->getRegion());
                $_shipping->setData('city', $customer_shipping->getCity());
                $_shipping->setData('zipcode', $customer_shipping->getPostcode());
                $_shipping->setData('country', $customer_shipping->getCountryId());
                $_shipping->setData('created_at', now());
                $_shipping->setData('updated_at', now());
                $_shipping->save();
            }
        }


    }

    public function getConfigurableOptionProduct($product)
    {
        if ($product->getTypeId() == 'configurable')
            return $product->getTypeInstance(true)->getConfigurableAttributesAsArray($product);
        else
            return array();
    }

    public function getOrdersByCustomer()
    {
        return Mage::getResourceModel('sales/order_collection')
            ->addFieldToSelect('*')
            ->addFieldToFilter('customer_id', Mage::getSingleton('customer/session')->getCustomer()->getId())
            ->addFieldToFilter('onestepcheckout_giftwrap_amount', 0)
            ->setOrder('created_at', 'desc');
    }

    public function checkOrderFromTruBox($customer_id)
    {
        $order_collection = Mage::getModel('sales/order')->getCollection()
            ->addFieldToFilter('state', array('in' => Mage::getSingleton('sales/order_config')->getVisibleOnFrontStates()))
            ->addFieldToFilter('status', array('in' => array(
                Mage_Sales_Model_Order::STATE_COMPLETE,
                Mage_Sales_Model_Order::STATE_PROCESSING,
            )))
            ->addAttributeToFilter('customer_id', $customer_id)
            ->addAttributeToFilter('created_by', Magestore_TruBox_Model_Status::ORDER_CREATED_BY_ADMIN_YES)
            ->getFirstItem();

        if (isset($order_collection) && $order_collection->getId())
            return true;
        else
            return false;
    }

    public function hasSaveCode($customer_id)
    {
        $customer = Mage::getModel('customer/customer')->load($customer_id);
        if (!isset($customer) || !$customer->getId())
            return null;

        $coupon_collection = Mage::getModel('trubox/coupon')->getCollection()
            ->addFieldToFilter('customer_id', $customer_id)
            ->addFieldToFilter('status', Magestore_TruBox_Model_Status::COUPON_CODE_STATUS_PENDING)
            ->getFirstItem();

        if ($coupon_collection->getId())
            return $coupon_collection;
        else
            return null;
    }

    public function synchronizeCIM()
    {
        $trubox_payment = Mage::getModel('trubox/payment')->getCollection()
            ->addFieldToFilter('payment_id', array('lteq' => 300))
            ->addFieldToFilter('payment_id', array('gteq' => 201))
            ->setOrder('payment_id', 'desc')
        ;
        echo $trubox_payment->getSelect();
        $start_time = time();
//        $trubox_payment->getSelect()->limit(1);

        $count = 0;
        if (sizeof($trubox_payment) > 0) {
            $method = 'authnetcim';
            $transactionSave = Mage::getModel('core/resource_transaction');
            foreach ($trubox_payment as $tp) {
                $trubox = Mage::getModel('trubox/trubox')->load($tp->getTruboxId());
                if ($trubox != null && $trubox->getId()) {
                    $customer = Mage::getModel('customer/customer')->load($trubox->getCustomerId());
                    if ($customer != null && $customer->getId()) {
                        $card = Mage::getModel($method . '/card');
                        $billing_address = $this->getBillingAddressTruBox($tp->getTruboxId());
                        if ($billing_address != null && $tp->getCardNumber() != null && is_numeric($tp->getCardNumber()) &&
                            ($tp->getYearExpire() > date('Y', time()) || ($tp->getYearExpire() == date('Y', time()) && $tp->getMonthExpire() >= date('m', time()))))
                        {
                            $billing_data = array(
                                'firstname' => $billing_address->getFirstname(),
                                'lastname' => $billing_address->getLastname(),
                                'company' => $billing_address->getCompany(),
                                'street' => array($billing_address->getStreet()),
                                'city' => $billing_address->getCity(),
                                'region_id' => $billing_address->getRegionId(),
                                'region' => $billing_address->getRegion(),
                                'postcode' => $billing_address->getZipcode(),
                                'country_id' => $billing_address->getCountry(),
                                'telephone' => $billing_address->getTelephone(),
                                'fax' => $billing_address->getFax(),
                            );

                            try{
                                $newAddr = Mage::getModel('customer/address');
                                $newAddr->setCustomerId($customer->getId());

                                $addressForm = Mage::getModel('customer/form');
                                $addressForm->setFormCode('customer_address_edit');
                                $addressForm->setEntity($newAddr);

                                $addressData = $addressForm->extractData($addressForm->prepareRequest($billing_data));
                                $addressErrors = $addressForm->validateData($addressData);

                                if ($addressErrors !== true) {
                                    Mage::throwException(implode(' ', $addressErrors));
                                }

                                $addressForm->compactData($addressData);
                                $newAddr->validate();

                                $newAddr->setSaveInAddressBook(false);
                                $newAddr->implodeStreetAddress();

                                $payment_data = array(
                                    'cc_type' => $tp->getCardType(),
                                    'cc_number' => $tp->getCardNumber(),
                                    'cc_exp_month' => $tp->getMonthExpire(),
                                    'cc_exp_year' => $tp->getYearExpire(),
                                    'cc_cid' => $tp->getCvv(),
                                    'method' => 'authnetcim',
                                    'card_id' => null,
                                    'cc_last4' => substr($tp->getCardNumber(), -4)
                                );

                                $newPayment = Mage::getModel('sales/quote_payment');
                                $newPayment->setQuote(Mage::getSingleton('checkout/session')->getQuote());
                                $newPayment->getQuote()->getBillingAddress()->setCountryId( $newAddr->getCountryId());
                                $newPayment->importData($payment_data);

                                $card->setMethod($method);
                                $card->setActive(1);
                                $card->setCustomer($customer);
                                $card->setAddress($newAddr);
                                $card->importPaymentInfo($newPayment);

                                $transactionSave->addObject($card);
                                $count++;
                                Mage::getSingleton('customer/session')->unsTokenbaseFormData();
                            } catch (Exception $ex) {
                                zend_debug::dump($billing_data);
                                zend_debug::dump($payment_data);
                                zend_debug::dump($customer->getId());
                                zend_debug::dump($tp->getId());
                                zend_debug::dump($ex->getMessage());
                                exit;
                            }
                        } else {
                            zend_debug::dump($tp->getId().' - Dont have met the conditions');
                        }
                    } else {
                        zend_debug::dump($tp->getId().' - Dont have customer account');
                    }
                } else {
                    zend_debug::dump($tp->getId().' - Dont have TruBox account');
                }
            }
            $transactionSave->save();
            $end_time = time();
            zend_debug::dump($count);
            zend_debug::dump('Duration: '.($end_time - $start_time));
            echo 'success';
        }
    }

    public function getBillingAddressTruBox($trubox_id)
    {
        $collection = Mage::getModel('trubox/address')->getCollection()
            ->addFieldToFilter('address_type', Magestore_TruBox_Model_Address::ADDRESS_TYPE_BILLING)
            ->addFieldToFilter('trubox_id', $trubox_id)
            ->getFirstItem();

        if ($collection != null && $collection->getId())
            return $collection;
        else
            return null;
    }

    public function calculatePointsCost($data)
    {
        $result = array();
        if($this->isShowCurrentMonth()){
            $result['first'] = $this->getDataCurrentMonthAjax($data);
            $result['second'] = $this->getDataNextMonthAjax($data, 1);
            $result['third'] = $this->getDataNextMonthAjax($data, 2);
        } else {
            $result['first'] = $this->getDataNextMonthAjax($data, 1);
            $result['second'] = $this->getDataNextMonthAjax($data, 2);
            $result['third'] = $this->getDataNextMonthAjax($data, 3);
        }

        return $result;
    }

    public function getCurrentCustomer()
    {
        return Mage::getModel('customer/customer')->load(Mage::getSingleton('customer/session')->getCustomer()->getId());
    }

    public function getShippingAddressTruBox()
    {
        $truBoxId = Mage::helper('trubox')->getCurrentTruBoxId();
        $truBoxFilter = Mage::getModel('trubox/address')->getCollection()
            ->addFieldToFilter('trubox_id', $truBoxId)
            ->addFieldToFilter('address_type', Magestore_TruBox_Model_Address::ADDRESS_TYPE_SHIPPING)
            ->getFirstItem();

        if ($truBoxFilter->getId() != null)
            return $truBoxFilter;
        else
            return $this->getCurrentCustomer()->getDefaultShippingAddress();
    }

    public function getRate($product)
    {
        $productTaxId = $product->getData("tax_class_id");
        $customer = $this->getCurrentCustomer();

        $shippingAddress = $this->getShippingAddressTruBox();
        $rate = 0;

        if($shippingAddress != null && $shippingAddress->getId())
        {
            $country = $shippingAddress->getCountry();
            $region = $shippingAddress->getRegionId();
            $postcode = $shippingAddress->getZipcode();
            $customerTaxId = $customer->getTaxClassId();

            $TaxRequest = new Varien_Object();
            $TaxRequest->setCountryId($country);
            $TaxRequest->setRegionId($region);
            $TaxRequest->setPostcode($postcode);
            $TaxRequest->setStore(Mage::app()->getStore());
            $TaxRequest->setCustomerClassId($customerTaxId);
            $TaxRequest->setProductClassId($productTaxId);

            $taxCalculationModel = Mage::getSingleton('tax/calculation');
            $rate = $taxCalculationModel->getRate($TaxRequest);
        }


        return $rate;
    }

    public function getTaxAmount($product, $qty)
    {
        $percent = $this->getRate($product);
        return ($product->getFinalPrice() * $percent * $qty) / 100;
    }

    public function getPointEarning($item)
    {
        if (!Mage::helper('rewardpointsrule')->isEnabled()) {
            return false;
        }

        $item->setProductId($item->getId());
        if ($item->getRewardpointsEarn()) {
            return $item->getRewardpointsEarn();
        }
        return Mage::helper('rewardpointsrule/calculation_earning')
            ->getCatalogItemEarningPoints($item);
    }

    public function isShowCurrentMonth()
    {
        $start_month = Mage::getStoreConfig('trubox/general/start_month');
        return date('d', time()) <= $start_month ? true : false;
    }

    public function getDataCurrentMonth()
    {
        $collection = $this->getCurrentTruBoxCollection();
        $data = array(
            'points' => 0,
            'cost'  => 0
        );

        $start_month = Mage::getStoreConfig('trubox/general/start_month');
        $is_odd = $start_month % 2 == 0 ? true : false;
        if(sizeof($collection) > 0) {
            foreach ($collection as $item) {
                if(($item->getTypeItem() == Magestore_TruBox_Model_Type::TYPE_ONE_TIME)
                    || $item->getTypeItem() == Magestore_TruBox_Model_Type::TYPE_EVERY_MONTH
                    ||($item->getTypeItem() == Magestore_TruBox_Model_Type::TYPE_EVERY_TWO_MONTHS && (($is_odd && date('m', time()) % 2 == 0) || (!$is_odd && date('m', time()) % 2 != 0)))
                ){
                    $product = Mage::getModel('catalog/product')->load($item->getProductId());
                    $pointEarn = $this->getPointEarning($product);
                    $option_params = json_decode($item->getOptionParams(), true);

                    $price_options = 0;
                    if(sizeof($product->getOptions()) > 0){
                        foreach ($product->getOptions() as $o) {
                            $values = $o->getValues();
                            $_attribute_value = 0;

                            foreach ($option_params as $k => $v) {
                                if ($k == $o->getOptionId()) {
                                    $_attribute_value = $v;
                                    break;
                                }
                            }

                            if ($_attribute_value > 0) {
                                foreach ($values as $val) {
                                    if (is_array($_attribute_value)) {
                                        if (in_array($val->getOptionTypeId(), $_attribute_value)) {
                                            $price_options += $val->getPrice();

                                        }
                                    } else if ($val->getOptionTypeId() == $_attribute_value) {
                                        $price_options += $val->getPrice();
                                    }
                                }
                            }
                        }
                    }

                    $itemPrice = ($product->getFinalPrice() + $price_options) * $item->getQty();
                    $item_tax_amount = $this->getTaxAmount($product, $item->getQty());
                    if ($product->getStockItem()->getIsInStock()) {
                        $flag = false;
                        if ($product->getTypeId() == 'configurable') {
                            $main_child_product = Mage::getModel('catalog/product_type_configurable')
                                ->getProductByAttributes($option_params, $product)->getId();
                            $childProducts = Mage::getModel('catalog/product_type_configurable')->getUsedProducts(null, $product);
                            foreach ($childProducts as $childProduct) {
                                $qty = Mage::getModel('cataloginventory/stock_item')->loadByProduct($childProduct)->getQty();
                                if ($childProduct->getId() == $main_child_product) {
                                    if ($qty <= 0) {
                                        $flag = true;
                                    }
                                    break;
                                }
                            }
                        }

                        if (!$flag){
                            $data['cost'] += $itemPrice + $item_tax_amount;
                            $data['points'] += $pointEarn * $item->getQty();
                        }
                    }
                }
            }

        }

        return $data;
    }

    public function getDataNextMonth($month)
    {
        $collection = $this->getCurrentTruBoxCollection();
        $data = array(
            'points' => 0,
            'cost'  => 0
        );

        $start_month = Mage::getStoreConfig('trubox/general/start_month');
        $is_odd = $start_month % 2 == 0 ? true : false;
        if($month == 1)
            $next_month = strtotime("first day of +$month month");
        else if ($month > 1)
            $next_month = strtotime("first day of +$month months");

        if(sizeof($collection) > 0) {
            foreach ($collection as $item) {
                if(($item->getTypeItem() == Magestore_TruBox_Model_Type::TYPE_ONE_TIME && $month == 1)
                    || $item->getTypeItem() == Magestore_TruBox_Model_Type::TYPE_EVERY_MONTH
                    ||($item->getTypeItem() == Magestore_TruBox_Model_Type::TYPE_EVERY_TWO_MONTHS && (($is_odd && date('m', $next_month) % 2 == 0) || (!$is_odd && date('m', $next_month) % 2 != 0)))
                ){
                    $product = Mage::getModel('catalog/product')->load($item->getProductId());
                    $pointEarn = $this->getPointEarning($product);
                    $option_params = json_decode($item->getOptionParams(), true);

                    $price_options = 0;
                    if(sizeof($product->getOptions()) > 0){
                        foreach ($product->getOptions() as $o) {
                            $values = $o->getValues();
                            $_attribute_value = 0;

                            foreach ($option_params as $k => $v) {
                                if ($k == $o->getOptionId()) {
                                    $_attribute_value = $v;
                                    break;
                                }
                            }

                            if ($_attribute_value > 0) {
                                foreach ($values as $val) {
                                    if (is_array($_attribute_value)) {
                                        if (in_array($val->getOptionTypeId(), $_attribute_value)) {
                                            $price_options += $val->getPrice();

                                        }
                                    } else if ($val->getOptionTypeId() == $_attribute_value) {
                                        $price_options += $val->getPrice();
                                    }
                                }
                            }
                        }
                    }

                    $itemPrice = ($product->getFinalPrice() + $price_options) * $item->getQty();
                    $item_tax_amount = $this->getTaxAmount($product, $item->getQty());
                    if ($product->getStockItem()->getIsInStock()) {
                        $flag = false;
                        if ($product->getTypeId() == 'configurable') {
                            $main_child_product = Mage::getModel('catalog/product_type_configurable')
                                ->getProductByAttributes($option_params, $product)->getId();
                            $childProducts = Mage::getModel('catalog/product_type_configurable')->getUsedProducts(null, $product);
                            foreach ($childProducts as $childProduct) {
                                $qty = Mage::getModel('cataloginventory/stock_item')->loadByProduct($childProduct)->getQty();
                                if ($childProduct->getId() == $main_child_product) {
                                    if ($qty <= 0) {
                                        $flag = true;
                                    }
                                    break;
                                }
                            }
                        }

                        if (!$flag){
                            $data['cost'] += $itemPrice + $item_tax_amount;
                            $data['points'] += $pointEarn * $item->getQty();
                        }
                    }
                }
            }

        }

        return $data;
    }

    public function getDataCurrentMonthAjax($data)
    {
        $collection = $this->getCurrentTruBoxCollection();
        $_data = array(
            'points' => 0,
            'cost'  => 0
        );

        $start_month = Mage::getStoreConfig('trubox/general/start_month');
        $is_odd = $start_month % 2 == 0 ? true : false;
        if(sizeof($collection) > 0) {
            foreach ($collection as $item) {
                $dt = $this->getDataItem($item->getId(), $data);
                if($dt != null && is_array($dt)) {
                    if ($dt['is_remove'] === 'true' || $dt['item_qty'] == 0)
                        continue;

                    if(($data['type_value'] == Magestore_TruBox_Model_Type::TYPE_ONE_TIME)
                        || $data['type_value'] == Magestore_TruBox_Model_Type::TYPE_EVERY_MONTH
                        ||($data['type_value'] == Magestore_TruBox_Model_Type::TYPE_EVERY_TWO_MONTHS && (($is_odd && date('m', time()) % 2 == 0) || (!$is_odd && date('m', time()) % 2 != 0)))
                    ){
                        $product = Mage::getModel('catalog/product')->load($item->getProductId());
                        $pointEarn = $this->getPointEarning($product);
                        $option_params = json_decode($item->getOptionParams(), true);

                        $price_options = 0;
                        if(sizeof($product->getOptions()) > 0){
                            foreach ($product->getOptions() as $o) {
                                $values = $o->getValues();
                                $_attribute_value = 0;

                                foreach ($option_params as $k => $v) {
                                    if ($k == $o->getOptionId()) {
                                        $_attribute_value = $v;
                                        break;
                                    }
                                }

                                if ($_attribute_value > 0) {
                                    foreach ($values as $val) {
                                        if (is_array($_attribute_value)) {
                                            if (in_array($val->getOptionTypeId(), $_attribute_value)) {
                                                $price_options += $val->getPrice();

                                            }
                                        } else if ($val->getOptionTypeId() == $_attribute_value) {
                                            $price_options += $val->getPrice();
                                        }
                                    }
                                }
                            }
                        }

                        $itemPrice = ($product->getFinalPrice() + $price_options) * $dt['item_qty'];
                        $item_tax_amount = $this->getTaxAmount($product, $dt['item_qty']);
                        if ($product->getStockItem()->getIsInStock()) {
                            $flag = false;
                            if ($product->getTypeId() == 'configurable') {
                                $main_child_product = Mage::getModel('catalog/product_type_configurable')
                                    ->getProductByAttributes($option_params, $product)->getId();
                                $childProducts = Mage::getModel('catalog/product_type_configurable')->getUsedProducts(null, $product);
                                foreach ($childProducts as $childProduct) {
                                    $qty = Mage::getModel('cataloginventory/stock_item')->loadByProduct($childProduct)->getQty();
                                    if ($childProduct->getId() == $main_child_product) {
                                        if ($qty <= 0) {
                                            $flag = true;
                                        }
                                        break;
                                    }
                                }
                            }

                            if (!$flag){
                                $_data['cost'] += $itemPrice + $item_tax_amount;
                                $_data['points'] += $pointEarn * $dt['item_qty'];
                            }
                        }
                    }
                } else {
                    if(($item->getTypeItem() == Magestore_TruBox_Model_Type::TYPE_ONE_TIME && date('Y', strtotime($item->getOnetimeMonth())) == date('Y', time()) && date('m', strtotime($item->getOnetimeMonth())) != date('m', time()))
                        || $item->getTypeItem() == Magestore_TruBox_Model_Type::TYPE_EVERY_MONTH
                        ||($item->getTypeItem() == Magestore_TruBox_Model_Type::TYPE_EVERY_TWO_MONTHS && (($is_odd && date('m', time()) % 2 == 0) || (!$is_odd && date('m', time()) % 2 != 0)))
                    ){
                        $product = Mage::getModel('catalog/product')->load($item->getProductId());
                        $pointEarn = $this->getPointEarning($product);
                        $option_params = json_decode($item->getOptionParams(), true);

                        $price_options = 0;
                        if(sizeof($product->getOptions()) > 0){
                            foreach ($product->getOptions() as $o) {
                                $values = $o->getValues();
                                $_attribute_value = 0;

                                foreach ($option_params as $k => $v) {
                                    if ($k == $o->getOptionId()) {
                                        $_attribute_value = $v;
                                        break;
                                    }
                                }

                                if ($_attribute_value > 0) {
                                    foreach ($values as $val) {
                                        if (is_array($_attribute_value)) {
                                            if (in_array($val->getOptionTypeId(), $_attribute_value)) {
                                                $price_options += $val->getPrice();

                                            }
                                        } else if ($val->getOptionTypeId() == $_attribute_value) {
                                            $price_options += $val->getPrice();
                                        }
                                    }
                                }
                            }
                        }

                        $itemPrice = ($product->getFinalPrice() + $price_options) * $item->getQty();
                        $item_tax_amount = $this->getTaxAmount($product, $item->getQty());
                        if ($product->getStockItem()->getIsInStock()) {
                            $flag = false;
                            if ($product->getTypeId() == 'configurable') {
                                $main_child_product = Mage::getModel('catalog/product_type_configurable')
                                    ->getProductByAttributes($option_params, $product)->getId();
                                $childProducts = Mage::getModel('catalog/product_type_configurable')->getUsedProducts(null, $product);
                                foreach ($childProducts as $childProduct) {
                                    $qty = Mage::getModel('cataloginventory/stock_item')->loadByProduct($childProduct)->getQty();
                                    if ($childProduct->getId() == $main_child_product) {
                                        if ($qty <= 0) {
                                            $flag = true;
                                        }
                                        break;
                                    }
                                }
                            }

                            if (!$flag){
                                $_data['cost'] += $itemPrice + $item_tax_amount;
                                $_data['points'] += $pointEarn * $item->getQty();
                            }
                        }
                    }
                }
            }
        }

        $_data['cost'] = Mage::helper('core')->currency($_data['cost'], true, false);

        return $_data;
    }

    public function getDataNextMonthAjax($data, $month)
    {
        $collection = $this->getCurrentTruBoxCollection();
        $_data = array(
            'points' => 0,
            'cost'  => 0
        );

        $start_month = Mage::getStoreConfig('trubox/general/start_month');
        $is_odd = $start_month % 2 == 0 ? true : false;
        if($month == 1)
            $next_month = strtotime("first day of +$month month");
        else if ($month > 1)
            $next_month = strtotime("first day of +$month months");

        if(sizeof($collection) > 0) {
            foreach ($collection as $item) {
                $dt = $this->getDataItem($item->getId(), $data);
                if($dt != null && is_array($dt)){
                    if($dt['is_remove'] === 'true' || $dt['item_qty'] == 0)
                        continue;

                    if(($dt['type_value'] == Magestore_TruBox_Model_Type::TYPE_ONE_TIME && $month == 1)
                        || $dt['type_value'] == Magestore_TruBox_Model_Type::TYPE_EVERY_MONTH
                        ||($dt['type_value'] == Magestore_TruBox_Model_Type::TYPE_EVERY_TWO_MONTHS && (($is_odd && date('m', $next_month) % 2 == 0) || (!$is_odd && date('m', $next_month) % 2 != 0)))
                    ){
                        $product = Mage::getModel('catalog/product')->load($item->getProductId());
                        $pointEarn = $this->getPointEarning($product);
                        $option_params = json_decode($item->getOptionParams(), true);

                        $price_options = 0;
                        if(sizeof($product->getOptions()) > 0){
                            foreach ($product->getOptions() as $o) {
                                $values = $o->getValues();
                                $_attribute_value = 0;

                                foreach ($option_params as $k => $v) {
                                    if ($k == $o->getOptionId()) {
                                        $_attribute_value = $v;
                                        break;
                                    }
                                }

                                if ($_attribute_value > 0) {
                                    foreach ($values as $val) {
                                        if (is_array($_attribute_value)) {
                                            if (in_array($val->getOptionTypeId(), $_attribute_value)) {
                                                $price_options += $val->getPrice();

                                            }
                                        } else if ($val->getOptionTypeId() == $_attribute_value) {
                                            $price_options += $val->getPrice();
                                        }
                                    }
                                }
                            }
                        }

                        $itemPrice = ($product->getFinalPrice() + $price_options) * $dt['item_qty'];
                        $item_tax_amount = $this->getTaxAmount($product, $dt['item_qty']);
                        if ($product->getStockItem()->getIsInStock()) {
                            $flag = false;
                            if ($product->getTypeId() == 'configurable') {
                                $main_child_product = Mage::getModel('catalog/product_type_configurable')
                                    ->getProductByAttributes($option_params, $product)->getId();
                                $childProducts = Mage::getModel('catalog/product_type_configurable')->getUsedProducts(null, $product);
                                foreach ($childProducts as $childProduct) {
                                    $qty = Mage::getModel('cataloginventory/stock_item')->loadByProduct($childProduct)->getQty();
                                    if ($childProduct->getId() == $main_child_product) {
                                        if ($qty <= 0) {
                                            $flag = true;
                                        }
                                        break;
                                    }
                                }
                            }

                            if (!$flag){
                                $_data['cost'] += $itemPrice + $item_tax_amount;
                                $_data['points'] += $pointEarn * $dt['item_qty'];
                            }
                        }

                    }
                } else {
                    if(($item->getTypeItem() == Magestore_TruBox_Model_Type::TYPE_ONE_TIME && date('Y', strtotime($item->getOnetimeMonth())) == date('Y', $next_month) &&
                            date('m', strtotime($item->getOnetimeMonth())) == date('m', $next_month))
                        || $item->getTypeItem() == Magestore_TruBox_Model_Type::TYPE_EVERY_MONTH
                        ||($item->getTypeItem() == Magestore_TruBox_Model_Type::TYPE_EVERY_TWO_MONTHS && (($is_odd && date('m', $next_month) % 2 == 0) || (!$is_odd && date('m', $next_month) % 2 != 0)))
                    ){
                        $product = Mage::getModel('catalog/product')->load($item->getProductId());
                        $pointEarn = $this->getPointEarning($product);
                        $option_params = json_decode($item->getOptionParams(), true);

                        $price_options = 0;
                        if(sizeof($product->getOptions()) > 0){
                            foreach ($product->getOptions() as $o) {
                                $values = $o->getValues();
                                $_attribute_value = 0;

                                foreach ($option_params as $k => $v) {
                                    if ($k == $o->getOptionId()) {
                                        $_attribute_value = $v;
                                        break;
                                    }
                                }

                                if ($_attribute_value > 0) {
                                    foreach ($values as $val) {
                                        if (is_array($_attribute_value)) {
                                            if (in_array($val->getOptionTypeId(), $_attribute_value)) {
                                                $price_options += $val->getPrice();

                                            }
                                        } else if ($val->getOptionTypeId() == $_attribute_value) {
                                            $price_options += $val->getPrice();
                                        }
                                    }
                                }
                            }
                        }

                        $itemPrice = ($product->getFinalPrice() + $price_options) * $item->getQty();
                        $item_tax_amount = $this->getTaxAmount($product, $item->getQty());
                        if ($product->getStockItem()->getIsInStock()) {
                            $flag = false;
                            if ($product->getTypeId() == 'configurable') {
                                $main_child_product = Mage::getModel('catalog/product_type_configurable')
                                    ->getProductByAttributes($option_params, $product)->getId();
                                $childProducts = Mage::getModel('catalog/product_type_configurable')->getUsedProducts(null, $product);
                                foreach ($childProducts as $childProduct) {
                                    $qty = Mage::getModel('cataloginventory/stock_item')->loadByProduct($childProduct)->getQty();
                                    if ($childProduct->getId() == $main_child_product) {
                                        if ($qty <= 0) {
                                            $flag = true;
                                        }
                                        break;
                                    }
                                }
                            }

                            if (!$flag){
                                $_data['cost'] += $itemPrice + $item_tax_amount;
                                $_data['points'] += $pointEarn * $item->getQty();
                            }
                        }
                    }
                }
            }

        }

        $_data['cost'] = Mage::helper('core')->currency($_data['cost'], true, false);
        return $_data;
    }

    public function getDataItem($item_id, $data)
    {
        $result = null;
        if(sizeof($data) > 0)
        {
            foreach ($data as $dt) {
                if($dt['item_id'] == $item_id){
                    $result = $dt;
                    break;
                }
            }
        }

        return $result;
    }
}
