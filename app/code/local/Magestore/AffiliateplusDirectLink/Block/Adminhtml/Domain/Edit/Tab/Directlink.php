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
 * directlink Tab Edit Domain Block
 * 
 * @category    Magestore
 * @package     Magestore_AffiliateplusDirectLink
 * @author      Magestore Developer
 */
class Magestore_AffiliateplusDirectLink_Block_Adminhtml_Domain_Edit_Tab_Directlink extends Mage_Adminhtml_Block_Widget_Grid {

    public function __construct() {
        parent::__construct();
        $this->setId('directlinkGrid');
        $this->setDefaultSort('direct_link_id');
        $this->setDefaultDir('ASC');
        $this->setUseAjax(true);
        $this->setSaveParametersInSession(true);
    }

    /**
     * prepare collection for block to display
     *
     * @return Magestore_AffiliateplusDirectLink_Block_Adminhtml_Domain_Edit_Tab_Directlink
     */
    protected function _prepareCollection() {
        $collection = Mage::getModel('affiliateplusdirectlink/directlink')->getCollection();
        $domain_id = $this->getRequest()->getParam('id');
        $collection->getSelect()
                ->join(array('adld' => Mage::getModel('core/resource')->getTableName('affiliateplus_direct_link_domain')), 'main_table.domain_id =adld.domain_id AND main_table.domain_id=' . $domain_id, array('domain', 'source_page' => 'CONCAT(domain, "/", source_page)'))
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

        $this->addColumn('source_page', array(
            'header' => Mage::helper('affiliateplusdirectlink')->__('Page To Show Banner'),
            'width' => '150px',
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

        $this->addColumn('link', array(
            'header' => Mage::helper('affiliateplusdirectlink')->__('Direct Link'),
            'width' => '300px',
            'index' => 'link',
            'filter_index' => 'main_table.link',
            'renderer' => 'affiliateplusdirectlink/adminhtml_renderer_directlink',
        ));

        $this->addColumn('action', array(
            'header' => Mage::helper('affiliateplusdirectlink')->__('Action'),
            'width' => '100',
            'type' => 'action',
            'getter' => 'getId',
            'actions' => array(
                array(
                    'caption' => Mage::helper('affiliateplusdirectlink')->__('Edit'),
                    'url' => array('base' => 'adminhtml/affiliateplusdirectlink_affiliateplusdirectlink/edit'),            //Changed By Adam 29/10/2015: Fix issue of SUPEE 6788 - in Magento 1.9.2.2
                    'field' => 'id'
            )),
            'filter' => false,
            'sortable' => false,
            'index' => 'stores',
            'is_system' => true,
        ));
        return parent::_prepareColumns();
    }

    public function getGridUrl() {
        return $this->getData('grid_url') ? $this->getData('grid_url') : $this->getUrl('*/*/directlink', array('_current' => true));
    }

}