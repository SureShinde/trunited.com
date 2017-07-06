<?php

class Magestore_Affiliatepluspayperlead_Helper_Data extends Mage_Core_Helper_Abstract
{
	public function getTargetPage(){		
		$basePayperleadUrl = Mage::helper('affiliatepluspayperlead/config')->getPayperleadConfig('base_link');
		$basePayperleadUrl = trim($basePayperleadUrl,'/');
		$baseUrl = Mage::getBaseUrl();
		$target = substr($basePayperleadUrl,strlen($baseUrl));
		$account = Mage::getSingleton('affiliateplus/session')->getAccount();
		$targetPath = substr(Mage::getUrl($target,array(''=>'')),strlen($baseUrl));
		//Zend_Debug::dump($targetPath);die();
		return $targetPath;
	}
}