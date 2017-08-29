<?php

class Magestore_AffiliateplusCostPerMille_Block_Commissions extends Magestore_Affiliateplus_Block_Sales_Standard
{
	
	public function getAccount(){
		return Mage::getSingleton('affiliateplus/session')->getAccount();
	}
	
	
	protected function _construct(){
		parent::_construct();
        $resource = Mage::getModel('core/resource');
        $read = $resource->getConnection('core_read');
        $bannerTable=Mage::getModel('core/resource')->getTableName('affiliateplus/banner');
        $actionTable=Mage::getModel('core/resource')->getTableName('affiliateplus/action');
		$account = Mage::getSingleton('affiliateplus/session')->getAccount();
		$collection = Mage::getModel('affiliateplus/transaction')->getCollection();
		if ($this->_getHelper()->getSharingConfig('balance') == 'store')
			$collection->addFieldToFilter('store_id',Mage::app()->getStore()->getId());
		$collection
            ->addFieldToFilter('main_table.account_id',$account->getId())
            ->addFieldToFilter('main_table.type',1)
			;
        $collection->getSelect()
            ->group(array('date(main_table.created_time)','banner_table.banner_id'))
            ->columns(array('transactions'=>'COUNT(date(main_table.created_time))'))
            ->join(array('banner_table'=>$bannerTable),'main_table.banner_id = banner_table.banner_id',array('banner_title'=>'banner_table.title','commission_total'=>'SUM(main_table.commission)'))
            ;
        //Zend_Debug::dump($collection->getSelect()->__toString());die();
		if ($fromDate = $this->getRequest()->getParam('from-date'))
			$collection->addFieldToFilter('date(main_table.created_time)',array('gteq' => $this->formatData($fromDate)));
		if ($toDate = $this->getRequest()->getParam('to-date'))
			$collection->addFieldToFilter('date(main_table.created_time)',array('lteq' => $this->formatData($toDate)));
		if ($status = $this->getRequest()->getParam('status'))
			$collection->addFieldToFilter('status',$status);
		$collection->setIsGroupCountSql(true);
		$this->setCollection($collection);
    }
    
    
	public function _prepareLayout(){
		parent::_prepareLayout();
		$pager = $this->getLayout()->createBlock('page/html_pager','impression_pager')->setCollection($this->getCollection());
		$this->setChild('impression_pager',$pager);
		
		$grid = $this->getLayout()->createBlock('affiliateplus/grid','impression_grid');
		
		// prepare column
		$grid->addColumn('created_time',array(
			'header'	=> $this->__('Date'),
			'index'		=> 'created_time',
			'type'		=> 'date',
			'format'	=> 'medium',
			'align'		=> 'left',
		));
		
		$grid->addColumn('banner_title',array(
			'header'	=> $this->__('Banner'),
			'index'		=> 'banner_title',
			'align'		=> 'left',
            'filter_index'  =>  'banner_table.title'
		));
		
        $grid->addColumn('impressions',array(
			'header'	=> $this->__('Impressions (unique/raw)'),
			'align'		=> 'left',
			'render'		=> 'getImpressions'
		));
        
		$grid->addColumn('commission_total',array(
			'header'	=> $this->__('Commission'),
			'align'		=> 'left',
            'type'		=> 'baseprice',
			'index'		=> 'commission_total'
		));
		
		
	
		
		$this->setChild('impression_grid',$grid);
		return $this;
    }
	public function getGridHtml(){
    	return $this->getChildHtml('impression_grid');
    }
    public function getImpressions($row){
        $date = new DateTime($row->getCreatedTime());
        
        $collection = Mage::getModel('affiliateplus/action')->getCollection()
                        ->addFieldToFilter('account_id',$row->getAccountId())
                        ->addFieldToFilter('banner_id',$row->getBannerId())
                        ->addFieldToFilter('created_date',array('eq'=>$date->format('Y-m-d')))
                        //->addFieldToFilter('store_id',$row->getStoreId())
                        ->addFieldToFilter('type',$row->getType())
                ;
        if ($this->_getHelper()->getSharingConfig('balance') == 'store')
			$collection->addFieldToFilter('store_id',Mage::app()->getStore()->getId());
        elseif($this->_getHelper()->getSharingConfig('balance') == 'website'){
            $websiteId = Mage::app()->getWebsite()->getId();
            $storeIds = Mage::helper('affiliateplus/account')->getStoreIdsByWebsite($websiteId);
            $collection->addFieldToFilter('store_id', array('in'=>$storeIds));
        }
        $collection->getSelect()
                    ->group('created_date')
                    ->columns(array('uniques'=>'SUM(is_unique)','raws'=>'SUM(totals)'))
                ;
        //Zend_Debug::dump($collection->getAllIds());
        $raw = 0;
        if($collection->getSize()) {
            $unique = $collection->getFirstItem()->getUniques();        
            $raw = $collection->getFirstItem()->getRaws();        
            
        }
        return $unique.'/'.$raw;
    }
	public function getPagerHtml(){
    	return $this->getChildHtml('impression_pager');
    }
	protected function _toHtml(){
    	$this->getChild('impression_grid')->setCollection($this->getCollection());
    	return parent::_toHtml();
    }
 	public function getStatisticInfo(){
    	$accountId = Mage::getSingleton('affiliateplus/session')->getAccount()->getId();
		$storeId = Mage::app()->getStore()->getId();
		$scope = Mage::getStoreConfig('affiliateplus/account/balance', $storeId);
		$totalCommission=0;
		$number = 0;
        $collection = $this->getCollection();
        /*edit by blanka*/
		if($storeId && $scope == 'store')
			$collection->addFieldToFilter('store_id', $storeId);
        elseif ($scope == 'website') {
            $websiteId = Mage::app()->getWebsite()->getId();
            $storeIds = Mage::helper('affiliateplus/account')->getStoreIdsByWebsite($websiteId);
            $collection->addFieldToFilter('store_id', array('in' => $storeIds));
        }
        /*end edit*/
		foreach($collection as $item){
			$totalCommission += $item->getCommissionTotal(); 
            $number += $item->getTransactions();
		}
		return array(
			'number_commission'	=> $number,
			'transactions'		=> $this->__('PPM Transactions'),
			'commissions'		=> $totalCommission,
			'earning'			=> $this->__('PPM Earnings')
		);
    }

}