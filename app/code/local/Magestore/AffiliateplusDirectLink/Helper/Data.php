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
 * AffiliateplusDirectLink Helper
 * 
 * @category    Magestore
 * @package     Magestore_AffiliateplusDirectLink
 * @author      Magestore Developer
 */

class Magestore_AffiliateplusDirectLink_Helper_Data extends Mage_Core_Helper_Abstract {

    public function refineDomain($domain) {
        $parseUrl = parse_url(trim($domain));
        $domain = trim($parseUrl['host'] ? $parseUrl['host'] : array_shift(explode('/', $parseUrl['path'], 2)));
        if ($parseUrl['port'])
            $domain.=':' . $parseUrl['port'];
        return $domain;
    }

    public function refineSourcePage($source_page) {
        $parseUrl = parse_url(trim($source_page));
        if (!$parseUrl['host'])
            return substr($parseUrl['path'], strlen($this->refineDomain($source_page)) + 1);
        return trim($parseUrl['path'], '/');
    }

    public function isPluginDisable($store = null) {
        
        if (Mage::helper('affiliateplus/account')->accountNotLogin())
            return TRUE;
        // Changed By Adam 30/07/2014
        if(!Mage::helper('affiliateplus')->isAffiliateModuleEnabled()) return false;
            
        $check = Mage::getStoreConfig('affiliateplus/directlink/enable', $store);
        return !$check;
    }

    public function renderDirectLink($directlink) {
        $arr = explode('/', $directlink);
        $domain = $this->refineDomain($directlink);
        $page = $arr[count($arr) - 1];
        if (!$page && count($arr) >= 3)
            $page = $arr[count($arr) - 2];
        if ($page) {
            if (strlen($page) > 7)
                $page = substr($page, -7);
            $link = substr($domain, 0, 9) . '...' . $page;
        } else {
            $link = substr($domain, 0, 20);
        }
        return $link;
    }

}