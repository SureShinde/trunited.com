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
 * Rewardpointscoupon Adminhtml Block
 * 
 * @category    Magestore
 * @package     Magestore_RewardPointsCoupon
 * @author      Magestore Developer
 */
class Magestore_RewardPointsCoupon_Block_Adminhtml_Rewardpointscoupon extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_controller = 'adminhtml_rewardpointscoupon';
        $this->_blockGroup = 'rewardpointscoupon';
        $this->_headerText = Mage::helper('rewardpointscoupon')->__('Reward Coupon Manager');
        $this->_addButtonLabel = Mage::helper('rewardpointscoupon')->__('Add Reward Coupon');
        parent::__construct();
    }
}