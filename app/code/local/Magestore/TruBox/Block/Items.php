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
class Magestore_TruBox_Block_Items extends Mage_Core_Block_Template {

    CONST COUNTRY_DEFAULT_SHIPING = 'US';
    protected $_address;
    /**
     * check trubox system is enabled or not
     *
     * @return boolean
     */
    public function isEnable() {
        return Mage::helper('trubox')->isEnable();
    }

    public function getTruBox() {
        $truBoxId = Mage::helper('trubox')->getCurrentTruBoxId();
        $collection = Mage::getModel('trubox/item')->getCollection()->addFieldToFilter('trubox_id', $truBoxId);
        return $collection;
    }

    public function saveItemsUrl() {
        return Mage::getBaseUrl() . 'mytrubox/index/saveItems';
    }

    public function saveAddressUrl() {
        return Mage::getBaseUrl() . 'mytrubox/index/saveAddress';
    }

    public function savePaymentUrl() {
        return Mage::getBaseUrl() . 'mytrubox/index/savePayment';
    }

    public function deleteItemsUrl($id) {
        return Mage::getBaseUrl() . 'mytrubox/index/deleteItems?id=' . $id;
    }

    public function getRegionHtml() {
        return Mage::getBaseUrl() . 'mytrubox/index/getRegionHtml';
    }

    public function getAddressTruBox() {
        $truBoxId = Mage::helper('trubox')->getCurrentTruBoxId();
        $truBoxFilter = Mage::getModel('trubox/address')->getCollection()
            ->addFieldToFilter('trubox_id', $truBoxId)->getFirstItem();
        return $truBoxFilter;
    }

    public function getPaymentTruBox() {
        $truBoxId = Mage::helper('trubox')->getCurrentTruBoxId();
        $truBoxFilter = Mage::getModel('trubox/payment')->getCollection()
            ->addFieldToFilter('trubox_id', $truBoxId)->getFirstItem();
        return $truBoxFilter;
    }

    public function getCountryHtmlSelect($type)
    {
        $address = $this->getAddressTruBox();
        $countryId = Mage::helper('core')->getDefaultCountry();
        $country = $address->getCountry();
        if ($country) {
            $countryId = $country;
        }
        if (!$countryId) {
            $countryId = self::COUNTRY_DEFAULT_SHIPING;
        }
        $select = $this->getLayout()->createBlock('core/html_select')
            ->setName('country')
            ->setId('country-trubox')
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

}
