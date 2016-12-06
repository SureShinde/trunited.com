<?php

/**
 * Magestore
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the Magestore.com license that is
 * available through the world-wide-web at this URL:
 * http://www.magestore.com/license-agreement.html
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category    Magestore
 * @package     Magestore_RewardPointsTransfer
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * RewardPointsTransfer Index Controller
 * 
 * @category    Magestore
 * @package     Magestore_RewardPointsTransfer
 * @author      Magestore Developer
 */
class Magestore_RewardPointsTransfer_IndexController extends Mage_Core_Controller_Front_Action {

	/**
	 * xuanbinh
	 * 16/07/2015
	 * check customer group
	 * @see Mage_Core_Controller_Front_Action::preDispatch()
	 */
	public function preDispatch()
	{
		parent::preDispatch();
		
		/* check customer group */
		$customer = Mage::helper('customer')->getCustomer();
		$group = $customer->getGroupId();
		$customerGroup = Mage::helper('rewardpointstransfer')->getTransferconfig('customer_group');
		$cusGroup = explode(',', $customerGroup);
		$check = false;
		foreach($cusGroup as $key=>$val){
			if($val == $group) $check = true;
		}
		/* end check */
		
		if(!$check) {
			$this->norouteAction();
		}
	}
	
    /**
     * index action
     */
    public function indexAction() {
        if (!$this->enable()) {
            $this->_redirect('customer/account/login/');
        }
        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     * ajax function check customer receive transfer email
     * @return string
     */
    public function checkemailAction() {
        if (!$this->enable()) {
            echo 'deni';
            return;
        }
        $data = $this->getRequest()->getPost();
        $email = $data['email'];
        $website = $data['website'];
        $customer = Mage::getModel('customer/customer')->setWebsiteId($website)->loadByEmail($email);
        $customerSend = Mage::helper('customer')->getCustomer();
        if ($customer->getId()) {
            if ($customerSend->getEmail() == $email)
                echo 'deni';
            else
                echo 'correct';
            return;
        }else {
            echo 'incorrect';
            return;
        }
    }

    /**
     * action to create a new transfer
     * @return type
     */
    public function transferAction() {
        if (!$this->enable()) {
            $this->_redirect('customer/account/login/');
        }
        $data = $this->getRequest()->getPost();
        $email = $data['email'];
        $point = $data['point'];
        $message = $data['message'];
        if($email == '' || is_nan($point)){
            Mage::getSingleton('core/session')->addError($this->__('Data error, please try again!'));
            $this->_redirect('*/');
            return;
        }
        if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
            Mage::getSingleton('core/session')->addError($this->__('Please enter a valid email address!'));
            $this->_redirect('*/');
            return;
        }
        
//        $group = Mage::helper('customer')->getCustomer()->getGroupId();
//        $customerGroup = Mage::helper('rewardpointstransfer')->getTransferconfig('customer_group');
//        $cusGroup = explode(',', $customerGroup);
//        $check = 0;
//        foreach($cusGroup as $key=>$val){
//            if($val == $group) $check = 1;
//        }
//        if(!$check){
//            Mage::getSingleton('core/session')->addError($this->__('You do not have the permission to transfer!'));
//            $this->_redirect('*/');
//            return;
//        }

        $customer = Mage::helper('customer')->getCustomer();
        if ($customer->getEmail() == $email) {
            Mage::getSingleton('core/session')->addError($this->__('You cannot transfer points to yourself!'));
            $this->_redirect('*/');
            return;
        }
        $rewardCustomer = Mage::helper('rewardpoints/customer')->getAccountByCustomer($customer);
        if (!$rewardCustomer->getId()) {
            Mage::getSingleton('core/session')->addError($this->__('Your account does not have enough points to transfer!'));
            $this->_redirect('*/');
            return;
        } else {
            $pointReturn = Mage::getBlockSingleton('rewardpointstransfer/rewardpointstransfer')->getPointAvailable();
            $pointMin = Mage::getBlockSingleton('rewardpointstransfer/rewardpointstransfer')->getPointExist();
            if($pointReturn < 0){
                Mage::getSingleton('core/session')->addError($this->__('You do not have the permission to transfer!'));
                $this->_redirect('*/');
                return;
            }else if($pointReturn == 0){
                if($pointMin) Mage::getSingleton('core/session')->addError($this->__('The points on your account must be greater than or equal to %s.', $pointMin));
                else Mage::getSingleton('core/session')->addError($this->__('Your account does not have enough  points to transfer!'));
                $this->_redirect('*/');
                return;
            } else if ($point > $pointReturn) {
                Mage::getSingleton('core/session')->addError($this->__('You cannot transfer more than %s.', Mage::helper('rewardpoints/point')->format($pointReturn)));
                $this->_redirect('*/');
                return;
            }
        }
        $store_id = Mage::app()->getStore()->getId();

//        $transferObject = new Varien_Object();
//        $transferObject->setData('point_amount', $point);
//        $transferObject->setData('store_id', $store_id);
        try {
            $transfer = Mage::helper('rewardpointstransfer')->addTransferTransaction($customer->getEmail(), $email, $point, $message);

            Mage::getSingleton('core/session')->addSuccess($this->__('Points have been sent to %s successfully!', $email));
            $this->_redirect('*/');
        } catch (Exception $e) {
            Mage::getSingleton('core/session')->addError($this->__('Cannot send points to your friend'));
            $this->_redirect('*/');
        }
    }

    /**
     * action to cancel pending, holding transfer
     */
    public function cancelTransferAction() {
        $transferId = $this->getRequest()->getParam('id');
        $transfer = Mage::getModel('rewardpointstransfer/rewardpointstransfer')->load($transferId);
        if(!Mage::helper('rewardpointstransfer')->getTransferConfig('allow_cancel')) return;
        $customerId = Mage::helper('customer')->getCustomer()->getId();
        if($customerId != $transfer->getSenderCustomerId()){
            Mage::getSingleton('core/session')->addError($this->__('You cannot have permission to cancel transfer!'));
            $this->_redirect('*/');
            return;
        }

        if ($transfer->getId() && in_array($transfer->getStatus(), array(Magestore_RewardPointsTransfer_Model_Status::STATUS_PENDING, Magestore_RewardPointsTransfer_Model_Status::STATUS_HOLDING))) {
            $reason = $this->__('Cancel by %s (%s)', Mage::helper('customer')->getCustomer()->getName(), Mage::helper('customer')->getCustomer()->getEmail());
            Mage::helper('rewardpointstransfer')->cancelTransfer($transfer, $reason, true);
        }
        $this->_redirect('*/');
    }

    /**
     * check enable plugin, customer loggedin
     * @return boolean
     */
    public function enable() {
        if (Mage::helper('customer')->isLoggedIn() && Mage::helper('rewardpointstransfer')->isEnable())
            return true;
        else
            return false;
    }
}
