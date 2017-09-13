<?php

class Magestore_Interest_Adminhtml_InterestController extends Mage_Adminhtml_Controller_Action
{
	protected function _initAction(){
		$this->loadLayout()
			->_setActiveMenu('interest/interest')
			->_addBreadcrumb(Mage::helper('adminhtml')->__('Interests Manager'), Mage::helper('adminhtml')->__('Interest Manager'));
		return $this;
	}
 
	public function indexAction(){
		$this->_initAction()
			->renderLayout();
	}

	public function editAction() {
		$id	 = $this->getRequest()->getParam('id');
		$model  = Mage::getModel('interest/interest')->load($id);

		if ($model->getId() || $id == 0) {
			$data = Mage::getSingleton('adminhtml/session')->getFormData(true);
			if (!empty($data))
				$model->setData($data);

			Mage::register('interest_data', $model);

			$this->loadLayout();
			$this->_setActiveMenu('interest/interest');

			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Interest Manager'), Mage::helper('adminhtml')->__('Interest Manager'));
			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Interest News'), Mage::helper('adminhtml')->__('Interest News'));

			$this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
			$this->_addContent($this->getLayout()->createBlock('interest/adminhtml_interest_edit'))
				->_addLeft($this->getLayout()->createBlock('interest/adminhtml_interest_edit_tabs'));

			$this->renderLayout();
		} else {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('interest')->__('Interest does not exist'));
			$this->_redirect('*/*/');
		}
	}
 
	public function newAction() {
		$this->_forward('edit');
	}
 
	public function saveAction() {
		if ($data = $this->getRequest()->getPost()) {
			
			$model = Mage::getModel('interest/interest');		
			$model->setData($data)
				->setId($this->getRequest()->getParam('id'));
			
			try {
				if ($model->getCreatedTime == NULL || $model->getUpdateTime() == NULL)
					$model->setCreatedTime(now())
						->setUpdateTime(now());
				else
					$model->setUpdateTime(now());
				
				$model->save();
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('interest')->__('Interest was successfully saved'));
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
		Mage::getSingleton('adminhtml/session')->addError(Mage::helper('interest')->__('Unable to find interest to save'));
		$this->_redirect('*/*/');
	}
 
	public function deleteAction() {
		if( $this->getRequest()->getParam('id') > 0 ) {
			try {
				$model = Mage::getModel('interest/interest');
				$model->setId($this->getRequest()->getParam('id'))
					->delete();
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Interest was successfully deleted'));
				$this->_redirect('*/*/');
			} catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
				$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
			}
		}
		$this->_redirect('*/*/');
	}

	public function massDeleteAction() {
		$interestIds = $this->getRequest()->getParam('interest');
		if(!is_array($interestIds)){
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select interest(s)'));
		}else{
			try {
				foreach ($interestIds as $interestId) {
					$interest = Mage::getModel('interest/interest')->load($interestId);
					$interest->delete();
				}
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Total of %d record(s) were successfully deleted', count($interestIds)));
			} catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
			}
		}
		$this->_redirect('*/*/index');
	}
	
	public function massStatusAction() {
		$interestIds = $this->getRequest()->getParam('interest');
		if(!is_array($interestIds)) {
			Mage::getSingleton('adminhtml/session')->addError($this->__('Please select interest(s)'));
		} else {
			try {
				foreach ($interestIds as $interestId) {
					$interest = Mage::getSingleton('interest/interest')
						->load($interestId)
						->setStatus($this->getRequest()->getParam('status'))
						->setIsMassupdate(true)
						->save();
				}
				$this->_getSession()->addSuccess(
					$this->__('Total of %d record(s) were successfully updated', count($interestIds))
				);
			} catch (Exception $e) {
				$this->_getSession()->addError($e->getMessage());
			}
		}
		$this->_redirect('*/*/index');
	}
  
	public function exportCsvAction(){
		$fileName   = 'interest.csv';
		$content	= $this->getLayout()->createBlock('interest/adminhtml_interest_grid')->getCsv();
		$this->_prepareDownloadResponse($fileName,$content);
	}

	public function exportXmlAction(){
		$fileName   = 'interest.xml';
		$content	= $this->getLayout()->createBlock('interest/adminhtml_interest_grid')->getXml();
		$this->_prepareDownloadResponse($fileName,$content);
	}

    public function importAction() {
        $this->loadLayout();
        $this->_setActiveMenu('interest/interest');

        $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Import Interest'), Mage::helper('adminhtml')->__('Import Interest'));
        $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Import Interest'), Mage::helper('adminhtml')->__('Import Interest'));

        $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
        $editBlock = $this->getLayout()->createBlock('interest/adminhtml_interest_import');
        $editBlock->removeButton('delete');
        $editBlock->removeButton('saveandcontinue');
        $editBlock->removeButton('reset');
        $editBlock->updateButton('back', 'onclick', 'setLocation(\'' . $this->getUrl('*/*/') . '\')');
        $editBlock->setData('form_action_url', $this->getUrl('*/*/importSave', array()));

        $this->_addContent($editBlock)
            ->_addLeft($this->getLayout()->createBlock('interest/adminhtml_interest_import_tabs'));

        $this->renderLayout();
    }

    public function importSaveAction() {

        if (!empty($_FILES['csv_store']['tmp_name'])) {
            try {
                $number = Mage::helper('interest')->import();
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('interest')->__('You\'ve successfully imported ') . $number . Mage::helper('interest')->__(' new interest(s)'));
            } catch (Mage_Core_Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('interest')->__('Invalid file upload attempt'));
            }
            $this->_redirect('*/*/');
        } else {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('interest')->__('Invalid file upload attempt'));
            $this->_redirect('*/*/importstore');
        }

    }
}
