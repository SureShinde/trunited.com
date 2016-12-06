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
class Magestore_RewardPointsCoupon_Model_Template extends Mage_Core_Model_Abstract {

    protected static $_couponCodeGenerator;

    /**
     * Rule's primary coupon
     *
     * @var Mage_SalesRule_Model_Coupon
     */
    protected $_primaryCoupon;

    /**
     * Rule's subordinate coupons
     *
     * @var array of Mage_SalesRule_Model_Coupon
     */
    protected $_coupons;


    public function _construct() {
        parent::_construct();
        $this->_init('rewardpointscoupon/template');
    }

    public static function getCouponMassGenerator() {
        return Mage::getSingleton('rewardpointscoupon/coupon_massgenerator');
    }

    /**
     * 
     * @return type
     */
    public function getCoupons() {
        if ($this->_coupons === null) {
            $collection = Mage::getResourceModel('rewardpointscoupon/coupon_collection');
            $collection->addRuleToFilter($this);
            $this->_coupons = $collection->getItems();
        }
        return $this->_coupons;
    }

    protected function _beforeSave() {
        parent::_beforeSave();
        if ($this->hasWebsiteIds()) {
            $websiteIds = $this->getWebsiteIds();
            if (is_array($websiteIds) && !empty($websiteIds)) {
                $this->setWebsiteIds(implode(',', $websiteIds));
            }
        }
        if ($this->hasCustomerGroupIds()) {
            $groupIds = $this->getCustomerGroupIds();
            if (is_array($groupIds) && !empty($groupIds)) {
                $this->setCustomerGroupIds(implode(',', $groupIds));
            }
        }
        return $this;
    }

    /**
     * Set coupon code and uses per coupon
     *
     * @return Magestore_RewardPointsCoupon_Model_Template
     */
    protected function _afterLoad() {
        $this->setCouponCode($this->getPrimaryCoupon()->getCode());
        if ($this->getUsesPerCoupon() !== null && !$this->getUseAutoGeneration()) {
            $this->setUsesPerCoupon($this->getPrimaryCoupon()->getUsageLimit());
        }
        return parent::_afterLoad();
    }

    public function getPrimaryCoupon() {
        if ($this->_primaryCoupon === null) {
            $this->_primaryCoupon = Mage::getModel('rewardpointscoupon/coupon');
            $this->_primaryCoupon->loadPrimaryByTemplate($this->getId());
            $this->_primaryCoupon->setTemplateId($this->getId())->setIsPrimary(true);
        }
        return $this->_primaryCoupon;
    }

    /**
     * Save/delete coupon
     *
     * @return Magestore_RewardPointsCoupon_Model_Template
     */
    protected function _afterSave() {
        $couponCode = trim($this->getCouponCode());
        if (strlen($couponCode) && !$this->getUseAutoGeneration()
        ) {
            $this->getPrimaryCoupon()
                    ->setCode($couponCode)
                    ->setUsageLimit($this->getUsesPerCoupon() ? $this->getUsesPerCoupon() : null)
                    ->setUsagePerCustomer($this->getUsesPerCustomer() ? $this->getUsesPerCustomer() : null)
                    ->setExpirationDate($this->getToDate())
                    ->save();
        } else {
            $this->getPrimaryCoupon()->delete();
        }

        parent::_afterSave();
        return $this;
    }

}
