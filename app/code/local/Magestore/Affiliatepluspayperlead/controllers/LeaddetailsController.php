<?php

class Magestore_Affiliatepluspayperlead_LeaddetailsController extends Mage_Core_Controller_Front_Action
{
	protected function _getAccountHelper(){
		return Mage::helper('affiliateplus/account');
	}
	
	protected function _getHelper(){
		return Mage::helper('affiliateplus');
	}
	
	protected function _getPayperleadHelper(){
		return Mage::helper('affiliatepluspayperlead');
	}
	
	protected function _getCoreSession(){
		return Mage::getSingleton('core/session');
	}
	
	public function indexAction(){
		$this->loadLayout();
		$this->renderLayout();
	}
	/* Personal URL */
    public function personalAction(){
    	if ($this->_getAccountHelper()->accountNotLogin()){
    		return $this->_redirect('affiliateplus/account/login');
    	}
    	if ($data = $this->getRequest()->getPost()){
    		$session = $this->_getCoreSession();
	    	if (!$data['payperlead_url']){
	    		$session->addError($this->__('Please enter a valid custom url'));
	    		return $this->_redirect('*/*/index');
	    	}
	    	$account = Mage::getSingleton('affiliateplus/session')->getAccount();
	    	$store = Mage::app()->getStore();
	    	$targetPath = Mage::helper('affiliatepluspayperlead')->getTargetPage();
	    	//Zend_Debug::dump($targetPath);die();
	    	if($targetPath){
	    		$requestPath = trim($targetPath,'/').'/'.$data['payperlead_url'];
	    	}else{
	    		$requestPath = $data['payperlead_url'];
	    	}
	    	//Zend_Debug::dump($requestPath);die('1');
	    	$idPath = 'affiliatepluspayperlead/'.$store->getId().'/'.$account->getId();
	    	$existedRewirte = Mage::getResourceModel('core/url_rewrite_collection')
	    			->addFieldToFilter('store_id',$store->getId())
	    			->addFieldToFilter('request_path',$requestPath)
	    			->addFieldToFilter('id_path',array('neq' => $idPath))
	    			->getFirstItem();
    		if ($existedRewirte->getId()){
    			$session->addError($this->__('This custom url already existed. Please choose another custom url.'));
    			$session->setPayperleadUrl($data['payperlead_url']);
    			return $this->_redirect('*/*/index');
    		}
    		
    		if(!$targetPath){
	    		$pageId = Mage::getStoreConfig('web/default/cms_home_page');
	    		if (strpos($pageId,'|') !== false) $pageId = substr($pageId,strpos($pageId,'|')+1);
	    		$targetPath = "cms/page/view/id/$pageId/?acc=";
	    		$targetPath .= $account->getIdentifyCode();
    		}else{
    			$targetPath .= '?acc='.$account->getIdentifyCode();
    		}
                // Changed By Adam 12/11/2014
    		if (Mage::app()->getDefaultStoreView() && $store->getId() != Mage::app()->getDefaultStoreView()->getId())
    			$targetPath .= '&___store='.$store->getCode();
    		$rewrite = Mage::getModel('core/url_rewrite')->load($idPath,'id_path');
    		$rewrite->addData(array(
    			'store_id'	=> $store->getId(),
    			'id_path'	=> $idPath,
    			'request_path'	=> $requestPath,
    			'target_path'	=> $targetPath,
    			'is_system'	=> 0,
    		));
    		try {
    			$rewrite->save();
    			$session->addSuccess($this->__('Your custom url has been saved successfully!'));
    		} catch (Exception $e){
    			$session->addError($e->getMessage());
    			$session->setAffilateCustomUrl($data['payperlead_url']);
    		}
    	}
    	$this->_redirect('*/*/index');
    }
}