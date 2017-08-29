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
 * @package     Magestore_AffiliateplusPayPerClick
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * AffiliateplusPayPerClick Helper
 * 
 * @category    Magestore
 * @package     Magestore_AffiliateplusPayPerClick
 * @author      Magestore Developer
 */
class Magestore_AffiliateplusPayPerClick_Helper_Data extends Mage_Core_Helper_Abstract {

    public function getPayPerClickConfig($name, $store = null) {
        return Mage::getStoreConfig('affiliateplus/commission/' . $name, $store);
    }

    public function addTransaction($account_id, $account_name, $account_email, $commission, $storeId, $banner_id, $program_id) {
        $model = Mage::getModel('affiliatepluspayperclick/transaction');
        $model->setAccountId($account_id)
                ->setAccountName($account_name)
                ->setAccountEmail($account_email)
                ->setCommission($commission)
                ->setCreatedTime(now())
                ->setStoreId($storeId)
                ->setBannerId($banner_id)
                ->setType(2)
                ->save();
        $isprogramEnabled = $this->isModuleOutputEnabled('Magestore_Affiliateplusprogram');
        if ($isprogramEnabled) {
            $modelProgram = Mage::getModel('affiliateplusprogram/transaction');
            $modelProgram->setTransactionId($model->getId())
                    ->setAccountId($account_id)
                    ->setProgramId($program_id)
                    ->setAccountName($account_name)
                    ->setCommission($commission)
                    ->setType(2)
                    ->save();
        }
    }

    public function programIsActive() {
        $modules = Mage::getConfig()->getNode('modules')->children();
        $modulesArray = (array) $modules;
        if (isset($modulesArray['Magestore_Affiliateplusprogram']) && is_object($modulesArray['Magestore_Affiliateplusprogram']))
            return $modulesArray['Magestore_Affiliateplusprogram']->is('active');
        return false;
    }
    
    
    /**
     * @author Adam
     * @date    03/08/2014
     * @return boolean
     */
        public function isPluginEnabled(){
            if(!Mage::helper('affiliateplus')->isAffiliateModuleEnabled()) return false;
            return $this->getPayPerClickConfig('payperclick_enable');
        }

}