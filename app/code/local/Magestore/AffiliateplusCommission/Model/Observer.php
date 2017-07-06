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
 * AffiliateplusCommission Observer Model
 * 
 * @category    Magestore
 * @package     Magestore_AffiliateplusCommission
 * @author      Magestore Developer
 */
class Magestore_AffiliateplusCommission_Model_Observer
{
    /**
     * calculation additional commission for affiliate core
     * 
     * @param type $observer
     */
    public function calculateCommissionAfter($observer)
    {
        // Changed By Adam 30/07/2014
        if(!Mage::helper('affiliatepluscommission')->isPluginEnabled()) return;
        
        $order = $observer['order'];
        $transaction = $observer['transaction'];
        
        $commissionPlus = 0;
        $salesHelper = Mage::helper('affiliatepluscommission');
        // Monthly commission
        if ($salesHelper->getConfig('month')) {
            $commissionLevels = $salesHelper->getMonthlyCommission();
            if ($levels = count($commissionLevels)) {
                $accountSales = $salesHelper->getAccountSales($transaction->getAccountId());
                foreach ($commissionLevels as $commissionLV) {
                    if ($accountSales >= $commissionLV['sales']) {
                        $commissionPlus = $commissionLV['commission'];
                        break;
                    }
                }
            }
        }
        // Yearly commission
        if ($salesHelper->getConfig('year')) {
            $commissionLevels = $salesHelper->getYearlyCommission();
            if ($levels = count($commissionLevels)) {
                $yearlyPlus = 0;
                $accountSales = $salesHelper->getAccountSales($transaction->getAccountId(), 'Y-');
                foreach ($commissionLevels as $commissionLV) {
                    if ($accountSales >= $commissionLV['sales']) {
                        $yearlyPlus = $commissionLV['commission'];
                        break;
                    }
                }
                $commissionPlus += $yearlyPlus;
            }
        }
        
        // set commission plus for transaction
        if ($salesHelper->getConfig('add_commission_type') == 'percentage') {
            $transaction->setData('percent_plus', $commissionPlus);
            $transaction->setData('commission_plus', 0);
        } else {
            $transaction->setData('percent_plus', 0);
            $transaction->setData('commission_plus', $commissionPlus);
        }
    }
    
    /**
     * Add column "additional commission" for commission grid
     * 
     * @param type $observer
     */
    public function prepareSalesColumnsPlus($observer)
    {
        // Changed By Adam 30/07/2014
        if(!Mage::helper('affiliatepluscommission')->isPluginEnabled()) return;
        
        $grid = $observer['grid'];
        $helper = Mage::helper('affiliatepluscommission');
        $grid->addColumn('commission_plus', array(
            'header' => $helper->__('Additional') . '<br />' . $helper->__('Commission'),
            'align' => 'left',
            'type' => 'baseprice',
            'index' => 'commission_plus',
            'render' => 'getCommissionPlus'
        ));
    }
    
    /**
     * Add column "additional commission" for tier commission grid
     * 
     * @param type $observer
     */
    public function levelPrepareSalesColumnsPlus($observer)
    {
        // Changed By Adam 30/07/2014
        if(!Mage::helper('affiliatepluscommission')->isPluginEnabled()) return;
        
        $grid = $observer['grid'];
        $helper = Mage::helper('affiliatepluscommission');
        
        $grid->addColumn('commission_plus',array(
			'header'	=> $helper->__('Additional').'<br />'.$helper->__('Commission'),
			'align'		=> 'left',
			'type'		=> 'baseprice',
			'index'		=> 'commission_plus',
		));
    }
    
    /**
     * Add column "additional commission" for tier commission grid on backend
     * 
     * @param type $observer
     */
    public function levelTransactionPrepareColumns($observer)
    {
        // Changed By Adam 30/07/2014
        if(!Mage::helper('affiliatepluscommission')->isPluginEnabled()) return;
        
        $grid = $observer['grid'];
        $grid->addColumn('commission_plus', array(
			'header'    => Mage::helper('affiliatepluscommission')->__('Additional Commission'),
			'align'     => 'right',
			'index'     => 'commission_plus',
			'type'		=> 'price',
			'currency_code' => $grid->getAffiliatepluslevelCurrencyCode(),
      	));
    }
}
