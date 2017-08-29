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
 * @package     Magestore_RewardPointsCoupon
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * RewardPointsCoupon Index Controller
 * 
 * @category    Magestore
 * @package     Magestore_RewardPointsCoupon
 * @author      Magestore Developer
 */
class Magestore_RewardPointsCoupon_CheckController extends Mage_Core_Controller_Front_Action {

    public function preDispatch() {
        parent::preDispatch();
        if (!$this->getRequest()->isDispatched()) {
            return;
        }
        if (!Mage::helper('rewardpointscoupon')->isEnabled()) {
            $this->_redirect('customer/account');
            $this->setFlag('', Mage_Core_Controller_Varien_Action::FLAG_NO_DISPATCH, true);
            return;
        }
        if (Mage::getSingleton('customer/session')->isLoggedIn()) {
            $this->_redirect('*');
            $this->setFlag('', Mage_Core_Controller_Varien_Action::FLAG_NO_DISPATCH, true);
            return;
        }
    }

    /**
     * index action
     */
    public function indexAction() {
        //test
        $this->loadLayout();
        $this->initLayoutMessages('rewardpointscoupon/session');
        $this->renderLayout();
    }

    public function checkOrRedeemAction() {
        $wrong_number = Mage::getStoreConfig('rewardpoints/couponplugin/max_redeem_code');
        if($wrong_number > 0){
            $number_wrong = Mage::getSingleton('rewardpointscoupon/session')->getMaxWrongCode();
            if($number_wrong >= $wrong_number){
                Mage::getSingleton('rewardpointscoupon/session')->addError($this->__('Reward Code has exceeded the number of uses allowed.'));
                $this->_redirect("*/check");
                return;
            }
        }

        $code = $this->getRequest()->getParam('code');
        if (!$code) {
            Mage::getSingleton('rewardpointscoupon/session')->addError($this->__('Please enter Reward Code'));
            $this->_redirect("*/check");
            return;
        }
        $codeObject = Mage::helper('rewardpointscoupon')->checkCode($code);
//        Zend_Debug::dump($codeObject); exit();
        if (count($codeObject) == 0) {
            $wrong_number = Mage::getStoreConfig('rewardpoints/couponplugin/max_redeem_code');
            if($wrong_number > 0){
                $number_wrong = Mage::getSingleton('rewardpointscoupon/session')->getMaxWrongCode() + 1;
                Mage::getSingleton('rewardpointscoupon/session')->setMaxWrongCode($number_wrong);
                if($number_wrong >= $wrong_number){
                    Mage::getSingleton('rewardpointscoupon/session')->addError($this->__('Reward Code is not valid. You don’t have any times left to redeem/ check code.'));
                }else{
                    Mage::getSingleton('rewardpointscoupon/session')->addError($this->__('Reward Code is not valid. You have %s time(s) remaining to redeem code.', $wrong_number - $number_wrong));
                }
            }else            
                Mage::getSingleton('rewardpointscoupon/session')->addError($this->__('Reward Code is not valid.'));
            $this->_redirect("*/check");
            return;
        }
        Mage::getSingleton('rewardpointscoupon/session')->setMaxWrongCode(0);
        
        $type = $this->getRequest()->getParam('type_submit');
        if($type == '1'){
            Mage::getSingleton('rewardpointscoupon/session')->setData('current_check_code', $codeObject);
            $this->_redirect("*/check");
        }else{
            Mage::getSingleton('customer/session')->setAfterAuthUrl(
                    Mage::getUrl("*/index/checkOrRedeem", array('code' => $code))
            );
            Mage::getSingleton('rewardpointscoupon/session')->setData('current_check_code_login', $codeObject);
            Mage::getSingleton('customer/session')->addNotice($this->__('Redeem code successfully. Your balance was added.'));
            $this->_redirect('customer/account/login');
        }
        return $this;
    }

}
