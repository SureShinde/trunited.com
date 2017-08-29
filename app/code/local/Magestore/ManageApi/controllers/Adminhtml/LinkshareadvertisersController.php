<?php

class Magestore_ManageApi_Adminhtml_LinkshareadvertisersController extends Mage_Adminhtml_Controller_Action
{
	protected function _initAction(){
		$this->loadLayout()
			->_setActiveMenu('manageapi/linkshareadvertisers')
			->_addBreadcrumb(Mage::helper('adminhtml')->__('LinkShare Advertisers'), Mage::helper('adminhtml')->__('LinkShare Advertisers'));
		return $this;
	}
 
	public function indexAction(){
		$this->_initAction()
			->renderLayout();
	}

	public function massDeleteAction() {
		$manageapiIds = $this->getRequest()->getParam('manageapi');

		if(!is_array($manageapiIds)){
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select item(s)'));
		}else{
			try {
				foreach ($manageapiIds as $manageapiId) {
					$manageapi = Mage::getModel('manageapi/linkshareadvertisers')->load($manageapiId);
					$manageapi->delete();
				}
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Total of %d record(s) were successfully deleted', count($manageapiIds)));
			} catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
			}
		}
		$this->_redirect('*/*/index');
	}

	public function exportCsvAction(){
		$fileName   = 'linkshare_advertisers.csv';
		$content	= $this->getLayout()->createBlock('manageapi/adminhtml_linkshareadvertisers_grid')->getCsv();
		$this->_prepareDownloadResponse($fileName,$content);
	}

	public function exportXmlAction(){
		$fileName   = 'linkshare_advertisers.xml';
		$content	= $this->getLayout()->createBlock('manageapi/adminhtml_linkshareadvertisers_grid')->getXml();
		$this->_prepareDownloadResponse($fileName,$content);
	}
}