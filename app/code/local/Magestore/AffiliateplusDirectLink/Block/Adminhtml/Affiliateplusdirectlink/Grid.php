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
 * @package     Magestore_AffiliateplusDirectLink
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * Affiliateplusdirectlink Grid Block
 * 
 * @category    Magestore
 * @package     Magestore_AffiliateplusDirectLink
 * @author      Magestore Developer
 */
class Magestore_AffiliateplusDirectLink_Block_Adminhtml_Affiliateplusdirectlink_Grid extends Mage_Adminhtml_Block_Widget_Grid {

    public function __construct() {
        parent::__construct();
        $this->setId('affiliateplusdirectlinkGrid');
        $this->setDefaultSort('direct_link_id');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
    }

    /**
     * prepare collection for block to display
     *
     * @return Magestore_AffiliateplusDirectLink_Block_Adminhtml_Affiliateplusdirectlink_Grid
     */
    protected function _prepareCollection() {
        $collection = Mage::getModel('affiliateplusdirectlink/directlink')->getCollection();
        $collection->getSelect()
                ->join(array('adld' => Mage::getModel('core/resource')->getTableName('affiliateplus_direct_link_domain')), 'main_table.domain_id =adld.domain_id', array('domain',  'source_page' => 'CONCAT(domain, "/", source_page)'))
                ->join(array('aa' => Mage::getModel('core/resource')->getTableName('affiliateplus_account')), 'aa.account_id =adld.account_id', array('name', 'email'))
                ->join(array('ab' => Mage::getModel('core/resource')->getTableName('affiliateplus_banner')), 'main_table.banner_id =ab.banner_id', array('title'));
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * prepare columns for this grid
     *
     * @return Magestore_AffiliateplusDirectLink_Block_Adminhtml_Affiliateplusdirectlink_Grid
     */
    protected function _prepareColumns() {
        $this->addColumn('direct_link_id', array(
            'header' => Mage::helper('affiliateplusdirectlink')->__('ID'),
            'align' => 'right',
            'width' => '50px',
            'index' => 'direct_link_id',
        ));

        $this->addColumn('email', array(
            'header' => Mage::helper('affiliateplusdirectlink')->__('Affiliate Account'),
            'align' => 'left',
            'index' => 'email',
            'renderer'=> 'affiliateplusdirectlink/adminhtml_renderer_email',
        ));
        
        $this->addColumn('link', array(
            'header' => Mage::helper('affiliateplusdirectlink')->__('Direct Link'),
            'index' => 'link',
            'filter_index' => 'main_table.link',
            'renderer'=> 'affiliateplusdirectlink/adminhtml_renderer_directlink',
        ));

        $this->addColumn('domain', array(
            'header' => Mage::helper('affiliateplusdirectlink')->__('Domain'),
            'align' => 'left',
            'index' => 'domain',
            'renderer'=> 'affiliateplusdirectlink/adminhtml_renderer_domain',
        ));

        $this->addColumn('source_page', array(
            'header' => Mage::helper('affiliateplusdirectlink')->__('Page To Show Banner'),
            'index' => 'source_page',
            'filter_index' => 'CONCAT(domain, "/", source_page)',
            'renderer'=> 'affiliateplusdirectlink/adminhtml_renderer_sourcepage',
        ));

        $this->addColumn('title', array(
            'header' => Mage::helper('affiliateplusdirectlink')->__('Banner'),
            'width' => '150px',
            'index' => 'title',
            'renderer'=> 'affiliateplusdirectlink/adminhtml_renderer_banner',
        ));

        


        $this->addColumn('status', array(
            'header' => Mage::helper('affiliateplusdirectlink')->__('Status'),
            'align' => 'left',
            'width' => '80px',
            'index' => 'status',
            'filter_index'=>'main_table.status',
            'type' => 'options',
            'options' => Mage::getSingleton('affiliateplusdirectlink/status')->getOptionArray(),
        ));

        $this->addColumn('action', array(
            'header' => Mage::helper('affiliateplusdirectlink')->__('Action'),
            'width' => '100',
            'type' => 'action',
            'getter' => 'getId',
            'actions' => array(
                array(
                    'caption' => Mage::helper('affiliateplusdirectlink')->__('Edit'),
                    'url' => array('base' => '*/*/edit'),
                    'field' => 'id'
            )),
            'filter' => false,
            'sortable' => false,
            'index' => 'stores',
            'is_system' => true,
        ));

        $this->addExportType('*/*/exportCsv', Mage::helper('affiliateplusdirectlink')->__('CSV'));
        $this->addExportType('*/*/exportXml', Mage::helper('affiliateplusdirectlink')->__('XML'));

        return parent::_prepareColumns();
    }

    /**
     * prepare mass action for this grid
     *
     * @return Magestore_AffiliateplusDirectLink_Block_Adminhtml_Affiliateplusdirectlink_Grid
     */
    protected function _prepareMassaction() {
        $this->setMassactionIdField('affiliateplusdirectlink_id');
        $this->getMassactionBlock()->setFormFieldName('affiliateplusdirectlink');

        $this->getMassactionBlock()->addItem('delete', array(
            'label' => Mage::helper('affiliateplusdirectlink')->__('Delete'),
            'url' => $this->getUrl('*/*/massDelete'),
            'confirm' => Mage::helper('affiliateplusdirectlink')->__('Are you sure?')
        ));
        return $this;
    }

    /**
     * get url for each row in grid
     *
     * @return string
     */
    public function getRowUrl($row) {
        return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }

}