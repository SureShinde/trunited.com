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
 * Affiliatepluspayperclick Block
 * 
 * @category    Magestore
 * @package     Magestore_AffiliateplusPayPerClick
 * @author      Magestore Developer
 */
class Magestore_AffiliateplusPayPerClick_Block_Clickdetails extends Mage_Core_Block_Template {

    /**
     * get Helper
     *
     * @return Magestore_Affiliateplus_Helper_Config
     */
    public function _getHelper() {
        return Mage::helper('affiliateplus/config');
    }

    protected function _construct() {
        parent::_construct();
        $account = Mage::getSingleton('affiliateplus/session')->getAccount();
        $collection = Mage::getModel('affiliateplus/transaction')->getCollection();
        /*edit by blanka*/
        if ($this->_getHelper()->getSharingConfig('balance') == 'store')
            $collection->addFieldToFilter('store_id', Mage::app()->getStore()->getId());
        elseif($this->_getHelper()->getSharingConfig('balance') == 'website'){
            $websiteId = Mage::app()->getWebsite()->getId();
            $storeIds = Mage::helper('affiliateplus/account')->getStoreIdsByWebsite($websiteId);
            $collection->addFieldToFilter('store_id', array('in'=>$storeIds));
        }
        /*end edit*/
        $collection->addFieldToFilter('account_id', $account->getId())
                ->addFieldToFilter('type', 2)
                ->setOrder('created_time', 'DESC');

        Mage::dispatchEvent('affiliateplus_prepare_click_collection', array(
            'collection' => $collection,
        ));

        if ($fromDate = $this->getRequest()->getParam('from-date'))
            $collection->addFieldToFilter('date(created_time)', array('gteq' => $this->formatData($fromDate)));
        if ($toDate = $this->getRequest()->getParam('to-date'))
            $collection->addFieldToFilter('date(created_time)', array('lteq' => $this->formatData($toDate)));
        if ($status = $this->getRequest()->getParam('status'))
            $collection->addFieldToFilter('main_table.status', $status);
        $collection->getSelect()
                ->group('date(created_time)')
                ->group('banner_id')
                ->columns(array('count' => 'COUNT(transaction_id)'))
                ->columns(array('commissions' => 'SUM(commission)'));
        $collection->setIsGroupCountSql(True);
        $this->setCollection($collection);
    }

    public function formatData($date) {
        $intPos = strrpos($date, "-");
        $str1 = substr($date, 0, $intPos);
        $str2 = substr($date, $intPos + 1);
        if (strlen($str2) == 4) {
            $date = $str2 . "-" . $str1;
        }
        return $date;
    }

    public function _prepareLayout() {
        parent::_prepareLayout();
        $pager = $this->getLayout()->createBlock('page/html_pager', 'sales_pager')->setCollection($this->getCollection());
        $this->setChild('sales_pager', $pager);

        $grid = $this->getLayout()->createBlock('affiliateplus/grid', 'sales_grid');

        $grid->addColumn('created_time', array(
            'header' => $this->__('Date'),
            'index' => 'created_time',
            'type' => 'date',
            'format' => 'medium',
            'align' => 'left',
        ));

        $grid->addColumn('title', array(
            'header' => $this->__('Banner'),
            'index' => 'title',
            'align' => 'left',
            'render'=>'getTitleBanner'
        ));

        $grid->addColumn('click', array(
            'header' => $this->__('Clicks (unique/raw)'),
            'align' => 'left',
            'render' => 'getUniqueRaw',
            'index' => 'click'
        ));

        $grid->addColumn('commissions', array(
            'header' => $this->__('Commissions'),
            'align' => 'left',
            'type' => 'baseprice',
            'index' => 'commissions'
        ));

        Mage::dispatchEvent('affiliateplus_prepare_click_columns', array(
            'grid' => $grid,
        ));

        $this->setChild('sales_grid', $grid);
        return $this;
    }
    public function getTitleBanner($row){
        $title=Mage::getModel('affiliateplus/banner')->load($row->getBannerId())->getTitle();
        if(!$title)
            $title='N/A';
        return $title;
    }

