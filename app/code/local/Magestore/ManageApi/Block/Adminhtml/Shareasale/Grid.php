<?php

class Magestore_ManageApi_Block_Adminhtml_Shareasale_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('shareasaleGrid');
        $this->setDefaultSort('shareasale_id');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getModel('manageapi/shareasale')->getCollection();
        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('shareasale_id', array(
            'header' => Mage::helper('manageapi')->__('ID'),
            'align' => 'right',
            'width' => '50px',
            'index' => 'shareasale_id',
        ));

        $this->addColumn('transid', array(
            'header' => Mage::helper('manageapi')->__('TransID'),
            'align' => 'left',
            'index' => 'transid',
        ));
        $this->addColumn('userid', array(
            'header' => Mage::helper('manageapi')->__('UserID'),
            'align' => 'left',
            'index' => 'userid',
        ));
        $this->addColumn('merchantid', array(
            'header' => Mage::helper('manageapi')->__('MerchantID'),
            'align' => 'left',
            'index' => 'merchantid',
        ));
        $this->addColumn('transdate', array(
            'header' => Mage::helper('manageapi')->__('TransDate'),
            'align' => 'left',
            'index' => 'transdate',
            'type' => 'date',
        ));
        $this->addColumn('transamount', array(
            'header' => Mage::helper('manageapi')->__('TransAmount'),
            'align' => 'left',
            'index' => 'transamount',
        ));
        $this->addColumn('commission', array(
            'header' => Mage::helper('manageapi')->__('Commission'),
            'align' => 'left',
            'index' => 'commission',
        ));
        $this->addColumn('comment', array(
            'header' => Mage::helper('manageapi')->__('Comment'),
            'align' => 'left',
            'index' => 'comment',
        ));
        $this->addColumn('voided', array(
            'header' => Mage::helper('manageapi')->__('Voided'),
            'align' => 'left',
            'index' => 'voided',
        ));
        $this->addColumn('pendingdate', array(
            'header' => Mage::helper('manageapi')->__('PendingDate'),
            'align' => 'left',
            'index' => 'pendingdate',
            'type' => 'date',
        ));
        $this->addColumn('locked', array(
            'header' => Mage::helper('manageapi')->__('Locked'),
            'align' => 'left',
            'index' => 'locked',
        ));
        $this->addColumn('affcomment', array(
            'header' => Mage::helper('manageapi')->__('AffComment'),
            'align' => 'left',
            'index' => 'affcomment',
        ));
        $this->addColumn('bannerpage', array(
            'header' => Mage::helper('manageapi')->__('BannerPage'),
            'align' => 'left',
            'index' => 'bannerpage',
        ));
        $this->addColumn('reversaldate', array(
            'header' => Mage::helper('manageapi')->__('ReversalDate'),
            'align' => 'left',
            'index' => 'reversaldate',
        ));
        $this->addColumn('clickdate', array(
            'header' => Mage::helper('manageapi')->__('ClickDate'),
            'align' => 'left',
            'index' => 'clickdate',
        ));
        $this->addColumn('revenue', array(
            'header' => Mage::helper('manageapi')->__('Revenue'),
            'align' => 'left',
            'index' => 'revenue',
        ));
        $this->addColumn('clicktime', array(
            'header' => Mage::helper('manageapi')->__('ClickTime'),
            'align' => 'left',
            'index' => 'clicktime',
        ));
        $this->addColumn('bannerid', array(
            'header' => Mage::helper('manageapi')->__('BannerID'),
            'align' => 'left',
            'index' => 'bannerid',
        ));

        $this->addColumn('skulist', array(
            'header' => Mage::helper('manageapi')->__('SkuList'),
            'align' => 'left',
            'index' => 'skulist',
        ));

        $this->addColumn('quantitylist', array(
            'header' => Mage::helper('manageapi')->__('QuantityList'),
            'align' => 'left',
            'index' => 'quantitylist',
        ));

        $this->addColumn('lockdate', array(
            'header' => Mage::helper('manageapi')->__('LockDate'),
            'align' => 'left',
            'index' => 'lockdate',
            'type' => 'date',
        ));

        $this->addColumn('paiddate', array(
            'header' => Mage::helper('manageapi')->__('PaidDate'),
            'align' => 'left',
            'index' => 'paiddate',
            'type' => 'date',
        ));

        $this->addColumn('merchantorganization', array(
            'header' => Mage::helper('manageapi')->__('MerchantOrganization'),
            'align' => 'left',
            'index' => 'merchantorganization',
        ));
        $this->addColumn('merchantwebsite', array(
            'header' => Mage::helper('manageapi')->__('MerchantWebsite'),
            'align' => 'left',
            'index' => 'merchantwebsite',
        ));
        $this->addColumn('transtype', array(
            'header' => Mage::helper('manageapi')->__('TransType'),
            'align' => 'left',
            'index' => 'transtype',
        ));
        $this->addColumn('merchantdefinedtype', array(
            'header' => Mage::helper('manageapi')->__('MerchantDefindType'),
            'align' => 'left',
            'index' => 'merchantdefinedtype',
        ));
        $this->addColumn('created_time', array(
            'header' => Mage::helper('manageapi')->__('Created At'),
            'align' => 'left',
            'index' => 'created_time',
            'type' => 'date'
        ));


        $this->addExportType('*/*/exportCsv', Mage::helper('manageapi')->__('CSV'));
        $this->addExportType('*/*/exportXml', Mage::helper('manageapi')->__('XML'));

        return parent::_prepareColumns();
    }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('shareasale_id');
        $this->getMassactionBlock()->setFormFieldName('manageapi');

        $this->getMassactionBlock()->addItem('delete', array(
            'label' => Mage::helper('manageapi')->__('Delete'),
            'url' => $this->getUrl('*/*/massDelete'),
            'confirm' => Mage::helper('manageapi')->__('Are you sure?')
        ));

        return $this;
    }

    public function getRowUrl($row)
    {
    }
}