<?php
/**
 * Magestore
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the Magestore.com license that is
 * available through the world-wide-web at this URL:
 * http://www.magestore.com/license-agreement.html
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category    Magestore
 * @package     Magestore_truwallet
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * truwallet Tab on Customer Edit Form Block
 * 
 * @category    Magestore
 * @package     Magestore_truwallet
 * @author      Magestore Developer
 */
class Magestore_TruWallet_Block_Adminhtml_Customer_Edit_Tab_History
    extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('truwalletTransactionGrid');
        $this->setDefaultSort('truwallet_transaction_transaction_id');
        $this->setDefaultDir('DESC');
        $this->setUseAjax(true);
    }
    
    /**
     * prepare collection for block to display
     *
     * @return Magestore_truwallet_Block_Adminhtml_Customer_Edit_Tab_History
     */
    protected function _prepareCollection()
    {
        $collection = Mage::getSingleton('truwallet/transaction')->getCollection()
            ->addFieldToFilter('customer_id', $this->getRequest()->getParam('id'));
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }
    
    /**
     * prepare columns for this grid
     *
     * @return Magestore_truwallet_Block_Adminhtml_Customer_Edit_Tab_History
     */
    protected function _prepareColumns()
    {
        $this->addColumn('transaction_id', array(
            'header'    => Mage::helper('truwallet')->__('ID'),
            'align'     =>'right',
            'width'     => '50px',
            'index'     => 'transaction_id',
            'type'      => 'number',
        ));
        
        $this->addColumn('title', array(
            'header'    => Mage::helper('truwallet')->__('Title'),
            'align'     =>'left',
            'index'     => 'title',
        ));
        
        $this->addColumn('action_type', array(
            'header'    => Mage::helper('truwallet')->__('Action'),
            'align'     => 'left',
            'index'     => 'action_type',
            'type'      => 'options',
            'options'   => Mage::getSingleton('truwallet/type')->getOptionArray(),
        ));

        $this->addColumn('current_credit', array(
            'header'    => Mage::helper('truwallet')->__('Current Credits'),
            'align'     => 'right',
            'index'     => 'current_credit',
            'type'      => 'number',
        ));

        $this->addColumn('created_time', array(
            'header'    => Mage::helper('truwallet')->__('Created On'),
            'index'     => 'created_time',
            'type'      => 'datetime',
        ));

        $this->addColumn('updated_time', array(
            'header'    => Mage::helper('truwallet')->__('Updated On'),
            'index'     => 'updated_time',
            'type'      => 'datetime',
        ));

        $this->addColumn('expiration_date', array(
            'header'    => Mage::helper('truwallet')->__('Expires On'),
            'index'     => 'expiration_date',
            'type'      => 'datetime',
        ));

        $this->addColumn('order_id', array(
            'header'    => Mage::helper('truwallet')->__('Order'),
            'index'     => 'order_id',
        ));

        $this->addColumn('receiver_email', array(
            'header'    => Mage::helper('truwallet')->__('Receiver Email'),
            'index'     => 'receiver_email',
        ));

        $this->addColumn('status', array(
            'header'    => Mage::helper('truwallet')->__('Status'),
            'align'     => 'left',
            'index'     => 'status',
            'type'      => 'options',
            'options'   => Mage::getSingleton('truwallet/status')->getTransactionOptionArray(),
        ));
        
        $this->addColumn('store_id', array(
            'header'    => Mage::helper('truwallet')->__('Store View'),
            'align'     => 'left',
            'index'     => 'store_id',
            'type'      => 'options',
            'options'   => Mage::getModel('adminhtml/system_store')->getStoreOptionHash(true),
        ));
        
        $this->addExportType('adminhtml/reward_customer/exportCsv'
            , Mage::helper('truwallet')->__('CSV')
        );
        $this->addExportType('adminhtml/reward_customer/exportXml'
            , Mage::helper('truwallet')->__('XML')
        );
        return parent::_prepareColumns();
    }
    
    /**
     * Add column to grid
     *
     * @param   string $columnId
     * @param   array || Varien_Object $column
     * @return  Magestore_truwallet_Block_Adminhtml_Customer_Edit_Tab_History
     */
    public function addColumn($columnId, $column)
    {
        $columnId = 'truwallet_transaction_' . $columnId;
        return parent::addColumn($columnId, $column);
    }
    
    /**
     * get url for each row in grid
     *
     * @return string
     */
    public function getRowUrl($row)
    {
//        return $this->getUrl('adminhtml/reward_transaction/edit', array('id' => $row->getId()));
    }
    
    /**
     * get grid url (use for ajax load)
     * 
     * @return string
     */
    public function getGridUrl()
    {
//       return $this->getUrl('adminhtml/reward_customer/grid', array('_current' => true));
    }
}
