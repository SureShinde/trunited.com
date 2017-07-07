<?php

class Magestore_Custompromotions_Adminhtml_SmsController extends Mage_Adminhtml_Controller_Action
{
	protected function _initAction(){
		$this->loadLayout()
			->_setActiveMenu('custompromotions/custompromotions')
			->_addBreadcrumb(Mage::helper('adminhtml')->__('SMS Manager'), Mage::helper('adminhtml')->__('SMS Manager'));
		return $this;
	}
 
	public function indexAction(){
		$this->_initAction()
			->renderLayout();
	}

    public function importAction() {
        $this->loadLayout();
        $this->_setActiveMenu('custompromotions/custompromotions');

        $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Import SMS'), Mage::helper('adminhtml')->__('Import SMS'));
        $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Import SMS'), Mage::helper('adminhtml')->__('Import SMS'));

        $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
        $editBlock = $this->getLayout()->createBlock('custompromotions/adminhtml_sms_import');
        $editBlock->removeButton('delete');
        $editBlock->removeButton('saveandcontinue');
        $editBlock->removeButton('reset');
        $editBlock->updateButton('back', 'onclick', 'setLocation(\'' . $this->getUrl('*/*/') . '\')');
        $editBlock->setData('form_action_url', $this->getUrl('*/*/importSave', array()));

        $this->_addContent($editBlock)
            ->_addLeft($this->getLayout()->createBlock('custompromotions/adminhtml_sms_import_tabs'));

        $this->renderLayout();
    }

    public function importSaveAction() {

        if (!empty($_FILES['csv_store']['tmp_name'])) {
            try {
                $number = Mage::helper('custompromotions/sms')->import();
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('custompromotions')->__('You\'ve successfully imported ') . $number . Mage::helper('custompromotions')->__(' new sms'));
            } catch (Mage_Core_Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('custompromotions')->__('Invalid file upload attempt'));
            }
            $this->_redirect('*/*/import');
        } else {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('custompromotions')->__('Invalid file upload attempt'));
            $this->_redirect('*/*/import');
        }
    }
}