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
 * @package     Magestore_AffiliateplusDirectLink
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * Affiliateplusdirectlink Block
 * 
 * @category    Magestore
 * @package     Magestore_AffiliateplusDirectLink
 * @author      Magestore Developer
 */
class Magestore_AffiliateplusDirectLink_Block_Affiliateplusdirectlink extends Mage_Core_Block_Template {

    /**
     * prepare block's layout
     *
     * @return Magestore_AffiliateplusDirectLink_Block_Affiliateplusdirectlink
     */
    public function _prepareLayout() {
        return parent::_prepareLayout();
    }

    public function getBannerArr() {
        /*edit by blanka*/
        /*$bannercollection = Mage::getModel('affiliateplus/banner')->getCollection()
                ->addFieldToFilter('status', 1);*/
        $bannerBlock = Mage::getBlockSingleton('affiliateplus/account_banner');
        $bannercollection = $bannerBlock->getBannerCollection();
        /*end edit by blanka*/
        return $bannercollection;
    }

    public function getDoaminsSize() {
        return $this->getChild('domains')->getCollection()->getSize();
    }

    public function getDirectLinkSize() {
        return $this->getChild('directlinks')->getCollection()->getSize();
    }

}