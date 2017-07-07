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
 * @package     Magestore_AffiliateplusBanner
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * Affiliateplusbanner Custom Link Form Block
 * 
 * @category    Magestore
 * @package     Magestore_AffiliateplusBanner
 * @author      Magestore Developer
 */
class Magestore_AffiliateplusDirectLink_Block_Bannercode extends Magestore_Affiliateplus_Block_Account_Banner {

    private $_directlink = null;

    protected function _construct() {
        parent::_construct();
        $this->setTemplate('affiliateplusdirectlink/bannercode.phtml');
    }

    public function setDirectlink($id) {
        return $this->_directlink = Mage::getModel('affiliateplusdirectlink/directlink')->load($id);
    }

    public function getDirectlink() {
        return $this->_directlink;
    }

    public function getBanner() {
        return Mage::getModel('affiliateplus/banner')->load($this->getDirectlink()->getBannerId());
    }

    public function getLink() {
        if ($this->getDirectlink()->getLink()) {
            return $this->getDirectlink()->getLink();
        } else {
            return $this->getBanner()->getLink();
        }
    }

    public function getBannerCode($banner) {
        $renderBlock = Mage::getBlockSingleton('affiliateplusdirectlink/bannerview');
        $renderBlock->setRenderType('code')
                ->setBanner($banner)
                ->setBannerLink($this->getLink());
        return trim($renderBlock->toHtml());
    }

    public function isBannerModulActive() {
        return Mage::helper('affiliateplusdirectlink')->isModuleOutputEnabled('Magestore_AffiliateplusBanner');
    }
    
    public function getTypesLabel()
    {
        return Mage::helper('affiliateplusbanner')->getOptionHash();
    }
    
    /**
     * get banner preview HTML
     * 
     * @param Magestore_Affiliateplus_Model_Banner $banner
     * @return string
     */
    public function getBannerPreview($banner)
    {
        $renderBlock = Mage::getBlockSingleton('affiliateplusdirectlink/bannerview');
        $renderBlock->setRenderType('preview')
                ->setBanner($banner)
                ->setBannerLink($this->getLink());
        return $renderBlock->toHtml();
    }

}