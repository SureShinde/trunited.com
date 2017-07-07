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
 * @package     Magestore_AffiliateplusCostPerMille
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * Affiliatepluscostpermille Block
 * 
 * @category    Magestore
 * @package     Magestore_AffiliateplusCostPerMille
 * @author      Magestore Developer
 */
class Magestore_AffiliateplusCostPerMille_Block_Affiliatepluscostpermille extends Mage_Core_Block_Template
{
    /**
     * prepare block's layout
     *
     * @return Magestore_AffiliateplusCostPerMille_Block_Affiliatepluscostpermille
     */
    public function _prepareLayout()
    {
        $this->setTemplate('affiliatepluscostpermille/affiliatepluscostpermille.phtml');
        return parent::_prepareLayout();
    }
    
    public function getCommission()
    {
        return Mage::helper('affiliatepluscostpermille/config')->getCostpermilleConfig('commission_per_thousand_impressions');
    }
}