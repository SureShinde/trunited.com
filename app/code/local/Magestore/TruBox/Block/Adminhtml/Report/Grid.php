<?php

class Magestore_TruBox_Block_Adminhtml_Report_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('truboxGrid');
        $this->setDefaultSort('item_id');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
    }

    protected function _getStore()
    {
        $storeId = (int)$this->getRequest()->getParam('store', 0);
        return Mage::app()->getStore($storeId);
    }

    protected function _prepareCollection()
    {
        $item_collection = Mage::getModel('trubox/item')->getCollection()
            ->addFieldToSelect('item_id')
            ->addFieldToSelect('product_id')
            ->addFieldToSelect('qty')
            ->addFieldToSelect('price')
            ->addFieldToSelect('option_params')
            ->addFieldToFilter('product_id', array('gt' => 0));
        $item_collection->getSelect()->columns(array('trubox_qty' => new Zend_Db_Expr ('SUM(qty)')));
        $item_collection->getSelect()->columns(array('revenue' => new Zend_Db_Expr ('ROUND(SUM(qty) * price,2)')));
        $item_collection->getSelect()->group('product_id');

        $catalogInventoryStockStatusTable = Mage::getSingleton('core/resource')->getTableName('cataloginventory_stock_status');

        $storeId = (int)$this->getRequest()->getParam('store', 0);
        $store = Mage::app()->getStore($storeId);
        $collection = Mage::getModel('catalog/product')->getCollection()
            ->addAttributeToSelect('sku')
            ->addAttributeToSelect('name')
            ->addAttributeToSelect('attribute_set_id')
            ->addAttributeToSelect('rewardpoints_earn')
            ->addAttributeToSelect('type_id')
            ->addAttributeToFilter('entity_id', array('in' => $item_collection->getColumnValues('product_id')));

        if (Mage::helper('catalog')->isModuleEnabled('Mage_CatalogInventory')) {
            $collection->joinField('qty',
                'cataloginventory/stock_item',
                'qty',
                'product_id=entity_id',
                '{{table}}.stock_id=1',
                'left');
        }

        $collection->getSelect()->join(
            array('iv' => $catalogInventoryStockStatusTable),
            'iv.product_id = entity_id',
            array(
                'stock_status' => 'iv.stock_status',
                'stock_qty' => 'iv.qty',
            )
        );

        if ($store->getId()) {
            //$collection->setStoreId($store->getId());
            $adminStore = Mage_Core_Model_App::ADMIN_STORE_ID;
            $collection->addStoreFilter($store);
            $collection->joinAttribute(
                'name',
                'catalog_product/name',
                'entity_id',
                null,
                'inner',
                $adminStore
            );
            $collection->joinAttribute(
                'status',
                'catalog_product/status',
                'entity_id',
                null,
                'inner',
                $store->getId()
            );
            $collection->joinAttribute(
                'visibility',
                'catalog_product/visibility',
                'entity_id',
                null,
                'inner',
                $store->getId()
            );
            $collection->joinAttribute(
                'price',
                'catalog_product/price',
                'entity_id',
                null,
                'left',
                $store->getId()
            );
        } else {
            $collection->addAttributeToSelect('price');
            $collection->joinAttribute('status', 'catalog_product/status', 'entity_id', null, 'inner');
            $collection->joinAttribute('visibility', 'catalog_product/visibility', 'entity_id', null, 'inner');
        }
        $collection->joinAttribute(
            'rewardpoints_earn',
            'catalog_product/rewardpoints_earn',
            'entity_id',
            null,
            'inner',
            Mage_Core_Model_App::ADMIN_STORE_ID
        );

        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('entity_id', array(
            'header' => Mage::helper('trubox')->__('ID'),
            'align' => 'right',
            'width' => '50px',
            'index' => 'entity_id',
        ));

        $this->addColumn('name',
            array(
                'header' => Mage::helper('catalog')->__('Product Name'),
                'index' => 'name',
            )
        );

        $store = $this->_getStore();
        $this->addColumn('price',
            array(
                'header' => Mage::helper('catalog')->__('Product Price'),
                'type' => 'price',
                'currency_code' => $store->getBaseCurrency()->getCode(),
                'index' => 'price',
            ));

        $this->addColumn('trubox_qty', array(
            'header' => Mage::helper('trubox')->__('TruBox Qty'),
            'index' => 'trubox_qty',
            'type' => 'number',
            'align' => 'right',
            'renderer' => 'Magestore_TruBox_Block_Adminhtml_Renderer_TruBox_Qty',
            'filter_condition_callback' => array($this, '_filterQtyCallback')
        ));

        $this->addColumn('revenue', array(
            'header' => Mage::helper('trubox')->__('TruBox Revenue'),
            'index' => 'revenue',
            'type' => 'number',
            'align' => 'right',
            'filter_index' => 'revenue',
            'renderer' => 'Magestore_TruBox_Block_Adminhtml_Renderer_TruBox_Revenue',
            'filter_condition_callback' => array($this, '_filterRevenueCallback')
        ));

        if (Mage::helper('catalog')->isModuleEnabled('Mage_CatalogInventory')) {
            $this->addColumn('qty',
                array(
                    'header' => Mage::helper('catalog')->__('Inventory Qty'),
                    'width' => '100px',
                    'type' => 'number',
                    'index' => 'qty',
                ));
        }

        $this->addColumn('stock_status', array(
            'header' => Mage::helper('trubox')->__('Status'),
            'index' => 'stock_status',
            'type' => 'options',
            'align' => 'right',
            'options' => array(
                '1' => 'In stock',
                '0' => 'Out of stock'
            ),
            'renderer' => 'Magestore_TruBox_Block_Adminhtml_Renderer_Product_Stock',
            'filter_condition_callback' => array($this, '_filterStockStatusCallback')
        ));

        $this->addColumn('points', array(
            'header' => Mage::helper('trubox')->__('TruBox Points'),
            'index' => 'points',
            'type' => 'number',
            'align' => 'right',
            'renderer' => 'Magestore_TruBox_Block_Adminhtml_Renderer_TruBox_Point',
            'filter_condition_callback' => array($this, '_filterPointCallback')
        ));

        $this->addExportType('*/*/exportCsv', Mage::helper('trubox')->__('CSV'));
        $this->addExportType('*/*/exportXml', Mage::helper('trubox')->__('XML'));

        return parent::_prepareColumns();
    }

    public function getRowUrl($row)
    {
        return Mage::helper("adminhtml")->getUrl('adminhtml/catalog_product/edit', array('id' => $row->getId()));
    }

    protected function _filterProductNameCallback($collection, $column)
    {
        if (!$value = $column->getFilter()->getValue()) {
            return $this;
        }

        if (!empty($value)) {
            $products = Mage::getModel('catalog/product')->getCollection()
                ->addAttributeToSelect('entity_id')
                ->addAttributeToFilter('name', array('like' => '%' . $value . '%'));

            $rs = array();
            if (sizeof($products) > 0) {
                foreach ($products as $product) {
                    $rs[] = $product->getId();
                }
            }
            if (sizeof($rs) == 0)
                $collection->getSelect()->where('main_table.product_id IS NULL');
            else
                $collection->getSelect()->where('main_table.product_id IN (' . implode(',', $rs) . ')');
        }


        return $this;
    }

    protected function _filterStockStatusCallback($collection, $column)
    {
        $value = $column->getFilter()->getValue();
        if (!in_array($value, array(0, 1))) {
            return $this;
        }

        $collection->getSelect()->where('iv.stock_status = ' . $value);

        return $this;
    }

    public function _filterQtyCallback($collection, $column)
    {
        if (!$value = $column->getFilter()->getValue()) {
            return $this;
        }
        $values = $column->getFilter()->getValue();
        $from = $values['from'];
        $to = $values['to'];

        $resource = Mage::getSingleton('core/resource');
        $readConnection = $resource->getConnection('core_read');

        $item_collection = Mage::getModel('trubox/item')->getCollection()
            ->addFieldToSelect('product_id')
            ->addFieldToSelect('qty')
            ->addFieldToFilter('product_id', array('gt' => 0));

        $item_collection->getSelect()->group('product_id');
        if ($from != null && $to != null)
            $item_collection->getSelect()->having('SUM(qty) <= ' . $to . ' and SUM(qty) >= ' . $from);
        else if ($from == null && $to != null)
            $item_collection->getSelect()->having('SUM(qty) <= ' . $to);
        else if ($from != null && $to == null)
            $item_collection->getSelect()->having('SUM(qty) >= ' . $from);

        $qty = $readConnection->fetchCol($item_collection->getSelect());
        $collection->addAttributeToFilter('entity_id', array('in' => $qty));
        return $this;
    }

    public function _filterRevenueCallback($collection, $column)
    {
        if (!$value = $column->getFilter()->getValue()) {
            return $this;
        }
        $values = $column->getFilter()->getValue();
        $from = $values['from'];
        $to = $values['to'];

        $resource = Mage::getSingleton('core/resource');
        $readConnection = $resource->getConnection('core_read');
        $table = $resource->getTableName('trubox/item');
        if ($from != null && $to != null)
            $query = 'SELECT product_id, price FROM ' . $table . ' GROUP BY product_id HAVING (SUM(qty) * price) >= ' . $from . ' and (SUM(qty) * price) <=' . $to;
        else if ($from == null && $to != null)
            $query = 'SELECT product_id, price FROM ' . $table . ' GROUP BY product_id HAVING (SUM(qty) * price) <=' . $to;
        else if ($from != null && $to == null)
            $query = 'SELECT product_id, price FROM ' . $table . ' GROUP BY product_id HAVING (SUM(qty) * price) >= ' . $from;
        $revenue = $readConnection->fetchCol($query);
        $collection->addAttributeToFilter('entity_id', array('in' => $revenue));
        return $this;
    }

    public function _filterPointCallback($collection, $column)
    {
        if (!$value = $column->getFilter()->getValue()) {
            return $this;
        }
        $values = $column->getFilter()->getValue();
        $from = $values['from'];
        $to = $values['to'];
        $resource = Mage::getSingleton('core/resource');
        $table = $resource->getTableName('trubox/item');

        if ($from != null && $to != null) {
            $collection->getSelect()->where('at_rewardpoints_earn.value * (SELECT SUM(qty) FROM ' . $table . ' WHERE product_id = e.entity_id GROUP BY product_id) >=' . $from . ' and at_rewardpoints_earn.value * (SELECT SUM(qty) FROM ' . $table . ' WHERE product_id = e.entity_id GROUP BY product_id) <= ' . $to);
        } else if ($from == null && $to != null) {
            $collection->getSelect()->where('at_rewardpoints_earn.value * (SELECT SUM(qty) FROM ' . $table . ' WHERE product_id = e.entity_id GROUP BY product_id) <= ' . $to);
        } else if ($from != null && $to == null) {
            $collection->getSelect()->where('at_rewardpoints_earn.value * (SELECT SUM(qty) FROM ' . $table . ' WHERE product_id = e.entity_id GROUP BY product_id) >=' . $from);
        }
        return $this;
    }

}