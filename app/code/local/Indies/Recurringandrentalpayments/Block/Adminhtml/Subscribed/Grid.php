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

class Indies_Recurringandrentalpayments_Block_Adminhtml_Subscribed_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
   public function __construct()
    {
        parent::__construct();
        $this->setId('subscriptionsGrid');
        $this->setDefaultSort('id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
        if ($this->getRequest()->getParam('only_suspended') == 1)
            $this->setDefaultFilter(array('status' => '2'));

    }

    protected function _prepareCollection()
    {
        $collection = Mage::getModel('recurringandrentalpayments/subscription')->getCollection()->setOrder('subscription_id', 'DESC');
        $collection->getSelect();

        $this->setCollection($collection);


        return parent::_prepareCollection();
	
		
    }

    protected function _prepareColumns()
    {
		$this->addColumn('parent_order_id', array(
			  'header' => Mage::helper('recurringandrentalpayments')->__('Order ID'),
			  'align' => 'right',
			  'width' => '15px',
			  'index' => 'parent_order_id',
	    ));
        $this->addColumn('products_text', array(
			   'header' => Mage::helper('recurringandrentalpayments')->__('Subscribed Products'),
			   'align' => 'right',
			   'width' => '100px',
			   'index' => 'products_text',
			   'renderer' => 'recurringandrentalpayments/adminhtml_widget_grid_column_renderer_products'
        ));
        $this->addColumn('customer_name', array(
			   'header' => Mage::helper('recurringandrentalpayments')->__('Customer Name'),
			   'align' => 'right',
			   'width' => '100px',
			   'index' => 'customer_name',
		 ));
  		$this->addColumn('plan_name', array(
			 'header' => Mage::helper('recurringandrentalpayments')->__('Subscribed Plan'),
			 'align' => 'left',
			 'width' => '150px',
			 'sortable' => false,
			 'index' => 'term_type',
			 'renderer' => 'Indies_Recurringandrentalpayments_Block_Adminhtml_Subscribed_Renderer_Planname',
		));
		
		$this->addColumn('term_type', array(
			 'header' => Mage::helper('recurringandrentalpayments')->__('Subscribed Term'),
			 'align' => 'left',
			 'width' => '150px',
			 'type' => 'options',
			 'sortable' => false,
			 'options' => Mage::getModel('recurringandrentalpayments/source_subscription_periods')->getGridOptions(),
			 'index' => 'term_type',
		));
		 $this->addColumn('flat_next_payment_date', array(
			  'header' => Mage::helper('recurringandrentalpayments')->__('Upcoming Payment Date'),
			  'align' => 'right',
			  'width' => '150px',
			  'type' => 'date',
			  'index' => 'flat_next_payment_date',
		 ));
        $this->addColumn('flat_date_expire', array(
			  'header' => Mage::helper('recurringandrentalpayments')->__('Subscription Expiry Date'),
			  'align' => 'right',
			  'width' => '150px',
			  'type' => 'date',
			  'index' => 'flat_date_expire',
		 ));
        $this->addColumn('status', array(
			  'header' => Mage::helper('recurringandrentalpayments')->__('Subscription Status'),
			  'align' => 'left',
			  'width' => '150px',
			  'type' => 'options',
			  'sortable' => false,
			  'options' => Mage::getModel('recurringandrentalpayments/source_subscription_status')->getGridOptions(),
			  'index' => 'status',
		 ));
		 
        $this->addColumn('action',
			   array(
					'header' => $this->__('Action'),
					'width' => '100',
					'type' => 'action',
					'getter' => 'getId',
					'actions' => array(
  
						array(
							'caption' => $this->__('Edit'),
							'url' => array('base' => '*/*/edit'),
							'field' => 'id'
						)
					),
					'filter' => false,
					'sortable' => false,
					'index' => 'stores',
					'is_system' => true,
        ));
        $this->addExportType('*/*/exportCsv', Mage::helper('recurringandrentalpayments')->__('CSV'));
        $ret = parent::_prepareColumns();
        return $ret;
    }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('subscriptions');
        $this->getMassactionBlock()->setFormFieldName('subscriptions');
        return $this;
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit/', array('id' => $row->getId()));
    }
}