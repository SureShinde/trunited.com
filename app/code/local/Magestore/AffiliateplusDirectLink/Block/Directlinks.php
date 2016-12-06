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
 * Directlinks Block
 * 
 * @category    Magestore
 * @package     Magestore_AffiliateplusDirectLink
 * @author      Magestore Developer
 */
class Magestore_AffiliateplusDirectLink_Block_Directlinks extends Mage_Core_Block_Template {

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
        $collection = Mage::getModel('affiliateplusdirectlink/directlink')->getCollection();
        $collection->getSelect()
                ->join(array('adld' => Mage::getModel('core/resource')->getTableName('affiliateplus_direct_link_domain')), 'main_table.domain_id =adld.domain_id AND adld.account_id=' . $account->getId(), array('status_directlink' => 'main_table.status'));
        $this->setCollection($collection);
    }

    public function _prepareLayout() {
        parent::_prepareLayout();
        $pager = $this->getLayout()->createBlock('page/html_pager', 'sales_pager')->setCollection($this->getCollection());
        $this->setChild('sales_pager', $pager);

        $grid = $this->getLayout()->createBlock('affiliateplus/grid', 'sales_grid');

        $grid->addColumn('link', array(
            'header' => $this->__('Direct link'),
            'index' => 'link',
            'align' => 'center',
            'width' => '140px',
            'render' => 'getLink',
        ));

        $grid->addColumn('source_page', array(
            'header' => $this->__('Page to show banner'),
            'index' => 'source_page',
            'align' => 'center',
            'width' => '140px',
            'render' => 'getSourcePage',
        ));

        $grid->addColumn('banner_id', array(
            'header' => $this->__('Banner'),
            'index' => 'banner_id',
            'align' => 'center',
            'width' => '60px',
            'render' => 'getBanner',
        ));
        $text = $this->__('Statictics for clicks generated via direct link. Sometimes it shows O unique and a number of raws because clicks on the banner have been counted for normal link already.');
        $html = '<div id="discription-click-directlink" href="javascript:void(0)">(?)</div>
                    <div id="tooltip-click-directlink"  style="width:150px;height:150px; border:3px solid #D9D9D9;visibility: visible;background:#fff; display: none; ">' . $text . '</div>
                        <script type="text/javascript"> var tip = new Tooltip("discription-click-directlink", "tooltip-click-directlink");
                        </script><br/>';
        $grid->addColumn('clicks', array(
            'header' => $this->__('Clicks %s (unique/ raw)', '</br>'),
            'index' => 'clicks',
            'align' => 'center',
            'width' => '50px',
            'render' => 'getClicks',
        ));

        $grid->addColumn('status', array(
            'header' => $this->__('Status'),
            'index' => 'action',
            'align' => 'center',
            'index' => 'status',
            'width' => '30px',
            'render' => 'getStatus',
        ));

        $grid->addColumn('action', array(
            'header' => $this->__('Action'),
            'index' => 'action',
            'align' => 'center',
            'width' => '150px',
            'render' => 'getActions',
        ));

        $this->setChild('sales_grid', $grid);
        return $this;
    }

    public function getNoNumber($row) {
        return sprintf('#%d', $row->getId());
    }

    public function getLink($row) {
        if ($row->getLink()) {
            $link = $row->getLink();
        } else {
            $link = Mage::getModel('affiliateplus/banner')->load($row->getBannerId())->getLink();
        }
        $linkrender = Mage::helper('affiliateplusdirectlink')->renderDirectLink($link);
        return '<pre><a title="' . $link . '" href="' . $link . '">' . $linkrender . '</a></pre>';
    }

    public function getSourcePage($row) {
        $domain = Mage::getModel('affiliateplusdirectlink/domain')->load($row->getDomainId())->getDomain();
        $source = $row->getSourcePage();
        $sourcepagerender = Mage::helper('affiliateplusdirectlink')->renderDirectLink($domain . '/' . $source);
        return '<pre style=""><a title="http://' . $domain . '/' . $source . '" href="http://' . $domain . '/' . $source . '">' . $sourcepagerender . '</a></pre>';
    }

    public function getBanner($row) {
        $title = Mage::getModel('affiliateplus/banner')->load($row->getBannerId())->getTitle();
        if (!$title) {
            $title = 'N/A';
        } else {
            // $title= '<a id="discription-click-directlink-'.$row->getId().'" href="javascript:void(0)" >'.$title.'</a>'.$this->getTooltipBanner($row);
        }
        return $title;
    }

    public function getClicks($row) {
        $raw = Mage::getModel('affiliateplus/action')->getCollection()
                ->addFieldToFilter('type', 2)
                ->addFieldToFilter('banner_id', $row->getBannerId())
                ->addFieldToFilter('direct_link', $row->getId());
        $raw->getSelect()
                ->group('direct_link')
                ->columns(array('totals_raw' => 'SUM(totals)'));
        $totals_raw = $raw->getFirstItem()->getTotalsRaw();

        if (!$totals_raw) {
            $totals_raw = 0;
            $totals_unique = 0;
        } else {
            $unique_plus = Mage::getModel('affiliateplus/action')->getCollection()
                    ->addFieldToFilter('type', 2)
                    ->addFieldToFilter('banner_id', $row->getBannerId())
                    ->addFieldToFilter('direct_link', $row->getId());
            $unique_plus->getSelect()
                    ->group('ip_address')
                    ->columns(array('totals_unique' => 'IF (SUM(is_unique) = 0, 1, SUM(is_unique))'));
            $totals_unique = 0;
            foreach ($unique_plus as $value) {
                $totals_unique = $totals_unique + $value->getTotalsUnique();
            }
        }
        return $totals_unique . '/' . $totals_raw;
    }

    public function getStatus($row) {
        if ($row->getStatusDirectlink() == 1) {
            $status = 'Pending';
        } elseif ($row->getStatusDirectlink() == 2) {
            $status = 'Active';
        } else {
            $status = 'Disable';
        }
        return $status;
    }

