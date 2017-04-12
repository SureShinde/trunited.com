<?php

class Magestore_TruBox_Adminhtml_OrderController extends Mage_Adminhtml_Controller_Action
{
	protected function _initAction(){
		$this->loadLayout()
			->_setActiveMenu('trubox/item')
			->_addBreadcrumb(Mage::helper('adminhtml')->__('Order Manager'), Mage::helper('adminhtml')->__('Order Manager'));
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

			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Order Manager'), Mage::helper('adminhtml')->__('Order Manager'));
			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Order News'), Mage::helper('adminhtml')->__('Order News'));

			$this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
			$this->_addContent($this->getLayout()->createBlock('trubox/adminhtml_order_edit'))
				->_addLeft($this->getLayout()->createBlock('trubox/adminhtml_order_edit_tabs'));

			$this->renderLayout();
		} else {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('trubox')->__('Order does not exist'));
			$this->_redirect('*/*/');
		}
	}

	public function newAction() {
		$this->_forward('edit');
	}

	public function saveAction() {
		if ($params = $this->getRequest()->getPost()) {
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
			try {
				$customer_params = explode(',',$params['customers']);
				if(sizeof($customer_params) > 0)
				{
					$truBox_table = Mage::getSingleton('core/resource')->getTableName('trubox/item');
					foreach ($customer_params as $identify_customer) {
						if (!filter_var($identify_customer, FILTER_VALIDATE_INT) === false) {
							$customer = Mage::getModel('customer/customer')->load($identify_customer);
							if($customer->getId())
							{
								$truBox = Mage::getModel('trubox/trubox')->getCollection()
									->addFieldToFilter('customer_id', $customer->getId())
									->getFirstItem()
								;

								if(!$truBox->getId())
									continue;

								$collection = Mage::getModel('trubox/item')->getCollection()
									->addFieldToFilter('trubox_id', $truBox->getId())
								;
								
								$data = array();
								foreach ($collection as $item) {
									if(!array_key_exists($item->getTruboxId(), $data)){
										$data[$item->getTruboxId()] = array(
											$item->getId()
										);
									} else {
										$data[$item->getTruboxId()][] = $item->getId();
									}
								}

								zend_debug::dump($data);
								exit;
								$rs = 0;
								if(sizeof($data) > 0)
								{
									$rs = Mage::helper('trubox/order')->prepareOrder($data);
								}
							}
						} else if(!filter_var($identify_customer, FILTER_VALIDATE_EMAIL) === false) {

						} else {
							Mage::getSingleton('adminhtml/session')->addError(
								Mage::helper('trubox')->__('Error data: %s', $identify_customer)
							);
						}
					}
					Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('trubox')->__('Order(s) was successfully saved'));
					Mage::getSingleton('adminhtml/session')->setFormData(false);
				} else {
					Mage::getSingleton('adminhtml/session')->addError(Mage::helper('trubox')->__('Empty customers'));
					Mage::getSingleton('adminhtml/session')->setFormData(false);
				}


				$this->_redirect('*/*/new');
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

	public function generateOrdersAction()
	{
		$truBox_table = Mage::getSingleton('core/resource')->getTableName('trubox/trubox');

		$collection = Mage::getModel('trubox/item')->getCollection();
		$collection->getSelect()
			->joinLeft(
				array("tb" => $truBox_table),
				"main_table.trubox_id = tb.trubox_id",
				array("customer_id" => "tb.customer_id")
			);

		$data = array();
		foreach ($collection as $item) {
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

		$this->_redirect('*/*/');
	}
}