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
 * Show commission by level in Affiliate home page
 * 
 * @category    Magestore
 * @package     Magestore_AffiliateplusCommission
 * @author      Magestore Developer
 */
class Magestore_AffiliateplusCommission_Block_Home extends Mage_Core_Block_Template
{
	/**
	 * get Helper
	 *
	 * @return Magestore_Affiliateplus_Helper_Sales
	 */
	protected function getSalesHelper(){
		return Mage::helper('affiliatepluscommission');
	}
	
	public function enableToShow(){
		return $this->getSalesHelper()->getConfig('show')
			&& (count($this->getMonthlyCommission())
				|| count($this->getYearlyCommission()));
	}
	
	public function getSalesType(){
		if (!$this->hasData('sales_type')){
			$this->setData('sales_type',$this->getSalesHelper()->getConfig('type'));
		}
		return $this->getData('sales_type');
	}
	
	public function getCommissionType(){
		if (!$this->hasData('commission_type')){
			$this->setData('commission_type',$this->getSalesHelper()->getConfig('add_commission_type'));
		}
		return $this->getData('commission_type');
	}
	
	public function getMonthlyCommission(){
		if (!$this->hasData('monthly_commission')){
			if ($this->getSalesHelper()->getConfig('month')){
				$monthlyCommission = unserialize($this->getSalesHelper()->getConfig('month_tier'));
				usort($monthlyCommission,array($this,'cmpSales'));
				$this->setData('monthly_commission',$monthlyCommission);
			} else {
				$this->setData('monthly_commission',array());
			}
		}
		return $this->getData('monthly_commission');
	}
	
	public function getYearlyCommission(){
		if (!$this->hasData('yearly_commission')){
			if ($this->getSalesHelper()->getConfig('year')){
				$yearlyCommission = unserialize($this->getSalesHelper()->getConfig('year_tier'));
				usort($yearlyCommission,array($this,'cmpSales'));
				$this->setData('yearly_commission',$yearlyCommission);
			} else {
				$this->setData('yearly_commission',array());
			}
		}
		return $this->getData('yearly_commission');
	}
	
	public function cmpSales($aArray, $bArray){
		if ($aArray['sales'] == $bArray['sales'])
			return 0;
		return ($aArray['sales'] < $bArray['sales']) ? -1 : 1;
	}
	
	public function baseFormat($amount){
		return Mage::app()->getStore()->getBaseCurrency()->format($amount);
	}
	
	public function formatSales($total,$isSubtract = false){
		if ($this->getSalesType() == 'sales'){
			if ($isSubtract){
				$currency = Mage::app()->getStore()->convertPrice($total) - 0.01;
				return Mage::app()->getStore()->getCurrentCurrency()->format($currency);
			} else {
				return Mage::helper('core')->currency($total);//$this->baseFormat($total);
			}
		}
		return (int)$total;
	}
	
	public function formatCommission($amount){
		if ($this->getCommissionType() == 'fixed')
			return Mage::helper('core')->currency($amount);//$this->baseFormat($amount);
		return $amount . '%';
	}
}
