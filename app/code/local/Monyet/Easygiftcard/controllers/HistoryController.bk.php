<?php
/**

 */
 
class Monyet_Easygiftcard_HistoryController extends Mage_Core_Controller_Front_Action
{
	/*
	const XML_PATH_EMAIL_RECIPIENT  = 'catalog/monyet_easygiftcard/shopadmin_email';
    const XML_PATH_EMAIL_SENDER     = 'contacts/email/sender_email_identity';
    const XML_PATH_VENDOR_EMAIL_TEMPLATE   = 'catalog/monyet_easygiftcard/storeowner_email_template';
    const XML_PATH_VENDOR_EMAIL_TEMPLATE   = 'catalog/monyet_easygiftcard/shopadmin_email_template';
	*/

    public function indexAction()
    {	
		$this->loadLayout();  
		if($head = $this->getLayout()->getBlock('head'))
            $head->setTitle(Mage::helper('sales')->__('My Gift Cards'));
		$this->renderLayout();
    }
	
	public function reportAction()
    {	
		try {
			$orderIncrementId = $this->getRequest()->getParam('order_id');
			$productId = $this->getRequest()->getParam('product_id');
			$product = Mage::getModel('catalog/product')->load($productId);
			$pdfReportPath = base64_decode($this->getRequest()->getParam('file'));
			$_groupedParentsId = Mage::getModel('catalog/product_type_grouped')->getParentIdsByChild($productId);
			$_groupProduct = Mage::getModel('catalog/product');
			foreach($_groupedParentsId as $_groupedId) {
				$_groupProduct->load($_groupedId);
				break;
			}
			$order = Mage::getModel('sales/order')->loadByIncrementId($orderIncrementId);
			$vendorEmail = $_groupProduct->getVendorEmail();
			$pdfPath = $_groupProduct->getPdfPath();
			$path = Mage::getBaseDir() . DIRECTORY_SEPARATOR . $pdfPath;
			if (is_dir($path) && $pdfPath) {
				if ($handle = opendir($path)) {
					//Notice the parentheses I added:
					while (($file = readdir($handle)) !== FALSE) {
						$files_array[] = $file;
					}
					closedir($handle);
				}
				$isInstock = false;
				$fileNo = 0;
				foreach ($files_array as $filename) {
					$data = explode('_', $filename);
					$file = "";
					if (count($data) > 1) { //Valid file name only
						$prefile = str_replace($_groupProduct->getSku().'-', '', $product->getSku());
						if($prefile == $data[0]){
							$file = $filename;
							$filePath = $path . DIRECTORY_SEPARATOR . $file;
							if (file_exists($filePath)) {
								$isInstock = true;
								$fileNo++;
							}
						}
					}
				}
				if($isInstock) {
					foreach ($files_array as $filename) {
						$data = explode('_', $filename);
						$file = "";
						if (count($data) > 1) { //Valid file name only
							$prefile = str_replace($_groupProduct->getSku().'-', '', $product->getSku());
							if($prefile == $data[0]){
								$file = $filename;
								$filePath = $path . DIRECTORY_SEPARATOR . $file;
								$soldFilePath = $path . DIRECTORY_SEPARATOR . 'sold' . DIRECTORY_SEPARATOR . $order->getId() . DIRECTORY_SEPARATOR . $product->getId() . DIRECTORY_SEPARATOR . $file;
								
								if (file_exists($filePath)) {
									if (copy($filePath,$soldFilePath)) {
										unlink($filePath);
										//update inventory
										$stockItem = Mage::getModel('cataloginventory/stock_item')->loadByProduct($productId);
										if ($stockItem->getId() > 0 and $stockItem->getManageStock()) {
											$qty = (int)$stockItem->getQty();
											if($fileNo > 1 && $qty > 1){
												$stockItem->setQty($qty - 1);
											}
											if($fileNo == 1){
												$stockItem->setQty(0);
												$stockItem->setIsInStock(0);
											}
											
											try {
												$stockItem->save();
											} catch (Exception $ex) {
												echo $ex->getMessage();
											}
										}
										break;
									}
								}
							}
						}
					}
				} else {
					//Send email
					$translate = Mage::getSingleton('core/translate');
					$translate->setTranslateInline(false);
					$mailTemplate = Mage::getModel('core/email_template');
					$mailTemplate->setDesignConfig(array('area' => 'frontend'))
						->sendTransactional(
							Mage::getStoreConfig('catalog/monyet_easygiftcard/shopadminnocard_email_template'),
							Mage::getStoreConfig('contacts/email/sender_email_identity'),
							Mage::getStoreConfig('catalog/monyet_easygiftcard/shopadmin_email'),
							null,
							array('order' => $order)
						);
					//Send email to Vendor
					$translate->setTranslateInline(true);
					$translate->setTranslateInline(false);
					$mailTemplate->setDesignConfig(array('area' => 'frontend'))
						->sendTransactional(
							Mage::getStoreConfig('catalog/monyet_easygiftcard/storeownernocard_email_template'),
							Mage::getStoreConfig('contacts/email/sender_email_identity'),
							$vendorEmail,
							null,
							array('order' => $order)
						);
					Mage::getSingleton('core/session')->addError(Mage::helper('contacts')->__('There are currently no replacement gift cards available. Please contact giftcardshop@trunited.com including your order # and the last 4 digits of the invalid gift card number.'));
					$this->_redirect('*/*/');
					return;
				}
			}
			
			$store = $order->getStore();
			$filePath = Mage::getBaseDir() . DIRECTORY_SEPARATOR . $pdfReportPath;
			$translate = Mage::getSingleton('core/translate');
			$translate->setTranslateInline(false);
			$mailTemplate = Mage::getModel('core/email_template');
			//Send email to Administrator
			$mailTemplate->getMail()
					->createAttachment(
					file_get_contents($filePath),
					'application/pdf',
					Zend_Mime::DISPOSITION_ATTACHMENT,
					Zend_Mime::ENCODING_BASE64,
					basename($filePath)
				);
			$mailTemplate->setDesignConfig(array('area' => 'frontend'))
						->sendTransactional(
							Mage::getStoreConfig('catalog/monyet_easygiftcard/shopadmin_email_template'),
							Mage::getStoreConfig('contacts/email/sender_email_identity'),
							Mage::getStoreConfig('catalog/monyet_easygiftcard/shopadmin_email'),
							null,
							array('order' => $order)
						);
			//Send email to Vendor
			
			$translate->setTranslateInline(true);
			$mailTemplate = Mage::getModel('core/email_template');
			$mailTemplate->getMail()
					->createAttachment(
					file_get_contents($filePath),
					'application/pdf',
					Zend_Mime::DISPOSITION_ATTACHMENT,
					Zend_Mime::ENCODING_BASE64,
					basename($filePath)
				);
			$mailTemplate->setDesignConfig(array('area' => 'frontend'))
						->sendTransactional(
							Mage::getStoreConfig('catalog/monyet_easygiftcard/storeowner_email_template'),
							Mage::getStoreConfig('contacts/email/sender_email_identity'),
							$vendorEmail,
							null,
							array('order' => $order)
						);
			
			@mkdir($path . DIRECTORY_SEPARATOR . 'invalid');
			@mkdir($path . DIRECTORY_SEPARATOR . 'invalid' . DIRECTORY_SEPARATOR . $order->getId());
			@mkdir($path . DIRECTORY_SEPARATOR . 'invalid' . DIRECTORY_SEPARATOR . $order->getId() . DIRECTORY_SEPARATOR . $productId);
			$soldFilePath = str_replace('sold', 'invalid', $filePath);
			if (copy($filePath,$soldFilePath)) {
				unlink($filePath);
				
			}
			if (!$mailTemplate->getSentSuccess()) {
				throw new Exception();
			}

			$translate->setTranslateInline(true);
			Mage::getSingleton('core/session')->addSuccess(Mage::helper('contacts')->__('We have received your report of an invalid gift card. We will begin researching the cause. In the meantime, we have issued a replacement you can use by clicking VIEW REPLACEMENT CARD.'));
            $this->_redirect('*/*/');
			return;
		} catch (Exception $e) {
			$translate->setTranslateInline(true);

			Mage::getSingleton('core/session')->addError($e->getMessage());
			$this->_redirect('*/*/');
			return;
		}
    }
   
}