    public function getUniqueRaw($row) {
        $storeId = Mage::app()->getStore()->getId();
        $scope = Mage::getStoreConfig('affiliateplus/account/balance', $storeId);
        $account = Mage::getSingleton('affiliateplus/session')->getAccount();
        $datetime = $row['created_time'];
        $date = New DateTime($datetime);
        $unique = Mage::getModel('affiliateplus/action')->getCollection()
                ->addFieldToFilter('account_id', $account->getId())
                ->addFieldToFilter('type', 2)
                // ->addFieldToFilter('banner_id', $row->getBannerId())
                ->addFieldToFilter('is_unique', 1)
                ->addFieldToFilter('created_date', array('eq' => $date->format('Y-m-d')));
        if ($storeId && $scope == 'store')
            $unique->addFieldToFilter('store_id', $storeId);
        $raw = Mage::getModel('affiliateplus/action')->getCollection()
                ->addFieldToFilter('account_id', $account->getId())
                ->addFieldToFilter('type', 2)
                // ->addFieldToFilter('banner_id', $row->getBannerId())
                ->addFieldToFilter('created_date', array('eq' => $date->format('Y-m-d')));
        /*edit by blanka*/
        if ($storeId && $scope == 'store')
            $raw->addFieldToFilter('store_id', $storeId);
        elseif($scope == 'website'){
            $websiteId = Mage::app()->getWebsite()->getId();
            $storeIds = Mage::helper('affiliateplus/account')->getStoreIdsByWebsite($websiteId);
            $raw->addFieldToFilter('store_id', array('in'=>$storeIds));
        }
        /*end edit*/
        if ($row->getBannerId()) {
            $unique->addFieldToFilter('banner_id', $row->getBannerId());
            $raw->addFieldToFilter('banner_id', $row->getBannerId());
        } else {
            $unique->getSelect()->where('banner_id IS NULL OR banner_id = ?', '');
            $raw->getSelect()->where('banner_id IS NULL OR banner_id = ?', '');
        }
        $raw->getSelect()
                ->group('created_date')
                ->columns(array('totals_raw' => 'SUM(totals)'));
        $totals_raw = $raw->getFirstItem()->getTotalsRaw();
        if (!$totals_raw)
            $totals_raw = 0;
        return $unique->getSize() . '/' . $totals_raw;
    }

    public function getCount($row) {
        return $row['count'];
    }

    public function getNoNumber($row) {
        return sprintf('#%d', $row->getId());
    }

    public function getFrontendProductHtmls($row) {
        return Mage::helper('affiliateplus')->getFrontendProductHtmls($row->getData('order_item_ids'));
    }

    public function getCommissionPlus($row) {
        $addCommission = $row->getPercentPlus() * $row->getCommission() / 100 + $row->getCommissionPlus();
        return Mage::helper('core')->currency($addCommission); //Mage::app()->getStore()->getBaseCurrency()->format($addCommission);
    }

    public function getPagerHtml() {
        return $this->getChildHtml('sales_pager');
    }

    public function getGridHtml() {
        return $this->getChildHtml('sales_grid');
    }

    protected function _toHtml() {
        $this->getChild('sales_grid')->setCollection($this->getCollection());
        return parent::_toHtml();
    }

    public function getStatisticInfo() {
        $accountId = Mage::getSingleton('affiliateplus/session')->getAccount()->getId();
        $storeId = Mage::app()->getStore()->getId();
        $scope = Mage::getStoreConfig('affiliateplus/account/balance', $storeId);

        $collection = Mage::getModel('affiliateplus/transaction')->getCollection()
                ->addFieldToFilter('type', 2)
                ->addFieldToFilter('account_id', $accountId);

        $transactionTable = Mage::getModel('core/resource')->getTableName('affiliatepluslevel_transaction');
        if (Mage::helper('affiliateplus')->multilevelIsActive())
            $collection->getSelect()
                    ->joinLeft(array('ts' => $transactionTable), "ts.transaction_id = main_table.transaction_id", array('level' => 'level', 'plus_commission' => 'commission_plus'))
                    ->columns("if (ts.commission IS NULL, main_table.commission, ts.commission) as commission")
                    ->where("ts.tier_id=$accountId OR (ts.tier_id IS NULL AND main_table.account_id = $accountId )");
        /*edit by blanka*/
        if ($storeId && $scope == 'store')
            $collection->addFieldToFilter('store_id', $storeId);
        elseif($scope == 'website'){
            $websiteId = Mage::app()->getWebsite()->getId();
            $storeIds = Mage::helper('affiliateplus/account')->getStoreIdsByWebsite($websiteId);
            $collection->addFieldToFilter('store_id', array('in'=>$storeIds));
        }
        /*end edit*/
        $totalCommission = 0;
        foreach ($collection as $item) {
            if ($item->getStatus() == 1) {
                $totalCommission += $item->getCommission();
                if ($item->getPlusCommission())
                    $totalCommission += $item->getPlusCommission();
                else
                    $totalCommission += $item->getCommissionPlus() + $item->getCommission() * $item->getPercentPlus() / 100;
            }
        }

        return array(
            'number_commission' => count($collection),
            'transactions' => $this->__('PPC Transactions'),
            'commissions' => $totalCommission,
            'earning' => $this->__('PPC Earnings')
        );
    }

}