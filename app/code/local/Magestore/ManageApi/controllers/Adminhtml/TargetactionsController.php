<?php

class Magestore_ManageApi_Adminhtml_TargetactionsController extends Mage_Adminhtml_Controller_Action
{
	protected function _initAction(){
		$this->loadLayout()
			->_setActiveMenu('manageapi/targetactions')
			->_addBreadcrumb(Mage::helper('adminhtml')->__('API Manager'), Mage::helper('adminhtml')->__('Target API Manager'));
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
					$manageapi = Mage::getModel('manageapi/targetactions')->load($manageapiId);
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
		$fileName   = 'targetactions.csv';
		$content	= $this->getLayout()->createBlock('manageapi/adminhtml_targetactions_grid')->getCsv();
		$this->_prepareDownloadResponse($fileName,$content);
	}

	public function exportXmlAction(){
		$fileName   = 'targetactions.xml';
		$content	= $this->getLayout()->createBlock('manageapi/adminhtml_targetactions_grid')->getXml();
		$this->_prepareDownloadResponse($fileName,$content);
	}
}