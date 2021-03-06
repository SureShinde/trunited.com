<?php

class Magestore_TruBox_Adminhtml_ReportController extends Mage_Adminhtml_Controller_Action
{
	protected function _initAction(){
		$this->loadLayout()
			->_setActiveMenu('trubox/item')
			->_addBreadcrumb(Mage::helper('adminhtml')->__('Report Manager'), Mage::helper('adminhtml')->__('Report Manager'));
		return $this;
	}
 
	public function indexAction(){
		$this->_initAction()
			->renderLayout();
	}

	public function editAction() {
		$id	 = $this->getRequest()->getParam('id');
		$model  = Mage::getModel('trubox/item')->load($id);

		if ($model->getId() || $id == 0) {
			$data = Mage::getSingleton('adminhtml/session')->getFormData(true);
			if (!empty($data))
				$model->setData($data);

			Mage::register('trubox_data', $model);

			$this->loadLayout();
			$this->_setActiveMenu('trubox/item');

			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item Manager'), Mage::helper('adminhtml')->__('Item Manager'));
			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item News'), Mage::helper('adminhtml')->__('Item News'));

			$this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
			$this->_addContent($this->getLayout()->createBlock('trubox/adminhtml_items_edit'))
				->_addLeft($this->getLayout()->createBlock('trubox/adminhtml_items_edit_tabs'));

			$this->renderLayout();
		} else {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('trubox')->__('Item does not exist'));
			$this->_redirect('*/*/');
		}
	}
 
	public function newAction() {
		$this->_forward('edit');
	}
 
	public function saveAction() {
		if ($data = $this->getRequest()->getPost()) {
			if(isset($_FILES['filename']['name']) && $_FILES['filename']['name'] != '') {
				try {
					/* Starting upload */	
					$uploader = new Varien_File_Uploader('filename');
					
					// Any extention would work
			   		$uploader->setAllowedExtensions(array('jpg','jpeg','gif','png'));
					$uploader->setAllowRenameFiles(false);
					
					// Set the file upload mode 
					// false -> get the file directly in the specified folder
					// true -> get the file in the product like folders 
					//	(file.jpg will go in something like /media/f/i/file.jpg)
					$uploader->setFilesDispersion(false);
							
					// We set media as the upload dir
					$path = Mage::getBaseDir('media') . DS ;
					$uploader->save($path, $_FILES['filename']['name'] );
					
				} catch (Exception $e) {}
				
				//this way the name is saved in DB
	  			$data['filename'] = $_FILES['filename']['name'];
			}
	  		
			$model = Mage::getModel('trubox/item');		
			$model->setData($data)
				->setId($this->getRequest()->getParam('id'));
			
			try {
				if ($model->getCreatedTime == NULL || $model->getUpdateTime() == NULL)
					$model->setCreatedTime(now())
						->setUpdateTime(now());
				else
					$model->setUpdateTime(now());
				
				$model->save();
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('trubox')->__('Item was successfully saved'));
				Mage::getSingleton('adminhtml/session')->setFormData(false);

				if ($this->getRequest()->getParam('back')) {
					$this->_redirect('*/*/edit', array('id' => $model->getId()));
					return;
				}
				$this->_redirect('*/*/');
				return;
			} catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
				Mage::getSingleton('adminhtml/session')->setFormData($data);
				$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
				return;
			}
		}
		Mage::getSingleton('adminhtml/session')->addError(Mage::helper('trubox')->__('Unable to find item to save'));
		$this->_redirect('*/*/');
	}
 
	public function deleteAction() {
		if( $this->getRequest()->getParam('id') > 0 ) {
			try {
				$model = Mage::getModel('trubox/item');
				$model->setId($this->getRequest()->getParam('id'))
					->delete();
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Item was successfully deleted'));
				$this->_redirect('*/*/');
			} catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
				$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
			}
		}
		$this->_redirect('*/*/');
	}

	public function massDeleteAction() {
		$truboxIds = $this->getRequest()->getParam('trubox');
		if(!is_array($truboxIds)){
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select item(s)'));
		}else{
			try {
				foreach ($truboxIds as $truboxId) {
					$trubox = Mage::getModel('trubox/item')->load($truboxId);
					$trubox->delete();
				}
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Total of %d record(s) were successfully deleted', count($truboxIds)));
			} catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
			}
		}
		$this->_redirect('*/*/index');
	}
	
	public function massStatusAction() {
		$truboxIds = $this->getRequest()->getParam('trubox');
		if(!is_array($truboxIds)) {
			Mage::getSingleton('adminhtml/session')->addError($this->__('Please select item(s)'));
		} else {
			try {
				foreach ($truboxIds as $truboxId) {
					$trubox = Mage::getSingleton('trubox/item')
						->load($truboxId)
						->setStatus($this->getRequest()->getParam('status'))
						->setIsMassupdate(true)
						->save();
				}
				$this->_getSession()->addSuccess(
					$this->__('Total of %d record(s) were successfully updated', count($truboxIds))
				);
			} catch (Exception $e) {
				$this->_getSession()->addError($e->getMessage());
			}
		}
		$this->_redirect('*/*/index');
	}

	public function massOrderAction() {
		$truboxIds = $this->getRequest()->getParam('trubox');

		if(!is_array($truboxIds)){
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select item(s)'));
		}else{
			try {
				$data = array();
				foreach ($truboxIds as $truboxId) {
					$item = Mage::getModel('trubox/item')->load($truboxId);
					if(!array_key_exists($item->getTruboxId(), $data)){
						$data[$item->getTruboxId()] = array(
							$item->getId()
						);
					} else {
						$data[$item->getTruboxId()][] = $item->getId();
					}
				}

				$rs = 0;
				if(sizeof($data) > 0)
				{
					$rs = Mage::helper('trubox/order')->prepareOrder($data);
				}

				if($rs == 0)
					Mage::getSingleton('adminhtml/session')->addError(
						Mage::helper('trubox')->__('Something was wrong with this action !')
					);
				else
					Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Total of %d order(s) were successfully created', $rs));


			} catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
			}
		}
		$this->_redirect('*/*/index');
	}
  
	public function exportCsvAction(){
		$fileName   = 'trubox_items.csv';
		$content	= $this->getLayout()->createBlock('trubox/adminhtml_items_grid')->getCsv();
		$this->_prepareDownloadResponse($fileName,$content);
	}

	public function exportXmlAction(){
		$fileName   = 'trubox_items.xml';
		$content	= $this->getLayout()->createBlock('trubox/adminhtml_items_grid')->getXml();
		$this->_prepareDownloadResponse($fileName,$content);
	}
}