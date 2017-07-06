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
 * @package     Magestore_AffiliateplusPayPerClick
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * Domains Block
 * 
 * @category    Magestore
 * @package     Magestore_AffiliateplusDirectLink
 * @author      Magestore Developer
 */
class Magestore_AffiliateplusDirectLink_Block_Domains extends Mage_Core_Block_Template {

    /**
     * get Helper
     *
     * @return Magestore_Affiliateplus_Helper_Config
     */
    public function _getHelper() {
        return Mage::helper('affiliateplus/config');
    }

    protected function _construct() {
        parent::_construct();
        $account = Mage::getSingleton('affiliateplus/session')->getAccount();
        $collection = Mage::getModel('affiliateplusdirectlink/domain')->getDomainCollectionByAccountId($account->getId());
        $this->setCollection($collection);
    }

    public function _prepareLayout() {
        parent::_prepareLayout();
        $pager = $this->getLayout()->createBlock('page/html_pager', 'sales_pager')->setCollection($this->getCollection());
        $this->setChild('sales_pager', $pager);

        $grid = $this->getLayout()->createBlock('affiliateplus/grid', 'sales_grid');

//        $grid->addColumn('domain_id', array(
//            'header' => $this->__('ID'),
//            'index' => 'domain_id',
//            'align' => 'left',
//            'render' => 'getNoNumber',
//        ));

        $grid->addColumn('domain', array(
            'header' => $this->__('Domain'),
            'index' => 'domain',
            'align' => 'left',
            'render' => 'getDomainLink',
        ));
        $grid->addColumn('status', array(
            'header' => $this->__('Status'),
            'index' => 'status',
            'width' => '100px',
            'align' => 'left',
            'render' => 'getStatus',
        ));

        $grid->addColumn('action', array(
            'header' => $this->__('Action'),
            'index' => 'action',
            'width' => '170px',
            'align' => 'center',
            'render' => 'getActions',
        ));

        $this->setChild('sales_grid', $grid);
        return $this;
    }

    public function getNoNumber($row) {
        return sprintf('#%d', $row->getId());
    }

    public function getDomainLink($row) {
        return '<a href="http://' . $row->getDomain() . '">' . $row->getDomain() . '</a>';
    }

    public function getStatus($row) {
        $statuses = Mage::getSingleton('affiliateplusdirectlink/status')->getOptionArray();
        return $statuses[$row->getStatus()];
    }

//    public function getActions($row) {
//        if ($row->getStatus() == 1) {
//            $action = '<a href="javascript:void(0);" onclick="verifyDomain(' . $row->getId() . ')">' . $this->__('Verify') . '</a>';
//            $action .=' | <a href="javascript:void(0);" onclick="deleteDomain(' . $row->getId() . ')">' . $this->__('Delete') . '</a>';
//        } elseif ($row->getStatus() == 2) {
//            $action = '<a href="javascript:void(0);" onclick="addDirectLink(' . $row->getId() . ')">' . $this->__('Add direct link') . '</a>';
//            $action .=' | <a href="javascript:void(0);" onclick="deleteDomain(' . $row->getId() . ')">' . $this->__('Delete') . '</a>';
//        } else {
//            $action ='<a href="javascript:void(0);" onclick="deleteDomain(' . $row->getId() . ')">' . $this->__('Delete') . '</a>';
//        }
//        return $action;
//    }

    /**
     * Changed By Billy Trinh: Responsive
     * @param type $row
     * @return string
     */
    public function getActions($row) {
        if ($row->getStatus() == 1) {
            $action = '<a href="javascript:void(0);" onclick="verifyDomain(' . $row->getId() . ',this)">' . $this->__('Verify') . '</a>';
            $action .=' | <a href="javascript:void(0);" onclick="deleteDomain(' . $row->getId() . ',this)">' . $this->__('Delete') . '</a>';
        } elseif ($row->getStatus() == 2) {
            $action = '<a href="javascript:void(0);" onclick="addDirectLink(' . $row->getId() . ',this)">' . $this->__('Add direct link') . '</a>';
            $action .=' | <a href="javascript:void(0);" onclick="deleteDomain(' . $row->getId() . ',this)">' . $this->__('Delete') . '</a>';
        } else {
            $action ='<a href="javascript:void(0);" onclick="deleteDomain(' . $row->getId() . ',this)">' . $this->__('Delete') . '</a>';
        }
        return $action;
    }
    
    
    public function getPagerHtml() {
        return $this->getChildHtml('sales_pager');
    }

    public function getGridHtml() {
        return $this->getChildHtml('sales_grid');
    }

    protected function _toHtml() {
        $this->getChild('sales_grid')->setCollection($this->getCollection());
        return parent::_toHtml();
    }

}