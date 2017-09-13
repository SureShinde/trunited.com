<?php

class Magestore_Interest_Block_Adminhtml_Customer_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('customerGrid');
        $this->setDefaultSort('interest_customer_id');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
    }

    protected function _prepareCollection()
    {
        $interestTableName = Mage::getSingleton('core/resource')->getTableName('interest/interest');
        $customerTableName = Mage::getSingleton('core/resource')->getTableName('customer_entity');
        $customerVarcharTableName = Mage::getSingleton('core/resource')->getTableName('customer_entity_varchar');

        $collection = Mage::getModel('interest/customer')->getCollection();

        $collection->getSelect()
            ->joinLeft(
                array("it" => $interestTableName),
                "main_table.interest_id = it.interest_id",
                array("title")
            );

        $collection->getSelect()
            ->join(array('ce1' => $customerTableName), 'ce1.entity_id=main_table.customer_id', array('customer_email' => 'email'));

        $fn = Mage::getModel('eav/entity_attribute')->loadByCode('1', 'firstname');
        $ln = Mage::getModel('eav/entity_attribute')->loadByCode('1', 'lastname');
        $collection->getSelect()
            ->join(array('ce2' => $customerVarcharTableName), 'ce2.entity_id=main_table.customer_id', array('firstname' => 'value'))
            ->where('ce2.attribute_id=' . $fn->getAttributeId())
            ->join(array('ce3' => $customerVarcharTableName), 'ce3.entity_id=main_table.customer_id', array('lastname' => 'value'))
            ->where('ce3.attribute_id=' . $ln->getAttributeId())
            ->columns(new Zend_Db_Expr("CONCAT(`ce2`.`value`, ' ',`ce3`.`value`) AS customer_name"));


        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('interest_customer_id', array(
            'header' => Mage::helper('interest')->__('ID'),
            'align' => 'right',
            'width' => '50px',
            'index' => 'interest_customer_id',
        ));

        $this->addColumn('interest_id', array(
            'header' => Mage::helper('interest')->__('Interest ID'),
            'align' => 'left',
            'width' => '50px',
            'index' => 'interest_id',
        ));

        $this->addColumn('customer_id', array(
            'header' => Mage::helper('interest')->__('Customer ID'),
            'width' => '150px',
            'index' => 'customer_id',
        ));

        $this->addColumn('customer_name', array(
            'header' => Mage::helper('trubox')->__('Customer Name'),
            'index' => 'customer_name',
            'renderer' => 'Magestore_Interest_Block_Adminhtml_Renderer_Customer_Name',
            'filter_name' => 'customer_name',
            'filter_condition_callback' => array($this, '_filterCustomerNameCallback')
        ));

        $this->addColumn('customer_email', array(
            'header' => Mage::helper('Sales')->__('Customer Email'),
            'index' => 'customer_email',
            'type' => 'text',
            'renderer' => 'Magestore_Interest_Block_Adminhtml_Renderer_Customer_Email',
            'filter_index'  => 'ce1.email',
        ));

        $this->addColumn('title', array(
            'header' => Mage::helper('trubox')->__('Interest Title'),
            'align' => 'right',
            'index' => 'title',
            'renderer' => 'Magestore_Interest_Block_Adminhtml_Renderer_Interest_Item',
        ));

        $this->addColumn('created_at', array(
            'header' => Mage::helper('trubox')->__('Created At'),
            'align' => 'right',
            'index' => 'created_at',
            'type' => 'date'
        ));

        $this->addColumn('updated_at', array(
            'header' => Mage::helper('trubox')->__('Updated At'),
            'align' => 'right',
            'index' => 'updated_at',
            'type' => 'date'
        ));


        $this->addExportType('*/*/exportCsv', Mage::helper('interest')->__('CSV'));
        $this->addExportType('*/*/exportXml', Mage::helper('interest')->__('XML'));

        return parent::_prepareColumns();
    }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('interest_customer_id');
        $this->getMassactionBlock()->setFormFieldName('interest');

        $this->getMassactionBlock()->addItem('delete', array(
            'label' => Mage::helper('interest')->__('Delete'),
            'url' => $this->getUrl('*/*/massDelete'),
            'confirm' => Mage::helper('interest')->__('Are you sure?')
        ));

        return $this;
    }

    protected function _filterCustomerNameCallback($collection, $column)
    {
        if (!$value = $column->getFilter()->getValue()) {
            return $this;
        }

        $dir = $this->getParam($this->getVarNameDir(), $this->_defaultDir);

        if (!empty($value)) {
            $_customers = Mage::getModel('customer/customer')->getCollection()
                ->addAttributeToSelect('entity_id')
                ->addAttributeToSelect('firstname')
                ->addAttributeToFilter('firstname', array('like' => '%' . $value . '%'))
                ->setOrder('firstname', $dir);

            $rs_firstname = $_customers->getColumnValues('entity_id');

            $_customers_ = Mage::getModel('customer/customer')->getCollection()
                ->addAttributeToSelect('entity_id')
                ->addAttributeToSelect('lastname')
                ->addAttributeToFilter('lastname', array('like' => '%' . $value . '%'))
                ->setOrder('lastname', $dir);

            $rs_lastname = $_customers_->getColumnValues('entity_id');

            $rs = array_merge($rs_firstname, $rs_lastname);
            if (sizeof($rs) == 0)
                $collection->getSelect()->where('main_table.customer_id IS NULL');
            else
                $collection->getSelect()->where('main_table.customer_id IN (' . implode(',', $rs) . ')');
        }


        return $this;
    }

    public function getRowUrl($row)
    {
    }
}
