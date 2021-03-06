<?php

class Magestore_TruGiftCard_Adminhtml_TransactionController extends Mage_Adminhtml_Controller_Action
{
	protected function _initAction(){
		$this->loadLayout()
			->_setActiveMenu('trugiftcard/transaction')
			->_addBreadcrumb(Mage::helper('adminhtml')->__('Items Manager'), Mage::helper('adminhtml')->__('Item Manager'));
		return $this;
	}
 
	public function indexAction(){
		$this->_initAction()
			->renderLayout();
	}

	public function editAction() {
		$id	 = $this->getRequest()->getParam('id');
		$model  = Mage::getModel('trugiftcard/transaction')->load($id);

		if ($model->getId() || $id == 0) {
			$data = Mage::getSingleton('adminhtml/session')->getFormData(true);
			if (!empty($data))
				$model->setData($data);

			Mage::register('transaction_data', $model);

			$this->loadLayout();
			$this->_setActiveMenu('trugiftcard/transaction');

			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item Manager'), Mage::helper('adminhtml')->__('Item Manager'));
			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item News'), Mage::helper('adminhtml')->__('Item News'));

			$this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
			$this->_addContent($this->getLayout()->createBlock('trugiftcard/adminhtml_transaction_edit'))
				->_addLeft($this->getLayout()->createBlock('trugiftcard/adminhtml_transaction_edit_tabs'));

			$this->renderLayout();
		} else {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('trugiftcard')->__('Item does not exist'));
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
	  		
			$model = Mage::getModel('trugiftcard/transaction');
			$model->setData($data)
				->setId($this->getRequest()->getParam('id'));
			
			try {
				if ($model->getCreatedTime == NULL || $model->getUpdateTime() == NULL)
					$model->setCreatedTime(now())
						->setUpdateTime(now());
				else
					$model->setUpdateTime(now());
				
				$model->save();
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('trugiftcard')->__('Item was successfully saved'));
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
		Mage::getSingleton('adminhtml/session')->addError(Mage::helper('trugiftcard')->__('Unable to find item to save'));
		$this->_redirect('*/*/');
	}
 
	public function deleteAction() {
		if( $this->getRequest()->getParam('id') > 0 ) {
			try {
				$model = Mage::getModel('trugiftcard/transaction');
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
		$trugiftcardIds = $this->getRequest()->getParam('trugiftcard');
		if(!is_array($trugiftcardIds)){
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select item(s)'));
		}else{
			try {
				foreach ($trugiftcardIds as $trugiftcardId) {
					$trugiftcard = Mage::getModel('trugiftcard/transaction')->load($trugiftcardId);
					$trugiftcard->delete();
				}
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Total of %d record(s) were successfully deleted', count($trugiftcardIds)));
			} catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
			}
		}
		$this->_redirect('*/*/index');
	}
	
	public function massStatusAction() {
		$trugiftcardIds = $this->getRequest()->getParam('trugiftcard');
		if(!is_array($trugiftcardIds)) {
			Mage::getSingleton('adminhtml/session')->addError($this->__('Please select item(s)'));
		} else {
			try {
				foreach ($trugiftcardIds as $trugiftcardId) {
					$trugiftcard = Mage::getSingleton('trugiftcard/transaction')
						->load($trugiftcardId)
						->setStatus($this->getRequest()->getParam('status'))
						->setIsMassupdate(true)
						->save();
				}
				$this->_getSession()->addSuccess(
					$this->__('Total of %d record(s) were successfully updated', count($trugiftcardIds))
				);
			} catch (Exception $e) {
				$this->_getSession()->addError($e->getMessage());
			}
		}
		$this->_redirect('*/*/index');
	}
  
	public function exportCsvAction(){
		$fileName   = 'trugiftcard.csv';
		$content	= $this->getLayout()->createBlock('trugiftcard/adminhtml_transaction_grid')->getCsv();
		$this->_prepareDownloadResponse($fileName,$content);
	}

	public function exportXmlAction(){
		$fileName   = 'trugiftcard.xml';
		$content	= $this->getLayout()->createBlock('trugiftcard/adminhtml_transaction_grid')->getXml();
		$this->_prepareDownloadResponse($fileName,$content);
	}

	public function importAction() {
		$this->loadLayout();
		$this->_setActiveMenu('trugiftcard/transaction');

		$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Import Transactions'), Mage::helper('adminhtml')->__('Import Transactions'));
		$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Import Transactions'), Mage::helper('adminhtml')->__('Import Transactions'));

		$this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
		$editBlock = $this->getLayout()->createBlock('trugiftcard/adminhtml_transaction_import');
		$editBlock->removeButton('delete');
		$editBlock->removeButton('saveandcontinue');
		$editBlock->removeButton('reset');
		$editBlock->updateButton('back', 'onclick', 'setLocation(\'' . $this->getUrl('*/*/') . '\')');
		$editBlock->setData('form_action_url', $this->getUrl('*/*/importSave', array()));

		$this->_addContent($editBlock)
			->_addLeft($this->getLayout()->createBlock('trugiftcard/adminhtml_transaction_import_tabs'));

		$this->renderLayout();
	}

	public function importSaveAction() {

		if (!empty($_FILES['csv_store']['tmp_name'])) {
			try {
				$number = Mage::helper('trugiftcard/transaction')->import();
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('trugiftcard')->__('You\'ve successfully imported ') . $number . Mage::helper('trugiftcard')->__(' new transaction(s)'));
			} catch (Mage_Core_Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
			} catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError(Mage::helper('trugiftcard')->__('Invalid file upload attempt'));
			}
			$this->_redirect('*/*/');
		} else {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('trugiftcard')->__('Invalid file upload attempt'));
			$this->_redirect('*/*/importstore');
		}

	}

}