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
 * Rewardpointscoupon Model
 * 
 * @category    Magestore
 * @package     Magestore_RewardPointsCoupon
 * @author      Magestore Developer
 */
class Magestore_RewardPointsCoupon_Model_Coupon extends Mage_Core_Model_Abstract {

    public function _construct() {
        parent::_construct();
        $this->_init('rewardpointscoupon/coupon');
    }

    public function loadByCode($couponCode) {
        $this->load($couponCode, 'code');
        return $this;
    }

    public function loadPrimaryByTemplate($template) {
        $this->getResource()->loadPrimaryByTemplate($this, $template);
        return $this;
    }

    protected function _beforeSave() {
        return parent::_beforeSave();
    }

    public function loadByTemplateId($id){
        $result = array();
        if(!$id) return $result; //validator
        $coupons = Mage::getModel('rewardpointscoupon/coupon')->getCollection()
            ->addFieldToFilter('is_primary',1)
            ->addFieldToFilter('template_id',$id)
            ->getFirstItem();
        if($coupons->getId()){
            $result = array($coupons->getId());
        } else{
            $coupons = Mage::getModel('rewardpointscoupon/coupon')->getCollection()
//                ->addFieldToFilter('is_primary',array('neq',1))
                ->addFieldToFilter('template_id',$id);
            foreach($coupons as $coupon){
                $result[] = $coupon->getId();
            }
        }

        return $result;
    }

}
