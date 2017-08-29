<?php
/**
*
* Do not edit or add to this file if you wish to upgrade the module to newer
* versions in the future. If you wish to customize the module for your
* needs please contact us to https://www.milople.com/magento-extensions/contacts/
*
* @category     Ecommerce
* @package      Indies_Recurringandrentalpayments
* @copyright    Copyright (c) 2015 Milople Technologies Pvt. Ltd. All Rights Reserved.
* @url          https://www.milople.com/magento-extensions/recurring-and-subscription-payments.html
*
* Milople was known as Indies Services earlier.
*
**/

class Indies_Recurringandrentalpayments_Block_Adminhtml_Report_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
   public function __construct()
    {
        parent::__construct();
   	    $this->setTemplate('recurringandrentalpayments/report-widget-grid.phtml');
        $this->setId('subscriptionsReport');
        $this->setSaveParametersInSession(true);
	    $this->setFilterVisibility(false);
    }

    protected function _prepareCollection()
    {
		
		$filter = $this->getRequest()->get('filter');
		$collection = Mage::getModel('sales/order')
						->getCollection();
		if (is_null($filter)) 
		{
            $filter = $this->_defaultFilter;
			$collection->addFieldToFilter('entity_id' , -1);
        }
		else
		{
			$filter = base64_decode($filter);
       		parse_str(urldecode($filter), $data);
			
			$fromdate = Mage::app()->getLocale()->date($data['date'], Zend_Date::DATE_SHORT, null, false);
			$from = $fromdate->toString(Varien_Date::DATETIME_INTERNAL_FORMAT);
			$todate = Mage::app()->getLocale()->date($data['date'],Zend_Date::DATE_SHORT, null, false);
			$todate->addDay('1');
			$to = $todate->toString(Varien_Date::DATETIME_INTERNAL_FORMAT);
			$collection->addFieldToFilter('created_at', array('from' => $from, 'to' => $to));
			
			$collection->join(array('sequence'=> 'recurringandrentalpayments/sequence'), 'sequence.order_id = main_table.entity_id',array('main_table.entity_id'))->distinct(true);
			$collection->getSelect()->group('entity_id');
		}
		
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {

		$this->addColumn('increment_id', array(
			  'header' => Mage::helper('recurringandrentalpayments')->__('Order #'),
			  'align' => 'right',
			  'width' => '15px',
			  'index' => 'increment_id',
			  'sortable'      => false,
			  'filter' => false,
	    ));
		if (!Mage::app()->isSingleStoreMode()) {
            $this->addColumn('store_id', array(
                'header'    => Mage::helper('sales')->__('Purchased From (Store)'),
                'index'     => 'store_id',
                'type'      => 'store',
                'store_view'=> true,
                'display_deleted' => true,
			    'sortable'      => false,
			    'filter' => false,
            ));
        }

		 $this->addColumn('customer_firstname', array(
            'header' => Mage::helper('sales')->__('First Name'),
            'index' => 'customer_firstname',
			'sortable'      => false,
			'filter' => false,
        ));
		$this->addColumn('customer_lastname', array(
            'header' => Mage::helper('sales')->__('Last Name'),
            'index' => 'customer_lastname',
			'sortable'      => false,
			'filter' => false,
        ));
		$this->addColumn('customer_email', array(
            'header' => Mage::helper('sales')->__('Email Address'),
            'index' => 'customer_email',
			'sortable'      => false,
			'filter' => false,
        ));
		$this->addColumn('base_grand_total', array(
            'header' => Mage::helper('sales')->__('G.T. (Base)'),
            'index' => 'base_grand_total',
            'type'  => 'currency',
            'currency' => 'base_currency_code',
			'sortable'      => false,
			'filter' => false,
        ));

        $this->addColumn('grand_total', array(
            'header' => Mage::helper('sales')->__('G.T. (Purchased)'),
            'index' => 'grand_total',
            'type'  => 'currency',
            'currency' => 'order_currency_code',
			'sortable'      => false,
			'filter' => false,
        ));
		  $this->addColumn('status', array(
            'header' => Mage::helper('sales')->__('Status'),
            'index' => 'status',
            'type'  => 'options',
            'width' => '70px',
            'options' => Mage::getSingleton('sales/order_config')->getStatuses(),
			'sortable'      => false,
			'filter' => false,
			  
        ));
		
        $this->addExportType('*/*/exportCsv', Mage::helper('recurringandrentalpayments')->__('CSV'));
		$this->addExportType('*/*/exportXml', Mage::helper('recurringandrentalpayments')->__('XML'));

        $ret = parent::_prepareColumns();
        return $ret;
    }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('subscriptions');
        $this->getMassactionBlock()->setFormFieldName('subscriptions');
        return $this;
    }
	
}