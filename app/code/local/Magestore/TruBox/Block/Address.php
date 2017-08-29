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
 * TruBox Core Block Template Block
 * You should write block extended from this block when you write plugin
 *
 * @category    Magestore
 * @package     Magestore_TruBox
 * @author      Magestore Developer
 */
class Magestore_TruBox_Block_Address extends Mage_Core_Block_Template {

    CONST COUNTRY_DEFAULT_SHIPING = 'US';
    protected $_address;

    //construct function
    public function __construct() {
        parent::__construct();
    }

    //prepare layout
    public function _prepareLayout() {
        parent::_prepareLayout();
        return $this;
    }

    /**
     * check trubox system is enabled or not
     *
     * @return boolean
     */
    public function isEnable() {
        return Mage::helper('trubox')->isEnable();
    }

    public function saveAddressUrl() {
        return $this->getUrl('*/*/saveAddress');
    }

    public function getRegionHtml() {
        return $this->getUrl('*/*/getRegionHtml');
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

    public function getBillingAddressTruBox()
    {
        $truBoxId = Mage::helper('trubox')->getCurrentTruBoxId();
        $truBoxFilter = Mage::getModel('trubox/address')->getCollection()
            ->addFieldToFilter('trubox_id', $truBoxId)
            ->addFieldToFilter('address_type', Magestore_TruBox_Model_Address::ADDRESS_TYPE_BILLING)
            ->getFirstItem();


        if ($truBoxFilter->getId() != null)
            return $truBoxFilter;
        else
            return $this->getCurrentCustomer()->getDefaultBillingAddress();
    }


    public function getCardUrl()
    {
        return $this->getUrl('customer/paymentinfo/');
    }


    public function getCountryHtmlSelect($type)
    {
        if ($type == 'billing') {
            $country_name = 'billing[country]';
            $address = $this->getBillingAddressTruBox();
        } else {
            $address = $this->getShippingAddressTruBox();
            $country_name = 'shipping[country]';
        }


        $countryId = Mage::helper('core')->getDefaultCountry();

        if ($address == null) {
            $country = self::COUNTRY_DEFAULT_SHIPING;
        } else {
            $country = $address->getCountry();
        }


        if ($country) {
            $countryId = $country;
        }

        if (!$countryId) {
            $countryId = self::COUNTRY_DEFAULT_SHIPING;
        }

        $select = $this->getLayout()->createBlock('core/html_select')
            ->setName($country_name)
            ->setId('country-trubox-'.$type)
            ->setTitle(Mage::helper('checkout')->__('Country'))
            ->setClass('validate-select')
            ->setValue($countryId)
            ->setOptions($this->getCountryOptions());

        if ($type === 'shipping') {
            $select->setExtraParams('onchange="if(window.shipping)shipping.setSameAsBilling(false);"');
        }

        return $select->getHtml();
    }

    public function getCountryCollection()
    {
        if (!$this->_countryCollection) {
            $this->_countryCollection = Mage::getSingleton('directory/country')->getResourceCollection()
                ->loadByStore();
        }
        return $this->_countryCollection;
    }

    public function getCountryOptions()
    {
        $options    = false;
        $useCache   = Mage::app()->useCache('config');
        if ($useCache) {
            $cacheId    = 'DIRECTORY_COUNTRY_SELECT_STORE_' . Mage::app()->getStore()->getCode();
            $cacheTags  = array('config');
            if ($optionsCache = Mage::app()->loadCache($cacheId)) {
                $options = unserialize($optionsCache);
            }
        }

        if ($options == false) {
            $options = $this->getCountryCollection()->toOptionArray();
            if ($useCache) {
                Mage::app()->saveCache(serialize($options), $cacheId, $cacheTags);
            }
        }
        return $options;
    }

    public function getRegionCollectionTruBox($countryCode)
    {
        $regionCollection = Mage::getModel('directory/region_api')->items($countryCode);
        return $regionCollection;
    }
}
