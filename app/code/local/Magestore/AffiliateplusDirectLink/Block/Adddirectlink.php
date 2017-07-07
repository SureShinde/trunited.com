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
class Magestore_AffiliateplusDirectLink_Block_Adddirectlink extends Mage_Core_Block_Template {

    private $_domain = null;
    private $_directlink = null;

    protected function _construct() {
        parent::_construct();
        $this->setTemplate('affiliateplusdirectlink/adddirectlink.phtml');
    }

    public function setDomainId($id) {
        return $this->_domain = $id;
    }

    public function getDomainId() {
        return $this->_domain;
    }

    public function setDirectlink($directlinkId) {
        $model = Mage::getModel('affiliateplusdirectlink/directlink')->load($directlinkId);
        if ($model->getDomainId())
            $this->setDomainId($model->getDomainId());
        return $this->_directlink = $model;
    }

    public function getDirectlink() {
        return $this->_directlink;
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
    public function getDomain(){
        return Mage::getModel('affiliateplusdirectlink/domain')->load($this->getDomainId())->getDomain();
    }

}