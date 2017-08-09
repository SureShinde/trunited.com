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
        if($truBox != null && $truBox->getId())
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
            ->setOrder('item_id','desc')
        ;
        return $collection;
    }

    public function firstCheckAddress()
    {
        $truBoxId = $this->getCurrentTruBoxId();
        $truBox_billing = Mage::getModel('trubox/address')->getCollection()
            ->addFieldToFilter('trubox_id', $truBoxId)
            ->addFieldToFilter('address_type', Magestore_TruBox_Model_Address::ADDRESS_TYPE_BILLING)
            ->getFirstItem()
        ;

        $customer = Mage::getModel('customer/customer')->load(Mage::getSingleton('customer/session')->getCustomer()->getId());
        if($truBox_billing->getId() == null)
        {
            $customer_billing = $customer->getDefaultBillingAddress();
            if( $customer_billing != null && $customer_billing->getId())
            {
                $_billing = Mage::getModel('trubox/address');
                $_billing->setData('address_type',Magestore_TruBox_Model_Address::ADDRESS_TYPE_BILLING);
                $_billing->setData('trubox_id',$truBoxId);
                $_billing->setData('firstname',$customer_billing->getFirstname());
                $_billing->setData('lastname',$customer_billing->getLastname());
                $_billing->setData('company','');
                $_billing->setData('telephone',$customer_billing->getTelephone());
                $_billing->setData('fax','');
                $_billing->setData('street',$customer_billing['street']);
                $_billing->setData('region_id',$customer_billing->getRegionId());
                $_billing->setData('region',$customer_billing->getRegion());
                $_billing->setData('city',$customer_billing->getCity());
                $_billing->setData('zipcode',$customer_billing->getPostcode());
                $_billing->setData('country',$customer_billing->getCountryId());
                $_billing->setData('created_at', now());
                $_billing->setData('updated_at', now());
                $_billing->save();
            }
        }

        $truBox_shipping = Mage::getModel('trubox/address')->getCollection()
            ->addFieldToFilter('trubox_id', $truBoxId)
            ->addFieldToFilter('address_type', Magestore_TruBox_Model_Address::ADDRESS_TYPE_SHIPPING)
            ->getFirstItem()
        ;

        if($truBox_shipping->getId() == null)
        {
            $customer_shipping = $customer->getDefaultShippingAddress();

            if($customer_shipping != null && $customer_shipping->getId())
            {
                $_shipping = Mage::getModel('trubox/address');
                $_shipping->setData('address_type',Magestore_TruBox_Model_Address::ADDRESS_TYPE_SHIPPING);
                $_shipping->setData('trubox_id',$truBoxId);
                $_shipping->setData('firstname',$customer_shipping->getFirstname());
                $_shipping->setData('lastname',$customer_shipping->getLastname());
                $_shipping->setData('company','');
                $_shipping->setData('telephone',$customer_shipping->getTelephone());
                $_shipping->setData('fax','');
                $_shipping->setData('street',$customer_shipping['street']);
                $_shipping->setData('region_id',$customer_shipping->getRegionId());
                $_shipping->setData('region',$customer_shipping->getRegion());
                $_shipping->setData('city',$customer_shipping->getCity());
                $_shipping->setData('zipcode',$customer_shipping->getPostcode());
                $_shipping->setData('country',$customer_shipping->getCountryId());
                $_shipping->setData('created_at', now());
                $_shipping->setData('updated_at', now());
                $_shipping->save();
            }
        }


    }

    public function getConfigurableOptionProduct($product)
    {
        if($product->getTypeId() == 'configurable')
            return $product->getTypeInstance(true)->getConfigurableAttributesAsArray($product);
        else
            return array();
    }

    public function getOrdersByCustomer()
    {
        return Mage::getResourceModel('sales/order_collection')
            ->addFieldToSelect('*')
            ->addFieldToFilter('customer_id',Mage::getSingleton('customer/session')->getCustomer()->getId())
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
            ->getFirstItem()
            ;

        if(isset($order_collection) && $order_collection->getId())
            return true;
        else
            return false;
    }

    public function hasSaveCode($customer_id)
    {
        $customer = Mage::getModel('customer/customer')->load($customer_id);
        if(!isset($customer) || !$customer->getId())
            return null;

        $coupon_collection = Mage::getModel('trubox/coupon')->getCollection()
            ->addFieldToFilter('customer_id', $customer_id)
            ->addFieldToFilter('status', Magestore_TruBox_Model_Status::COUPON_CODE_STATUS_PENDING)
            ->getFirstItem()
            ;

        if($coupon_collection->getId())
            return $coupon_collection;
        else
            return null;
    }

}
