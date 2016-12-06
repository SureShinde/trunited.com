<?php

class Magestore_Affiliatepluspayperlead_Block_Lead extends Mage_Core_Block_Template
{
    protected function _construct(){
		parent::_construct();
		$account = Mage::getSingleton('affiliateplus/session')->getAccount();
		$collection = Mage::getModel('affiliatepluspayperlead/lead')->getCollection();
		/*if ($this->_getHelper()->getSharingConfig('balance') == 'store')
			$collection->addFieldToFilter('store_id',Mage::app()->getStore()->getId());
		$collection->addFieldToFilter('account_id',$account->getId())
			->setOrder('created_time','DESC');
		
		Mage::dispatchEvent('affiliateplus_prepare_sales_collection',array(
			'collection'	=> $collection,
		));
		
		if ($fromDate = $this->getRequest()->getParam('from-date'))
			$collection->addFieldToFilter('date(created_time)',array('gteq' => $this->formatData($fromDate)));
		if ($toDate = $this->getRequest()->getParam('to-date'))
			$collection->addFieldToFilter('date(created_time)',array('lteq' => $this->formatData($toDate)));
		if ($status = $this->getRequest()->getParam('status'))
			$collection->addFieldToFilter('status',$status);*/
		
		$this->setCollection($collection);
    }
    
    
	public function _prepareLayout(){
		parent::_prepareLayout();
		//$pager = $this->getLayout()->createBlock('page/html_pager','sales_pager')->setCollection($this->getCollection());
		//$this->setChild('sales_pager',$pager);
		
		$grid = $this->getLayout()->createBlock('affiliateplus/grid','lead_grid');
		
		// prepare column
		$grid->addColumn('id',array(
			'header'	=> $this->__('No.'),
			'align'		=> 'left',
			'render'	=> 'getNoNumber',
		));
		
		$grid->addColumn('created_time',array(
			'header'	=> $this->__('Date'),
			'index'		=> 'created_time',
			'type'		=> 'date',
			'format'	=> 'medium',
			'align'		=> 'left',
		));
		
		$grid->addColumn('order_item_names',array(
			'header'	=> $this->__('Products Name'),
			'index'		=> 'order_item_names',
			'align'		=> 'left',
			'render'	=> 'getFrontendProductHtmls',
		));
		
		$grid->addColumn('total_amount',array(
			'header'	=> $this->__('Total Amount'),
			'align'		=> 'left',
			'type'		=> 'baseprice',
			'index'		=> 'total_amount'
		));
		
		$grid->addColumn('commission',array(
			'header'	=> $this->__('Commission'),
			'align'		=> 'left',
			'type'		=> 'baseprice',
			'index'		=> 'commission'
		));
		
		$grid->addColumn('commission_plus',array(
			'header'	=> $this->__('Additional').'<br />'.$this->__('Commission'),
			'align'		=> 'left',
			'type'		=> 'baseprice',
			'index'		=> 'commission_plus',
			'render'	=> 'getCommissionPlus'
		));
		
		Mage::dispatchEvent('affiliateplus_prepare_sales_columns',array(
			'grid'	=> $grid,
		));
		
		$grid->addColumn('status',array(
			'header'	=> $this->__('Status'),
			'align'		=> 'left',
			'index'		=> 'status',
			'type'		=> 'options',
			'options'	=> array(
				1	=> $this->__('Complete'),
				2	=> $this->__('Pending'),
				3	=> $this->__('Canceled')
			)
		));
		
		$this->setChild('lead_grid',$grid);
		return $this;
    }
	public function getGridHtml(){
    	return $this->getChildHtml('lead_grid');
    }
	protected function _toHtml(){
    	$this->getChild('lead_grid')->setCollection($this->getCollection());
    	return parent::_toHtml();
    }
}