//    public function getActions($row) {
//        if ($row->getStatus() == 1) {
//            $action = '<a href="javascript:void(0);" onclick="verifyDomain(' . $row->getDomainId() . ')">' . $this->__('Verify') . '</a>';
//            $action .=' | <a href="javascript:void(0);" onclick="editDirectLink(' . $row->getId() . ')">' . $this->__('Edit') . '</a>';
//            $action .=' | <a href="javascript:void(0);" onclick="deleteDirectLink(' . $row->getId() . ')">' . $this->__('Delete') . '</a>';
//        } elseif ($row->getStatus() == 2 && $this->getBanner($row) != 'N/A') {
//            $action = '<a href="javascript:void(0);" onclick="getCode(' . $row->getId() . ')">' . $this->__('Get Code') . '</a>';
//            $action .=' | <a href="javascript:void(0);" onclick="editDirectLink(' . $row->getId() . ')">' . $this->__('Edit') . '</a>';
//            $action .=' | <a href="javascript:void(0);" onclick="deleteDirectLink(' . $row->getId() . ')">' . $this->__('Delete') . '</a>';
//        } else {
//            $action = '<a href="javascript:void(0);" onclick="editDirectLink(' . $row->getId() . ')">' . $this->__('Edit') . '</a>';
//            $action .= ' | <a href="javascript:void(0);" onclick="deleteDirectLink(' . $row->getId() . ')">' . $this->__('Delete') . '</a>';
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
            $action = '<a href="javascript:void(0);" onclick="verifyDomain(' . $row->getDomainId() . ',this)">' . $this->__('Verify') . '</a>';
            $action .=' | <a href="javascript:void(0);" onclick="editDirectLink(' . $row->getId() . ',this)">' . $this->__('Edit') . '</a>';
            $action .=' | <a href="javascript:void(0);" onclick="deleteDirectLink(' . $row->getId() . ',this)">' . $this->__('Delete') . '</a>';
        } elseif ($row->getStatus() == 2 && $this->getBanner($row) != 'N/A') {
            $action = '<a href="javascript:void(0);" onclick="getCode(' . $row->getId() . ',this)">' . $this->__('Get Code') . '</a>';
            $action .=' | <a href="javascript:void(0);" onclick="editDirectLink(' . $row->getId() . ',this)">' . $this->__('Edit') . '</a>';
            $action .=' | <a href="javascript:void(0);" onclick="deleteDirectLink(' . $row->getId() . ',this)">' . $this->__('Delete') . '</a>';
        } else {
            $action = '<a href="javascript:void(0);" onclick="editDirectLink(' . $row->getId() . ',this)">' . $this->__('Edit') . '</a>';
            $action .= ' | <a href="javascript:void(0);" onclick="deleteDirectLink(' . $row->getId() . ',this)">' . $this->__('Delete') . '</a>';
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

    public function getTooltipBanner($row) {
        $banner = Mage::getModel('affiliateplus/banner')->load($row->getBannerId());
        $imageurl = Mage::getBaseUrl('media') . 'affiliateplus/banner/' . $banner->getSourceFile();
        $html = '<div id="tooltip-click-directlink-' . $row->getId() . '" style="display:none;">
                    <div id="helper_information" class="giftwrap_tooltip giftwrap_protoClassic" style="border:1px solid #abc;visibility: visible;background:#fff">
                        <div class="giftwrap_toolbar" style="width: 100%;">
                            <div class="title" style="font-weight:bold;background:#abc">
                                ' . $banner->getTitle() . '
                            </div>
                        </div>
                        <div class="content">
                        <img style="width:200;height:100px;" src="' . $imageurl . '"/>
                        </div>
                    </div>
                </div><script type="text/javascript"> var tip = new Tooltip("discription-click-directlink-' . $row->getId() . '", "tooltip-click-directlink-' . $row->getId() . '");
                        </script>';
        return $html;
    }

}