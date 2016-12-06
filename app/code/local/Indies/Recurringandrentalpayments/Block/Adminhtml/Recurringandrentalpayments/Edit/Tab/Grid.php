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

class Indies_Recurringandrentalpayments_Block_Adminhtml_Recurringandrentalpayments_Edit_Tab_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

     public function __construct() {
        parent::__construct();
        $this->setTemplate('recurringandrentalpayments/widget-grid.phtml');
        $this->setId('productGrid');
        $this->setDefaultSort('entity_id');
        $this->setUseAjax(true);
        $this->setSaveParametersInSession(true);

        if ($this->getGroupId() && $this->_getSelectedProducts()) {
            $this->setDefaultFilter(array('massaction' => 1));
        }
    }

    public function getGroupId() {
        return (int) Mage::app()->getFrontController()->getRequest()->getParam('group_id');
    }

    protected function _getHelper() {
        return Mage::helper('recurringandrentalpayments');
    }

    protected function _getStore() {
        $storeId = (int) $this->getRequest()->getParam('store', 0);
        return Mage::app()->getStore($storeId);
    }

    protected function _prepareCollection() {
		$this->setDefaultFilter(array('massaction' => 1));
        
        $store = $this->_getStore();
		/* Start : Not display products in grid which are assigned in other plans */
		$plan_id = $this->getRequest()->getParam('id') ;
		if(isset($plan_id))   // If already any plan is created
		{
			$product_ids = Mage::getModel('recurringandrentalpayments/plans_product')->getCollection()
			->addFieldToFilter('plan_id', array('neq' => $plan_id));
		
			$items=array();
			
			if (sizeof($product_ids))
			{
				foreach ($product_ids as $product_id)
				{
					$items[] = $product_id['product_id'];	
				}
			}
			if(sizeof($items)==0)
			{
					$items[0]=0;
			}

			/* End : Not display products in grid which are assigned in other plans */
			$collection = Mage::getModel('catalog/product')->getCollection()
					->addAttributeToSelect('sku')
					->addAttributeToSelect('name')
					->addAttributeToSelect('attribute_set_id')
					->addAttributeToSelect('type_id')
					->addFieldToFilter('entity_id',array('nin' => $items))
				//	->addFieldToFilter('type_id', array('neq' => Mage_Catalog_Model_Product_Type::TYPE_GROUPED))
				//	->addFieldToFilter('type_id', array('neq' => Mage_Catalog_Model_Product_Type::TYPE_BUNDLE))
					->joinField('qty', 'cataloginventory/stock_item', 'qty', 'product_id=entity_id', '{{table}}.stock_id=1', 'left');
		}
		else   // If any plan is yet not create
		{
			$collection = Mage::getModel('catalog/product')->getCollection()
			->addAttributeToSelect('sku')
			->addAttributeToSelect('name')
			->addAttributeToSelect('attribute_set_id')
			->addAttributeToSelect('type_id')
		//	->addFieldToFilter('type_id', array('neq' => Mage_Catalog_Model_Product_Type::TYPE_GROUPED))
		//	->addFieldToFilter('type_id', array('neq' => Mage_Catalog_Model_Product_Type::TYPE_BUNDLE))
			->joinField('qty', 'cataloginventory/stock_item', 'qty', 'product_id=entity_id', '{{table}}.stock_id=1', 'left');
		}
		

		if ($store->getId()) {
            $collection->addStoreFilter($store);
            $collection->joinAttribute('custom_name', 'catalog_product/name', 'entity_id', null, 'inner', $store->getId());
            $collection->joinAttribute('status', 'catalog_product/status', 'entity_id', null, 'inner', $store->getId());
            $collection->joinAttribute('visibility', 'catalog_product/visibility', 'entity_id', null, 'inner', $store->getId());
            $collection->joinAttribute('price', 'catalog_product/price', 'entity_id', null, 'left', $store->getId());
        } else {
            $collection->addAttributeToSelect('price');
            $collection->joinAttribute('status', 'catalog_product/status', 'entity_id', null, 'inner');
            $collection->joinAttribute('visibility', 'catalog_product/visibility', 'entity_id', null, 'inner');
        }
        
        $this->setCollection($collection);        
        parent::_prepareCollection();
        
        return $this;
    }

    private function _getProductType() {
        $res = array();
        $type = Mage::getSingleton('catalog/product_type')->getOptionArray();
        if ($type) {
           foreach ($type as $key => $value) {
                //if ($key != Mage_Catalog_Model_Product_Type::TYPE_GROUPED
                      //  && $key != Mage_Catalog_Model_Product_Type::TYPE_BUNDLE) { //ab
                    $res[$key] = $value;
                //}
            }
        }
        return $res;
    }

    protected function _prepareColumns() {
        $helper = $this->_getHelper();

        $this->addColumn('entity_id', array(
            'header' => $helper->__('ID'),
            'index' => 'entity_id',
            'width' => 60,
            'align' => 'center'
        ));
        
        $this->addColumn('name', array(
            'header' => $helper->__('Name'),
            'index' => 'name',
        ));

        $this->addColumn('type', array(
            'header' => $helper->__('Type'),
            'width' => 100,
            'index' => 'type_id',
            'type' => 'options',
            'options' => $this->_getProductType(),
        ));
         $sets = Mage::getResourceModel('eav/entity_attribute_set_collection')
                ->setEntityTypeFilter(Mage::getModel('catalog/product')->getResource()->getTypeId())
                ->load()
                ->toOptionHash();

        $this->addColumn('set_name', array(
            'header' => $helper->__('Attrib. Set Name'),
            'width' => 100,
            'index' => 'attribute_set_id',
            'type' => 'options',
            'options' => $sets,
        ));

        $this->addColumn('sku', array(
            'header' => $helper->__('SKU'),
            'width' => 100,
            'index' => 'sku',
        ));

        $this->addColumn('price', array(
            'header' => $helper->__('Price'),
            'index' => 'price',
            'type' => 'currency',
            'currency_code'
            => (string) Mage::getStoreConfig(Mage_Directory_Model_Currency::XML_PATH_CURRENCY_BASE),
        ));

        $this->addColumn('qty', array(
            'header' => $helper->__('Qty'),
            'width' => 100,
            'index' => 'qty',
            'type' => 'number',
            'validate_class'
            => 'validate-number',
        ));

        $this->addColumn('visibility', array(
            'header' => $helper->__('Visibility'),
            'width' => 70,
            'index' => 'visibility',
            'type' => 'options',
            'options' => Mage::getSingleton('catalog/product_visibility')->getOptionArray(),
        ));

        $this->addColumn('status', array(
            'header' => $helper->__('Status'),
            'width' => 70,
            'index' => 'status',
            'type' => 'options',
            'options' => Mage::getSingleton('catalog/product_status')->getOptionArray(),
        ));

        return parent::_prepareColumns();
    }

    protected function _prepareMassaction() {
	
        $this->setMassactionIdField('entity_id');
        $massBlock = $this->getMassactionBlock();
        $massBlock->setTemplate('recurringandrentalpayments/widget-grid-massaction.phtml');
        $massBlock->addItem(null, array());

        $productIds = $this->_getSelectedProducts();
		
        if ($productIds) {
            $massBlock->getRequest()->setParam($massBlock->getFormFieldNameInternal(), $productIds);
        }

        return $this;
    }

    private function _getSelectedProducts() {
       
	    $productIds = '';
        $session = Mage::getSingleton('adminhtml/session');
		
        if ($data = $session->getData('recurringandrentalpayments_data')) {
            if (isset($data['in_products'])) {
                $productIds = $data['in_products'];
            }
            $session->setData('recurringandrentalpayments_data', null);
        } elseif (Mage::registry('recurringandrentalpayments_data_detail')) {
            $productIds = Mage::registry('recurringandrentalpayments_data_detail')->getData('product_ids');
        }

		$items = array();
		if($this->getRequest()->getParam('id') > 0)
		{
			$id = $this->getRequest()->getParam('id') ;
			$collection = Mage::getModel('recurringandrentalpayments/plans_product')->getCollection()
				  ->addFieldToFilter('plan_id',$id);
			
			foreach ($collection as $product)
			{
				$items[] = $product['product_id'];	
			}
		}
		
		$productIds = implode(",", $items);
        return $productIds;
    }
    
    public function category_filter($collection, $column) {
        $cond = $column->getFilter()->getCondition();
        if (empty($cond['eq'])) {
            return true;
        }
        $where = 'category.category_id = ' . $cond['eq'];
        $collection->getSelect()->where($where);
    }
    
    
    public function getGridUrl() {
        $url = $this->getUrl('*/*/productGrid', array('_current' => true));
        if (strpos($url, 'internal_massaction') !== false) {
            $url = substr($url, 0, strpos($url, 'internal_massaction'));
        }
        return $url;
    }
}
