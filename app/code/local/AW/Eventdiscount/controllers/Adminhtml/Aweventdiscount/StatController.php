<?php
/**
 * aheadWorks Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://ecommerce.aheadworks.com/AW-LICENSE.txt
 *
 * =================================================================
 *                 MAGENTO EDITION USAGE NOTICE
 * =================================================================
 * This software is designed to work with Magento community edition and
 * its use on an edition other than specified is prohibited. aheadWorks does not
 * provide extension support in case of incorrect edition use.
 * =================================================================
 *
 * @category   AW
 * @package    AW_Eventdiscount
 * @version    1.0.5
 * @copyright  Copyright (c) 2010-2012 aheadWorks Co. (http://www.aheadworks.com)
 * @license    http://ecommerce.aheadworks.com/AW-LICENSE.txt
 */

class AW_Eventdiscount_Adminhtml_Aweventdiscount_StatController extends Mage_Adminhtml_Controller_Action
{
    protected $_timerId = null;

    protected function _initAction()
    {
        $this->loadLayout()
           ->_setActiveMenu('promo/eventdiscount')
               ->_addBreadcrumb($this->__('Event Based Discounts'), $this->__('Event Based Discounts'));
        $this->_setTitle('Statistics');
        return $this;
    }

    public function indexAction()
    {
        $this->_initAction() ->renderLayout();
    }

    public function newAction()
    {
        $this->_forward('edit');
    }

    /**
     * Returns true when admin session contain error messages
     */
    protected function _hasErrors()
    {
        return (bool)count($this->_getSession()->getMessages()->getItemsByType('error'));
    }

    /**
     * Set title of page
     */
    protected function _setTitle($action)
    {
        if (method_exists($this, '_title')) {
            $this->_title($this->__('Event Based Discounts'))->_title($this->__($action));
        }
        return $this;
    }

    /**
     * Export customer grid to CSV format
     */
    public function exportCsvAction()
    {
        $fileName   = 'eventdiscounts.csv';
        $content    = $this->getLayout()
            ->createBlock('eventdiscount/adminhtml_stat_grid')
            ->getCsvFile()
        ;
        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * Export customer grid to XML format
     */
    public function exportXmlAction()
    {
        $fileName   = 'eventdiscounts.xml';
        $content    = $this->getLayout()->createBlock('eventdiscount/adminhtml_stat_grid')
            ->getExcelFile();

        $this->_prepareDownloadResponse($fileName, $content);
    }
}