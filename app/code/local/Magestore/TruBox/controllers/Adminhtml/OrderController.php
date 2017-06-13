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
			try {
				$customer_params = explode(',',$params['customers']);
				if(sizeof($customer_params) > 0)
				{
					$truBox_table = Mage::getSingleton('core/resource')->getTableName('trubox/item');
					$rs = array();
					foreach ($customer_params as $identify_customer) {
						$customer_id = trim($identify_customer);
						if (!filter_var($customer_id, FILTER_VALIDATE_INT) === false) {
							$customer = Mage::getModel('customer/customer')->load($customer_id);

							if($customer->getId())
							{
								$truBox = Mage::getModel('trubox/trubox')->getCollection()
									->addFieldToFilter('customer_id', $customer->getId())
									->getFirstItem()
								;

								if(!$truBox->getId()){
									Mage::getSingleton('adminhtml/session')->addError(
										Mage::helper('trubox')->__('Customer does not have items: %s', $customer->getId().' - '.$customer->getEmail())
									);
									continue;
								}


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

								if(sizeof($data) > 0)
								{
									$re = Mage::helper('trubox/order')->prepareOrder($data);

									if(sizeof($re) > 0)
										$rs[] = $re;
								}


							}
						} else if(!filter_var($customer_id, FILTER_VALIDATE_EMAIL) === false) {
							$customer = Mage::getModel('customer/customer')->getCollection()
								->addFieldToFilter('email', $customer_id)
								->setOrder('entity_id', 'desc')
								->getFirstItem()
							;

							if($customer->getId())
							{
								$truBox = Mage::getModel('trubox/trubox')->getCollection()
									->addFieldToFilter('customer_id', $customer->getId())
									->getFirstItem()
								;

								if(!$truBox->getId()){
									Mage::getSingleton('adminhtml/session')->addError(
										Mage::helper('trubox')->__('Customer does not have items: %s', $customer->getId().' - '.$customer->getEmail())
									);
									continue;
								}


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

								if(sizeof($data) > 0)
								{
									$re = Mage::helper('trubox/order')->prepareOrder($data);

									if(sizeof($re) > 0)
										$rs[] = $re;
								}


							}
						} else {
							Mage::getSingleton('adminhtml/session')->addError(
								Mage::helper('trubox')->__('Error data: %s', $identify_customer)
							);
						}
					}

					/*$message = str_replace(array('[',']','{','}'),array('','','',''),json_encode($rs));*/
					$message = '';
					if(sizeof($rs) > 0)
					{
						$flag = 0;
						foreach ($rs as $re) {
							if($flag > 0)
								$message .= '- ';
							$message .= str_replace(array('[',']','{','}'),array('','','',''),json_encode($re));
							$message .= '<br />';
							$flag++;
						}

					}
					if(sizeof($rs) > 0)
						Mage::getSingleton('adminhtml/session')->addSuccess(
							Mage::helper('trubox')->__(
								'Total Order(s) was successfully created: <b style="color: red;">%s</b> <br />- %s',sizeof($rs),$message
							)
						);
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