<?php
/**
*
* Do not edit or add to this file if you wish to upgrade the module to newer
* versions in the future. If you wish to customize the module for your
* needs please contact us to https://www.milople.com/magento-extensions/contacts/
*
* @category     Ecommerce
* @package      Indies_Recurringandrentalpayments
* @copyright    Copyright (c) 2015 Milople Technologies Pvt. Ltd. All Rights Reserved.
* @url          https://www.milople.com/magento-extensions/recurring-and-subscription-payments.html
*
* Milople was known as Indies Services earlier.
*
**/

class Indies_Recurringandrentalpayments_Adminhtml_SubscribedController extends Mage_Adminhtml_Controller_Action
{

	protected function _initAction() {
		$this->loadLayout()
			 ->_setActiveMenu('recurringandrentalpayments/subscribed')
			 ->_addBreadcrumb(Mage::helper('adminhtml')->__('Recurring &amp; Rental Subscribed Products'), Mage::helper('adminhtml')->__('Recurring &amp; Rental Subscribed Products'));
		return $this;
	}   
 
	public function indexAction() {
		$this->_initAction();
		$this->renderLayout();
	}

	public function editAction() {
		$id     = $this->getRequest()->getParam('id');
		$model  = Mage::getModel('recurringandrentalpayments/subscription')->load($id);
		if ($model->getId() || $id == 0) {
			$data = Mage::getSingleton('adminhtml/session')->getFormData(true);
			if (!empty($data)) {
				$model->setData($data);
			}
			$data = $model;
			Mage::register('recurringandrentalpayments_data', $data);

			$this->loadLayout();
			$this->_setActiveMenu('recurringandrentalpayments/plans');

			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Period Manager'), Mage::helper('adminhtml')->__('Period Manager'));
			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Pariod News'), Mage::helper('adminhtml')->__('Pariod News'));

			$this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

			$this->_addContent($this->getLayout()->createBlock('recurringandrentalpayments/adminhtml_subscribed_edit'))
				->_addLeft($this->getLayout()->createBlock('recurringandrentalpayments/adminhtml_subscribed_edit_tabs'));

			$this->renderLayout();
		} else {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('recurringandrentalpayments')->__('Pariod does not exist'));
			$this->_redirect('*/*/');
		}
	}
	public function saveAction() {
		if ($id=$this->getRequest()->getParam('id')) {
			$data = $this->getRequest()->getPost();
			$model = Mage::getModel('recurringandrentalpayments/subscription')->load($id);
			$status = $model->getStatus();
			try{
				$model->setStatus($data['lastorder']['status']);
				if($this->getRequest()->getParam('id'))
				{
					$model->setUpdateTime(now());
				}
				$model->save();
			
				if( $status != $data['lastorder']['status'])
				{
					$alert= Mage::getModel('recurringandrentalpayments/alert_event');
					$sender = Mage::getStoreConfig(Indies_Recurringandrentalpayments_Helper_Config::XML_PATH_ORDER_STATUS_CHANGE_SENDER);
					$ccto = Mage::getStoreConfig(Indies_Recurringandrentalpayments_Helper_Config::XML_PATH_ORDER_STATUS_CHANGE_CC_TO);
					
					$admin_status = Mage::getStoreConfig('recurringandrentalpayments/order_status_change_email/subscriptionstatus');			
			        $admin_status_array = explode(',',$admin_status);
					
					switch ($data['lastorder']['status'])
					{
						
						case 1:
								if(in_array('active', $admin_status_array))
								{
								$alert->send($model,Mage::getStoreConfig(Indies_Recurringandrentalpayments_Helper_Config::XML_PATH_ORDER_STATUS_ACTIVE_TEMPLATE),0,$sender,$ccto);
									
								}
						break;
						case 2:
								if(in_array('suspended', $admin_status_array))
								{
								$alert->send($model,Mage::getStoreConfig(Indies_Recurringandrentalpayments_Helper_Config::XML_PATH_ORDER_STATUS_SUSPEND_TEMPLATE),0,$sender,$ccto);	
								}
						break;
						case 3:
								if(in_array('suspended', $admin_status_array))
								{
								$alert->send($model,Mage::getStoreConfig(Indies_Recurringandrentalpayments_Helper_Config::XML_PATH_ORDER_STATUS_SUSPEND_TEMPLATE),0,$sender,$ccto);	
								}
						break;
						case 0:
								if(in_array('expired', $admin_status_array))
								{
									$alert->send($model,Mage::getStoreConfig(Indies_Recurringandrentalpayments_Helper_Config::XML_PATH_ORDER_STATUS_EXPIRE_TEMPLATE),0,$sender,$ccto);	
								}
						break;
						case -1:
								if(in_array('cancelled', $admin_status_array))
								{
								$alert->send($model,Mage::getStoreConfig(Indies_Recurringandrentalpayments_Helper_Config::XML_PATH_ORDER_STATUS_CANCLE_TEMPLATE),0,$sender,$ccto);	
								}
						break;
						default:
						throw new Indies_Recurringandrentalpayments_Exception('subcription alert not send ');
					}
				}
				
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('recurringandrentalpayments')->__('Subscription has been successfully updated.'));
				Mage::getSingleton('adminhtml/session')->setFormData(false);
				if ($this->getRequest()->getParam('back')) {
						$this->_redirect('*/*/edit', array('id' => $model->getId()));
					return;
				}
				$this->_redirect('*/*/');
				return;
			}
			catch (Exception $e)
			{
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setFormData($data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
		}
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('recurringandrentalpayments')->__('Unable to find Subcription to save'));
        $this->_redirect('*/*/');
	}
	public function exportCsvAction()
    {
        $fileName   = 'recurringandrentalpayments.csv';
        $content    = $this->getLayout()->createBlock('recurringandrentalpayments/adminhtml_subscribed_grid')
            ->getCsv();

        $this->_sendUploadResponse($fileName, $content);
    }
	protected function _sendUploadResponse($fileName, $content, $contentType='application/octet-stream')
	{
		$this->_prepareDownloadResponse($fileName, $content, $contentType);
	}
	public function payAction()
    {
		$subscriptionId = $this->getRequest()->getParam('id');
        $sequenceId = $this->getRequest()->getParam('seq');
        $subscription = Mage::getModel('recurringandrentalpayments/subscription')->load($subscriptionId);
		
        Indies_Recurringandrentalpayments_Model_Recurringandrentalpaymentscron::$isCronSession = 1;
		$subscription->paySeq($sequenceId);
        Indies_Recurringandrentalpayments_Model_Recurringandrentalpaymentscron::$isCronSession = 0;
        $this->_redirectReferer();

    }
	protected function _isAllowed()
    {
      return true; 
    }
}