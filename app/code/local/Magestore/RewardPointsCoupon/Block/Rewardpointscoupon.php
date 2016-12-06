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
 * Rewardpointscoupon Block
 * 
 * @category    Magestore
 * @package     Magestore_RewardPointsCoupon
 * @author      Magestore Developer
 */
class Magestore_RewardPointsCoupon_Block_Rewardpointscoupon extends Mage_Core_Block_Template
{
    /**
     * prepare block's layout
     *
     * @return Magestore_RewardPointsCoupon_Block_Rewardpointscoupon
     */
    public function _prepareLayout()
    {
        return parent::_prepareLayout();
    }
    public function getCheckOrRedeemUrlLogin(){
        return Mage::getUrl('rewardpointscoupon/check/checkOrRedeem');
    }
    public function getCheckOrRedeemUrl(){
        return Mage::getUrl('rewardpointscoupon/index/checkOrRedeem');
    }
    public function getCurrentCheckCode(){
        $currentCode = Mage::getSingleton('rewardpointscoupon/session')->getData('current_check_code');
        if($currentCode) {
            Mage::getSingleton('rewardpointscoupon/session')->setData('current_check_code', NULL);
            return $currentCode;
        } else return null;
    }
    public function getCustomerGroup($groupIds){
        $groupIds = explode(',', $groupIds);
        $groups = Mage::getModel('customer/group')->getCollection()
                ->addFieldToSelect('customer_group_code')
                ->addFieldToFilter('customer_group_id', array('in'=>$groupIds));
        $groupsArray = array();
        foreach ($groups as $group){
            $groupsArray[] = $group->getCustomerGroupCode();
        }
        return implode(', ', $groupsArray);
    }
}