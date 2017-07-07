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
 * @package     Magestore_AffiliateplusCostPermille
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * Affiliatepluscostpermille Block
 * 
 * @category    Magestore
 * @package     Magestore_AffiliateplusCostPermille
 * @author      Magestore Developer
 */
class Magestore_AffiliateplusCostPermille_Block_Impression extends Mage_Core_Block_Template
{
    /**
     * prepare block's layout
     *
     * @return Magestore_AffiliateplusCostPermille_Block_Impression
     */
    public function __construct() {
        parent::__construct();
    }
    public function _prepareLayout()
    {
        parent::_prepareLayout();
        $this->addBlockCommission();
    }
    
    public function addBlockCommission()
    {
        if(!Mage::helper('affiliatepluscostpermille')->isDisplayed()){
            return;
        }
        $block = $this->getLayout()->getBlock('sales_statistic');
        $block->addTransactionBlock(
                'costpermille',
                $this->__('Pay per Mille'),
                'affiliatepluscostpermille/index/listCpmTransaction',
                'affiliatepluscostpermille/commissions',
                'affiliatepluscostpermille/commissions.phtml'
        );
    }
    
}