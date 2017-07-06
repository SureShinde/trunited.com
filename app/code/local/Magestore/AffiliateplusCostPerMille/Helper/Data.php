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
 * @package     Magestore_AffiliateplusCostPerMille
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * AffiliateplusCostPerMille Helper
 * 
 * @category    Magestore
 * @package     Magestore_AffiliateplusCostPerMille
 * @author      Magestore Developer
 */
class Magestore_AffiliateplusCostPerMille_Helper_Data extends Mage_Core_Helper_Abstract
{
    public function isDisplayed()
    {
        // Changed By Adam 30/07/2014
        if(!Mage::helper('affiliateplus')->isAffiliateModuleEnabled()) return false;
        
        $store = Mage::app()->getStore()->getId();
        $config = Mage::getStoreConfig('affiliateplus/commission/costpermille_enable', $store);
        return $config;
    }
    
    public function programPluginIsActive()
    {
        $isprogramEnabled = $this->isModuleOutputEnabled('Magestore_Affiliateplusprogram');
        return $isprogramEnabled;
    }
}