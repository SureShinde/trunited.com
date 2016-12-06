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

class Indies_Recurringandrentalpayments_Block_Adminhtml_Recurringandrentalpayments_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
  public function __construct()
  {
      parent::__construct();
      $this->setId('recurringandrentalpaymentsGrid');
      $this->setDefaultSort('recurringandrentalpayments_id');
      $this->setDefaultDir('ASC');
      $this->setSaveParametersInSession(true);
  }

  protected function _prepareCollection()
  {
      $collection = Mage::getModel('recurringandrentalpayments/plans')->getCollection();
      $this->setCollection($collection);
      return parent::_prepareCollection();
  }

  protected function _prepareColumns()
  {
	 
	  $this->addColumn('plan_id', array(
          'header'    => Mage::helper('recurringandrentalpayments')->__('ID'),
          'align'     =>'right',
          'width'     => '50px',
          'index'     => 'plan_id',
      ));

      $this->addColumn('plan_name', array(
          'header'    => Mage::helper('recurringandrentalpayments')->__('Plan Name'),
          'align'     =>'left',
          'index'     => 'plan_name',
      ));

	  $this->addColumn('is_normal', array(
          'header'    => Mage::helper('recurringandrentalpayments')->__('Allow Purchase as Normal Product'),
          'align'     =>'left',
          'index'     => 'is_normal',
		  'type'      => 'options',
          'options'   => array(
   			  1 => 'Yes',
              0 => 'No',
  			),
      ));
	   $this->addColumn('terms', array(
          'header'    => Mage::helper('recurringandrentalpayments')->__('Terms'),
          'align'     => 'left',
          'width'     => '80px',
          'index'     => 'plan_id',
		  'renderer' => new Indies_Recurringandrentalpayments_Block_Adminhtml_Recurringandrentalpayments_Edit_Tab_Count(),
      ));
	  $this->addColumn('creation_time', array(
          'header'    => Mage::helper('recurringandrentalpayments')->__('Created Time'),
          'align'     =>'left',
          'index'     => 'creation_time',
      ));
	  $this->addColumn('update_time', array(
          'header'    => Mage::helper('recurringandrentalpayments')->__('Updated Time'),
          'align'     =>'left',
          'index'     => 'update_time',
      ));
	  $this->addColumn('plan_status', array(
          'header'    => Mage::helper('recurringandrentalpayments')->__('Status'),
          'align'     => 'left',
          'width'     => '80px',
          'index'     => 'plan_status',
          'type'      => 'options',
          'options'   => array(
   			  1 => 'Enable',
              2 => 'Disable',
          ),
      ));
	  $this->addColumn('action',
            array(
                'header'    =>  Mage::helper('recurringandrentalpayments')->__('Action'),
                'width'     => '100',
                'type'      => 'action',
                'getter'    => 'getId',
                'actions'   => array(
                    array(
                        'caption'   => Mage::helper('recurringandrentalpayments')->__('Edit Plan'),
                        'url'       => array('base'=> '*/*/edit' ),
                        'field'     => 'id',
						
                    )
                ),
                'filter'    => false,
                'sortable'  => false,
                'index'     => 'stores',
                'is_system' => true,
        ));		
		$this->addExportType('*/*/exportCsv', Mage::helper('recurringandrentalpayments')->__('CSV'));
		$this->addExportType('*/*/exportXml', Mage::helper('recurringandrentalpayments')->__('XML'));
	  
      return parent::_prepareColumns();
  }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('plan_id');
        $this->getMassactionBlock()->setFormFieldName('recurringandrentalpayments');

        $this->getMassactionBlock()->addItem('delete', array(
             'label'    => Mage::helper('recurringandrentalpayments')->__('Delete'),
             'url'      => $this->getUrl('*/*/massDelete'),
             'confirm'  => Mage::helper('recurringandrentalpayments')->__('Deleting the plan will remove relation of terms and products with it. Are you sure ?')
        ));
        return $this;
    }

  public function getRowUrl($row)
  {
      $product_id = Mage::getModel('recurringandrentalpayments/plans')->load($row->getId())->getProductId();
	  return $this->getUrl('*/*/edit', array('id' => $row->getId()));
  }

}