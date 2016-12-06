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
class Magestore_RewardPointsCoupon_IndexController extends Mage_Core_Controller_Front_Action {

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
        if (!Mage::getSingleton('customer/session')->isLoggedIn()) {
            Mage::getSingleton('customer/session')->setAfterAuthUrl(
                    Mage::getUrl($this->getFullActionName('/'))
            );
            $this->_redirect('customer/account/login');
            $this->setFlag('', Mage_Core_Controller_Varien_Action::FLAG_NO_DISPATCH, true);
            return;
        }
    }

    /**
     * index action
     */
    public function indexAction() {
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
            Mage::getSingleton('rewardpointscoupon/session')->addError($this->__('Reward Code is not valid.'));
            $this->_redirect("*");
            return;
        }
        $customer = Mage::getSingleton('customer/session')->getCustomer();
        $codeArray = Mage::helper('rewardpointscoupon')->checkCode($code, $customer);

        if (count($codeArray) == 0) {
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
            $this->_redirect("*");
            return;
        }
        Mage::getSingleton('rewardpointscoupon/session')->setMaxWrongCode(0);
        try {
            Mage::helper('rewardpoints/action')->addTransaction('coupon', $customer, $codeArray, array());
            $this->addUsageCoupon($codeArray['template_id'], $customer->getId(), $code);
            Mage::getSingleton('rewardpointscoupon/session')->addSuccess($this->__('Reward Code redeemed successfully. Your balance was added %s.', Mage::helper('rewardpoints/point')->format($codeArray['point_balance'])));
            $this->_redirect('*');
        } catch (Exception $exc) {
            Mage::getSingleton('rewardpointscoupon/session')->addError($this->__('There is an error accur. Please try again.'));
            $this->_redirect('*');
        }
        return $this;
    }

    protected function addUsageCoupon($templateId, $customerId, $couponCode) {
        $template = Mage::getModel('rewardpointscoupon/template');
        $template->load($templateId);
        if ($template->getId()) {
            $template->setTimesUsed($template->getTimesUsed() + 1);
            $template->save();

            $templateCustomer = Mage::getModel('rewardpointscoupon/template_customer');
            $templateCustomer->loadByCustomerRule($customerId, $templateId);

            if ($templateCustomer->getId()) {
                $templateCustomer->setTimesUsed($templateCustomer->getTimesUsed() + 1);
            } else {
                $templateCustomer
                        ->setCustomerId($customerId)
                        ->setTemplateId($templateId)
                        ->setTimesUsed(1);
            }
            $templateCustomer->save();
        }
        
        $coupon = Mage::getModel('rewardpointscoupon/coupon');
        $coupon->load($couponCode, 'code');
        if ($coupon->getId()) {
            $coupon->setTimesUsed($coupon->getTimesUsed() + 1);
            $coupon->save();
            if ($customerId) {
                $couponUsage = Mage::getResourceModel('rewardpointscoupon/coupon_usage');
                $couponUsage->updateCustomerCouponTimesUsed($customerId, $coupon->getId());
            }
        }
        return $this;
    }

}
