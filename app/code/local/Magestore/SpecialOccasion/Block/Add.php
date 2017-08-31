<?php

class Magestore_SpecialOccasion_Block_Add extends Mage_Core_Block_Template
{
    CONST COUNTRY_DEFAULT_SHIPING = 'US';



	public function _prepareLayout(){
		return parent::_prepareLayout();
	}

    public function getCountryHtmlSelect()
    {
        $address = null;

        $country_name = 'country';
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
            ->setId('country-occasion')
            ->setTitle(Mage::helper('checkout')->__('Country'))
            ->setClass('validate-select')
            ->setValue($countryId)
            ->setOptions($this->getCountryOptions());

        $select->setExtraParams('onchange="if(window.shipping)shipping.setSameAsBilling(false);"');

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

    public function getAddNewAction()
    {
        return $this->getUrl('*/*/addPost');
    }

    public function getUpdateAction()
    {
        return $this->getUrl('*/*/updatePost');
    }

    public function getBackAction()
    {
        return $this->getUrl('*/');
    }
}
