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
 * Rewardpointscoupon Resource Collection Model
 * 
 * @category    Magestore
 * @package     Magestore_RewardPointsCoupon
 * @author      Magestore Developer
 */
class Magestore_RewardPointsCoupon_Model_Mysql4_Coupon_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract {

    public function _construct() {
        parent::_construct();
        $this->_init('rewardpointscoupon/coupon');
    }

    public function addAttributeToFilter($field, $value) {
        $this->addFilter($field, $value);
        return $this;
    }

    public function addGeneratedCouponsFilter() {
        $this->addFieldToFilter('is_primary', array('null' => 1));
        return $this;
    }

    public function getCoupon() {
        return $this->getCode();
    }

    public function addIsUsedFilterCallback($collection, $column) {
        $filterValue = $column->getFilter()->getCondition();
        $collection->addFieldToFilter(
                $this->getConnection()->getCheckSql('main_table.times_used > 0', 1, 0), array('eq' => $filterValue)
        );
    }

}
