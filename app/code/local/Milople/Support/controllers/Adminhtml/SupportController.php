<?php
/**
* 
* Do not edit or add to this file if you wish to upgrade the module to newer
* versions in the future. If you wish to customize the module for your 
* needs please contact us to https://www.milople.com/magento-extensions/contacts/ 
* 
* @category    Ecommerce
* @package     Milople_Support
* @copyright   Copyright (c) 2015 Milople Technologies Pvt. Ltd. All Rights Reserved.
* @url	       http://www.milople.com/
*
**/

class Milople_Support_Adminhtml_SupportController extends Mage_Adminhtml_Controller_action
{

	protected function _initAction() {
		$this->loadLayout()
			->_setActiveMenu('support/items')
			->_addBreadcrumb(Mage::helper('adminhtml')->__('Items Manager'), Mage::helper('adminhtml')->__('Item Manager'));
		
		return $this;
	}
	public function sendmailAction()
	{
		$to = "magento@milople.com";
		$subject = $_POST['subject'];
		$message = 
						"Name: " . $_POST['name'] . "\n" .
						"Email: " . $_POST['email'] . "\n" .
						"Reason: " . $_POST['reason'] . "\n" .
						"Licensed Domain: " . $_POST['license'] . "\n" .
						"Message: " . $_POST['message'];
		
		$from = Mage::getStoreConfig('trans_email/ident_general/email');
		$headers = "From:" . $from;		
		mail($to,$subject,$message,$headers);
		
		echo "<script type='text/javascript'>";
		echo "alert ('Mail has been sent successfully. We will be in touch with you within short time.');";
		echo "</script>";
		
	}
}