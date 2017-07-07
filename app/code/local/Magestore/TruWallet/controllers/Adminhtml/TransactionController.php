<?php

class Magestore_TruWallet_Adminhtml_TransactionController extends Mage_Adminhtml_Controller_Action
{
	protected function _initAction(){
		$this->loadLayout()
			->_setActiveMenu('truwallet/transaction')
			->_addBreadcrumb(Mage::helper('adminhtml')->__('Items Manager'), Mage::helper('adminhtml')->__('Item Manager'));
		return $this;
	}
 
	public function indexAction(){
		$this->_initAction()
			->renderLayout();
	}

	public function editAction() {
		$id	 = $this->getRequest()->getParam('id');
		$model  = Mage::getModel('truwallet/transaction')->load($id);

		if ($model->getId() || $id == 0) {
			$data = Mage::getSingleton('adminhtml/session')->getFormData(true);
			if (!empty($data))
				$model->setData($data);

			Mage::register('transaction_data', $model);

			$this->loadLayout();
			$this->_setActiveMenu('truwallet/transaction');

			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item Manager'), Mage::helper('adminhtml')->__('Item Manager'));
			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item News'), Mage::helper('adminhtml')->__('Item News'));

			$this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
			$this->_addContent($this->getLayout()->createBlock('truwallet/adminhtml_transaction_edit'))
				->_addLeft($this->getLayout()->createBlock('truwallet/adminhtml_transaction_edit_tabs'));

			$this->renderLayout();
		} else {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('truwallet')->__('Item does not exist'));
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
	  		
			$model = Mage::getModel('truwallet/transaction');
			$model->setData($data)
				->setId($this->getRequest()->getParam('id'));
			
			try {
				if ($model->getCreatedTime == NULL || $model->getUpdateTime() == NULL)
					$model->setCreatedTime(now())
						->setUpdateTime(now());
				else
					$model->setUpdateTime(now());
				
				$model->save();
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('truwallet')->__('Item was successfully saved'));
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
		Mage::getSingleton('adminhtml/session')->addError(Mage::helper('truwallet')->__('Unable to find item to save'));
		$this->_redirect('*/*/');
	}
 
	public function deleteAction() {
		if( $this->getRequest()->getParam('id') > 0 ) {
			try {
				$model = Mage::getModel('truwallet/transaction');
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
		$truwalletIds = $this->getRequest()->getParam('truwallet');
		if(!is_array($truwalletIds)){
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select item(s)'));
		}else{
			try {
				foreach ($truwalletIds as $truwalletId) {
					$truwallet = Mage::getModel('truwallet/transaction')->load($truwalletId);
					$truwallet->delete();
				}
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Total of %d record(s) were successfully deleted', count($truwalletIds)));
			} catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
			}
		}
		$this->_redirect('*/*/index');
	}
	
	public function massStatusAction() {
		$truwalletIds = $this->getRequest()->getParam('truwallet');
		if(!is_array($truwalletIds)) {
			Mage::getSingleton('adminhtml/session')->addError($this->__('Please select item(s)'));
		} else {
			try {
				foreach ($truwalletIds as $truwalletId) {
					$truwallet = Mage::getSingleton('truwallet/transaction')
						->load($truwalletId)
						->setStatus($this->getRequest()->getParam('status'))
						->setIsMassupdate(true)
						->save();
				}
				$this->_getSession()->addSuccess(
					$this->__('Total of %d record(s) were successfully updated', count($truwalletIds))
				);
			} catch (Exception $e) {
				$this->_getSession()->addError($e->getMessage());
			}
		}
		$this->_redirect('*/*/index');
	}
  
	public function exportCsvAction(){
		$fileName   = 'truwallet.csv';
		$content	= $this->getLayout()->createBlock('truwallet/adminhtml_transaction_grid')->getCsv();
		$this->_prepareDownloadResponse($fileName,$content);
	}

	public function exportXmlAction(){
		$fileName   = 'truwallet.xml';
		$content	= $this->getLayout()->createBlock('truwallet/adminhtml_transaction_grid')->getXml();
		$this->_prepareDownloadResponse($fileName,$content);
	}

	public function importAction() {
		$this->loadLayout();
		$this->_setActiveMenu('truwallet/transaction');

		$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Import Transactions'), Mage::helper('adminhtml')->__('Import Transactions'));
		$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Import Transactions'), Mage::helper('adminhtml')->__('Import Transactions'));

		$this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
		$editBlock = $this->getLayout()->createBlock('truwallet/adminhtml_transaction_import');
		$editBlock->removeButton('delete');
		$editBlock->removeButton('saveandcontinue');
		$editBlock->removeButton('reset');
		$editBlock->updateButton('back', 'onclick', 'setLocation(\'' . $this->getUrl('*/*/') . '\')');
		$editBlock->setData('form_action_url', $this->getUrl('*/*/importSave', array()));

		$this->_addContent($editBlock)
			->_addLeft($this->getLayout()->createBlock('truwallet/adminhtml_transaction_import_tabs'));

		$this->renderLayout();
	}

	public function importSaveAction() {

		if (!empty($_FILES['csv_store']['tmp_name'])) {
			try {
				$number = Mage::helper('truwallet/transaction')->import();
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('truwallet')->__('You\'ve successfully imported ') . $number . Mage::helper('truwallet')->__(' new transaction(s)'));
			} catch (Mage_Core_Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
			} catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError(Mage::helper('truwallet')->__('Invalid file upload attempt'));
			}
			$this->_redirect('*/*/');
		} else {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('truwallet')->__('Invalid file upload attempt'));
			$this->_redirect('*/*/importstore');
		}

	}

}