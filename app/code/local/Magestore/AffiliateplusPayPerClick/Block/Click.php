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
 * @package     Magestore_AffiliateplusPayPerClick
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * Affiliatepluspayperclick Block
 * 
 * @category    Magestore
 * @package     Magestore_AffiliateplusPayPerClick
 * @author      Magestore Developer
 */
class Magestore_AffiliateplusPayPerClick_Block_Click extends Mage_Core_Block_Template {

    public function __construct() {
        parent::__construct();
    }

    public function _prepareLayout() {
        parent::_prepareLayout();
        $check = Mage::helper('affiliatepluspayperclick')->isPluginEnabled();
        if (!$check) {
            return $this;
        }
        $this->addBlockCommission();
    }

    public function addBlockCommission() {
        $block = $this->getLayout()->getBlock('sales_statistic');
        $block->addTransactionBlock(
                'payperclick', $this->__('Pay per Click'), 'affiliatepluspayperclick/index/listClickTransaction', 'affiliatepluspayperclick/clickdetails', 'affiliatepluspayperclick/commission.phtml'
        );
    }
}
