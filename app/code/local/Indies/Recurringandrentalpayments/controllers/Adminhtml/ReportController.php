<?php
/**
*
* Do not edit or add to this file if you wish to upgrade the module to newer
* versions in the future. If you wish to customize the module for your
* needs please contact us to https://www.milople.com/magento-extensions/contacts/
*
* @category     Ecommerce
* @package      Indies_Recurringandrentalpayments
* @copyright    Copyright (c) 2015 Milople Technologies Pvt. Ltd. All Rights Reserved.
* @url          https://www.milople.com/magento-extensions/recurring-and-subscription-payments.html
*
* Milople was known as Indies Services earlier.
*
**/

class Indies_Recurringandrentalpayments_Adminhtml_ReportController extends Mage_Adminhtml_Controller_Action
{

	protected function _initAction() {
		$this->loadLayout()
			 ->_setActiveMenu('recurringandrentalpayments/subscribed')
			 ->_addBreadcrumb(Mage::helper('adminhtml')->__('Recurring &amp; Rental Subscribed Products'), Mage::helper('adminhtml')->__('Recurring &amp; Rental Subscribed Products'));
		return $this;
	}   
 
	public function indexAction() {
		$this->_initAction();
		$this->renderLayout();
	}
	public function exportCsvAction() {
        $fileName   = 'Subscriptionorders.csv';
        $content    = $this->getLayout()->createBlock('recurringandrentalpayments/adminhtml_report_grid')
            ->getCsvFile();
		$this->_prepareDownloadResponse($fileName, $content);
    }
	
	public function exportXmlAction() {
        $fileName   = 'Subscriptionorders.xml';
        $content    = $this->getLayout()->createBlock('recurringandrentalpayments/adminhtml_report_grid')
            ->getXml();
        $this->_sendUploadResponse($fileName, $content);
    }
	protected function _sendUploadResponse($fileName, $content, $contentType='application/octet-stream') {
        $response = $this->getResponse();
        $response->setHeader('HTTP/1.1 200 OK','');
        $response->setHeader('Pragma', 'public', true);
        $response->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true);
        $response->setHeader('Content-Disposition', 'attachment; filename='.$fileName);
        $response->setHeader('Last-Modified', date('r'));
        $response->setHeader('Accept-Ranges', 'bytes');
        $response->setHeader('Content-Length', strlen($content));
        $response->setHeader('Content-type', $contentType);
        $response->setBody($content);
        $response->sendResponse();
        die;
    }
	protected function _isAllowed()
    {
      return true; 
    }
}