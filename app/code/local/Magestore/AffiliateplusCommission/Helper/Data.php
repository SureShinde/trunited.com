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
 * @package     Magestore_AffiliateplusCommission
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * AffiliateplusCommission Helper
 * 
 * @category    Magestore
 * @package     Magestore_AffiliateplusCommission
 * @author      Magestore Developer
 */
class Magestore_AffiliateplusCommission_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * get configuration for additional sale
     * 
     * @param string $code
     * @param mixed $store
     * @return mixed
     */
    public function getConfig($code, $store = null){
		return Mage::getStoreConfig('affiliateplus/commission/'.$code,$store);
	}
	
	public function getMonthlyCommission(){
		$monthlyCommission = unserialize($this->getConfig('month_tier'));
		usort($monthlyCommission,array($this,'cmpSales'));
		return $monthlyCommission;
	}
	
	public function getYearlyCommission(){
		$yearlyCommission = unserialize($this->getConfig('year_tier'));
		usort($yearlyCommission,array($this,'cmpSales'));
		return $yearlyCommission;
	}
	
	public function cmpSales($aArray, $bArray){
		if ($aArray['sales'] == $bArray['sales'])
			return 0;
		return ($aArray['sales'] < $bArray['sales']) ? 1 : -1;
	}
	
    /**
     * get total sale of affiliate on a period (current month or current year)
     * 
     * @param int $accountId
     * @param string $period Period pattern
     * @return float
     */
	public function getAccountSales($accountId, $period = 'Y-m-'){
		$transactions = Mage::getResourceModel('affiliateplus/transaction_collection')
			->addFieldToFilter('account_id',$accountId)
			->addFieldToFilter('status',1)
			->addFieldToFilter('created_time',array('like' => date($period).'%'));
		$transactions->getSelect()
			->columns('COUNT(`order_id`) AS total_orders')
			->columns('SUM(`total_amount`) AS total_sales')
			->group('account_id');
		$salesStatistic = $transactions->getFirstItem();
		return $salesStatistic->getData("total_{$this->getConfig('type')}");
	}
        
    /**
     * @author Adam
     * @date    30/07/2014
     * @return boolean
     */
    public function isPluginEnabled() {
        if(!Mage::helper('affiliateplus')->isAffiliateModuleEnabled()) return false;
        $storeId = Mage::app()->getStore()->getId();
        $check = Mage::getStoreConfig('affiliateplus/commissionbylevel/enable', $storeId);
        return $check;
    }
}
