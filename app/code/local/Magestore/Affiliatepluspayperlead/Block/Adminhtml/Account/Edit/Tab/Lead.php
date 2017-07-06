<?php

class Magestore_Affiliatepluspayperlead_Block_Adminhtml_Account_Edit_Tab_Lead extends Mage_Adminhtml_Block_Widget_Grid {

    public function __construct() {
        parent::__construct();
        $this->setId('leadGrid');
        $this->setDefaultSort('payperlead_lead_id');
        $this->setDefaultDir('DESC');
        $this->setUseAjax(true);
    }

    protected function _prepareCollection() {
        $account_id = $this->getRequest()->getParam('id');
        $collection = Mage::getModel('affiliateplus/transaction')
                ->getCollection()
                ->addFieldToFilter('type', array('in' => array(4, 5)))
                ->addFieldToFilter('account_id', $account_id)
        ;
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns() {
        $currencyCode = Mage::app()->getStore()->getBaseCurrency()->getCode();
        $this->addColumn('transaction_id', array(
            'header' => Mage::helper('affiliatepluspayperlead')->__('ID'),
            'align' => 'right',
            'width' => '50px',
            'index' => 'transaction_id',
        ));
        $this->addColumn('customer_email', array(
            'header' => Mage::helper('affiliatepluspayperlead')->__('Customer Email'),
            'align' => 'left',
            'index' => 'customer_email',
        ));
        $this->addColumn('type', array(
            'header' => Mage::helper('affiliatepluspayperlead')->__('Action'),
            'align' => 'left',
            'width' => '80px',
            'index' => 'type',
            'type' => 'options',
            'options' => array(
                4 => 'Sign up for an account',
                5 => 'Subscribe to newsletters',
            ),
        ));
        $this->addColumn('commission', array(
            'header' => Mage::helper('affiliatepluspayperlead')->__('Commission'),
            'align' => 'left',
            'index' => 'commission',
            'type' => 'price',
            'currency_code' => $currencyCode,
        ));
        $this->addColumn('created_time', array(
            'header' => Mage::helper('affiliatepluspayperlead')->__('Date Created'),
            'align' => 'left',
            'type' => 'datetime',
            'index' => 'created_time',
        ));
        $this->addColumn('status', array(
            'header' => Mage::helper('affiliatepluspayperlead')->__('Status'),
            'align' => 'left',
            'width' => '80px',
            'index' => 'status',
            'type' => 'options',
            'options' => Mage::getSingleton('affiliatepluspayperlead/status')->getOptionArray(),
        ));



        $this->addExportType('*/*/exportCsv', Mage::helper('affiliatepluspayperlead')->__('CSV'));
        $this->addExportType('*/*/exportXml', Mage::helper('affiliatepluspayperlead')->__('XML'));

        return parent::_prepareColumns();
    }

    public function addColumn($columnId, $column) {
        // fix error duplicate name 
        $columnId = 'payperlead_' . $columnId;
        return parent::addColumn($columnId, $column);
    }

    public function getGridUrl() {
        //Changed By Adam 29/10/2015: Fix issue of SUPEE 6788 - in Magento 1.9.2.2
        return $this->getData('grid_url') ? $this->getData('grid_url') : $this->getUrl('adminhtml/affiliatepluspayperlead_affiliatepluspayperlead/leadGrid', array('_current' => true, 'id' => $this->getRequest()->getParam('id')));
    }

//    public function getRowUrl($row) {
//        return $this->getUrl('affiliatepluspayperleadadmin/adminhtml_affiliatepluspayperlead/edit', array('id' => $row->getId()));
//    }

}
