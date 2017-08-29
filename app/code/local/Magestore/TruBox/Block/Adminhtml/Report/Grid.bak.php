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

    protected function _prepareCollection()
    {
        $productsTableName = Mage::getSingleton('core/resource')->getTableName('catalog_product_entity_varchar');
        $catalogInventoryStockStatusTable = Mage::getSingleton('core/resource')->getTableName('cataloginventory_stock_status');

        $entityTypeId = Mage::getModel('eav/entity')
            ->setType('catalog_product')
            ->getTypeId();

        $prodNameAttrId = Mage::getModel('eav/entity_attribute')
            ->loadByCode($entityTypeId, 'name')
            ->getAttributeId();

        $collection = Mage::getModel('trubox/item')->getCollection();

        $collection->getSelect()->joinLeft(
            array('cpev' => $productsTableName),
            'cpev.entity_id=main_table.product_id AND cpev.attribute_id=' . $prodNameAttrId . '',
            array('product_name' => 'value')
        );

        $collection->getSelect()->join(
            array('iv' => $catalogInventoryStockStatusTable),
            'iv.product_id = main_table.product_id',
            array(
                'stock_status' => 'iv.stock_status',
                'stock_qty' => 'iv.qty',
            )
        );

        $collection->getSelect()->columns(array('trubox_qty' => new Zend_Db_Expr ('SUM(main_table.qty)')));
        $collection->getSelect()->columns(array('revenue' => new Zend_Db_Expr ('ROUND(SUM(main_table.qty) * main_table.price,2)')));

        $collection->getSelect()->where('main_table.product_id > 0');

        $collection ->getSelect()->group('main_table.product_id');

        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        /*$this->addColumn('item_id', array(
            'header' => Mage::helper('trubox')->__('ID'),
            'align' => 'right',
            'width' => '50px',
            'index' => 'item_id',
        ));*/

        $this->addColumn('product_id', array(
            'header' => Mage::helper('trubox')->__('ID'),
            'align' => 'right',
            'width' => '50px',
            'index' => 'product_id',
        ));

        $this->addColumn('product_name', array(
            'header' => Mage::helper('trubox')->__('Product Name'),
            'index' => 'product_name',
            'renderer' => 'Magestore_TruBox_Block_Adminhtml_Renderer_Items_Name',
            'filter_condition_callback' => array($this, '_filterProductNameCallback')
        ));

        $this->addColumn('price', array(
            'header' => Mage::helper('trubox')->__('Product Price'),
            'type' => 'price',
            'currency_code' => Mage::app()->getStore()->getBaseCurrency()->getCode(),
            'index' => 'price',
        ));

        $this->addColumn('trubox_qty', array(
            'header' => Mage::helper('trubox')->__('TruBox Qty'),
            'index' => 'trubox_qty',
            'type' => 'number',
            'align'     =>'left',
            'filter_condition_callback' => array($this, '_filterQtyCallback')
        ));

        $this->addColumn('revenue', array(
            'header' => Mage::helper('trubox')->__('TruBox Revenue'),
            'index' => 'revenue',
            'type' => 'number',
            'align'     =>'left',
            'filter_index'     =>'revenue',
            'renderer' => 'Magestore_TruBox_Block_Adminhtml_Renderer_TruBox_Revenue',
            'filter_condition_callback' => array($this, '_filterRevenueCallback')
        ));

        $this->addColumn('stock_qty', array(
            'header' => Mage::helper('trubox')->__('Inventory Qty'),
            'index' => 'stock_qty',
            'type' => 'number',
            'align'     =>'left',
            'filter_index'     =>'iv.qty',
        ));

        $this->addColumn('stock_status', array(
            'header'=> Mage::helper('trubox')->__('Status'),
            'index' => 'stock_status',
            'type'  => 'options',
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
            'align'     =>'left',
            'renderer' => 'Magestore_TruBox_Block_Adminhtml_Renderer_TruBox_Point',
            'filter_condition_callback' => array($this, '_filterPointCallback')
        ));

        $this->addExportType('*/*/exportCsv', Mage::helper('trubox')->__('CSV'));
        $this->addExportType('*/*/exportXml', Mage::helper('trubox')->__('XML'));

        return parent::_prepareColumns();
    }

    public function getRowUrl($row)
    {
//        return $this->getUrl('*/*/edit', array('id' => $row->getId()));
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
        if (!in_array($value, array(0,1))) {
            return $this;
        }

        $collection->getSelect()->where('iv.stock_status = '.$value);

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
        $collection->getSelect()->having('SUM(main_table.qty) >= '.$from.' and  SUM(main_table.qty) <= '.$to);
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

        return $this;
    }

}
