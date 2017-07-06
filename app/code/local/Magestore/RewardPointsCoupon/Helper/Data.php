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
 * RewardPointsCoupon Helper
 * 
 * @category    Magestore
 * @package     Magestore_RewardPointsCoupon
 * @author      Magestore Developer
 */
class Magestore_RewardPointsCoupon_Helper_Data extends Mage_Core_Helper_Abstract {

    public function isEnabled(){
        return Mage::getStoreConfig('rewardpoints/couponplugin/enable') && Mage::helper('rewardpoints')->isEnable();
    }
    public function getLinkCheckCode(){
//        if(Mage::helper('customer')->isLoggedIn()) $url = Mage::getUrl('rewardpointscoupon');
        $url = Mage::getUrl('rewardpointscoupon/check');
        return $url;
    }
    public function checkCode($couponCode, $customer = null) {
        $codeInfo = array();
        if($customer != null) $customerId = $customer->getId();
        else $customerId = null;
        $websiteId = Mage::app()->getWebsite()->getId();
        $now = Mage::getModel('core/date')->date('Y-m-d');
        if (strlen($couponCode)) {
            $coupon = Mage::getModel('rewardpointscoupon/coupon');
            $coupon->load($couponCode, 'code');

            if ($coupon->getId()) {
                // check entire usage limit
                $templateId = $coupon->getTemplateId();
//                $template = Mage::getModel('rewardpointscoupon/template')->load($templateId);
                $templateCollection = Mage::getModel('rewardpointscoupon/template')->getCollection()
                        ->addFieldToFilter('template_id', $templateId)
                        ->addFieldToFilter('status', 1)
                        ->addFieldToFilter('website_ids', array('finset'=>$websiteId));
                if($customer != null){
                    $templateCollection->addFieldToFilter('customer_group_ids', array('finset'=>$customer->getGroupId()));
                }
                $templateCollection->getSelect()
                        ->where('from_date is null or from_date <= ?', $now)
                        ->where('to_date is null or to_date >= ?', $now);
                $template = $templateCollection->getFirstItem();
                if (!$template->getId())
                    return $codeInfo;
                if(!$template->getUseAutoGeneration()){
                    if($template->getCouponCode() !== $couponCode) return $codeInfo;
                }
                if ($coupon->getUsageLimit() && $coupon->getTimesUsed() >= $coupon->getUsageLimit()) {
                    return $codeInfo;
                }
                // check per customer usage limit
                if ($customerId) {
                    if ($coupon->getUsagePerCustomer()) {
                        $couponUsage = new Varien_Object();
                        Mage::getResourceModel('rewardpointscoupon/coupon_usage')->loadByCustomerCoupon(
                                $couponUsage, $customerId, $coupon->getId());
                        if ($couponUsage->getCouponId() &&
                                $couponUsage->getTimesUsed() >= $coupon->getUsagePerCustomer()
                        ) {
                            return $codeInfo;
                        }
                    }

                    if ($template->getUsesPerCustomer()) {
                        $ruleCustomer = Mage::getModel('rewardpointscoupon/template_customer');
                        $ruleCustomer->loadByCustomerRule($customerId, $templateId);
                        if ($ruleCustomer->getId()) {
                            if ($ruleCustomer->getTimesUsed() >= $template->getUsesPerCustomer()) {
                                return $codeInfo;
                            }
                        }
                    }
                }
//                $codeCollection = Mage::getModel('rewardpointscoupon/coupon')->getCollection()
//                        ->addFieldToFilter('main_table.code', $couponCode);
//                $now = Mage::getModel('core/date')->date('Y-m-d');
//                $codeCollection->getSelect()
//                        ->join(array('temp' => Mage::getSingleton('core/resource')->getTableName('rewardpointscoupon/template')), 'main_table.template_id = temp.template_id')
//                        ->where('temp.status = 1')
//                        ->where('temp.from_date is null or temp.from_date <= ?', $now)
//                        ->where('temp.to_date is null or temp.to_date >= ?', $now);
                $template->setCode($coupon->getCode());
                $codeInfo = $template->getData();
            }
        }
        return $codeInfo;
    }
    
    public function calcCode($expression) {
        if ($this->isExpression($expression)) {
            return preg_replace_callback('#\[([AN]{1,2})\.([0-9]+)\]#', array($this, 'convertExpression'), $expression);
        } else {
            return $expression;
        }
    }

    public function convertExpression($param) {
        $alphabet = (strpos($param[1], 'A')) === false ? '' : 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $alphabet .= (strpos($param[1], 'N')) === false ? '' : '0123456789';
        return $this->getRandomString($param[2], $alphabet);
    }

    public function isExpression($string) {
        return preg_match('#\[([AN]{1,2})\.([0-9]+)\]#', $string);
    }


    public function getReferConfig($code, $store = null) {
        return Mage::getStoreConfig('rewardpoints/couponplugin/' . $code, $store);
    }

    /**
     * get style color of coupon to print pdf
     * @return String
     */
    public function getPdfStyleColor() {
        return '#' . $this->getReferConfig('style_color');
    }

    /**
     * get text color of coupon to print pdf
     * @return string
     */
    public function getPdfCouponColor() {
        return '#' . $this->getReferConfig('coupon_color');
    }

    /**
     * limit customer uses coupon
     * @return type
     */
    public function getUsesPerCustomer() {
        return $this->getReferConfig('uses_per_customer');
    }

    /**
     * get type discount show in pdf
     */
    public function getTypeMaxDiscount($store_id = null) {
        return $this->getReferConfig('max_discount', $store_id);
    }

    public function getCaptionCoupon($store_id = null) {
        return $this->getReferConfig('caption', $store_id);
    }

    public function getBackgroundCoupon($store_id = null) {
        return '#' . $this->getReferConfig('background_coupon', $store_id);
    }

    public function getBackgroundImg($store_id = null) {
        return $this->getReferConfig('background', $store_id);
    }


}