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
 * Rewardpointstransfer Adminhtml Block
 * 
 * @category    Magestore
 * @package     Magestore_RewardPointsTransfer
 * @author      Magestore Developer
 */
class Magestore_RewardPointsTransfer_Block_Adminhtml_Rewardpointstransfer extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_controller = 'adminhtml_rewardpointstransfer';
        $this->_blockGroup = 'rewardpointstransfer';
        $this->_headerText = Mage::helper('rewardpointstransfer')->__('All Point Transfers');
        $this->_addButtonLabel = Mage::helper('rewardpointstransfer')->__('Add New Transfer');
        parent::__construct();
    }
}