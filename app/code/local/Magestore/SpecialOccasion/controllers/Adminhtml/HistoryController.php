<?php

class Magestore_SpecialOccasion_Adminhtml_HistoryController extends Mage_Adminhtml_Controller_Action
{
	protected function _initAction(){
		$this->loadLayout()
			->_setActiveMenu('specialoccasion/history')
			->_addBreadcrumb(Mage::helper('adminhtml')->__('History Manager'), Mage::helper('adminhtml')->__('History Manager'));
		return $this;
	}
 
	public function indexAction(){

		$this->_initAction()
			->renderLayout();
	}

	public function exportCsvAction(){
		$fileName   = 'specialoccasion_history.csv';
		$content	= $this->getLayout()->createBlock('specialoccasion/adminhtml_history_grid')->getCsv();
		$this->_prepareDownloadResponse($fileName,$content);
	}

	public function exportXmlAction(){
		$fileName   = 'specialoccasion_history.xml';
		$content	= $this->getLayout()->createBlock('specialoccasion/adminhtml_history_grid')->getXml();
		$this->_prepareDownloadResponse($fileName,$content);
	}
}
