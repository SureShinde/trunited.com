<?php

class Magestore_ManageApi_Adminhtml_CjactionsController extends Mage_Adminhtml_Controller_Action
{
	protected function _initAction(){
		$this->loadLayout()
			->_setActiveMenu('manageapi/cjactions')
			->_addBreadcrumb(Mage::helper('adminhtml')->__('API Manager'), Mage::helper('adminhtml')->__('Cj API Manager'));
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
					$manageapi = Mage::getModel('manageapi/cjactions')->load($manageapiId);
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
		$fileName   = 'cjactions.csv';
		$content	= $this->getLayout()->createBlock('manageapi/adminhtml_cjactions_grid')->getCsv();
		$this->_prepareDownloadResponse($fileName,$content);
	}

	public function exportXmlAction(){
		$fileName   = 'cjactions.xml';
		$content	= $this->getLayout()->createBlock('manageapi/adminhtml_cjactions_grid')->getXml();
		$this->_prepareDownloadResponse($fileName,$content);
	}
}