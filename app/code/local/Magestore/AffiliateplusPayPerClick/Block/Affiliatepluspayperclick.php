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
class Magestore_AffiliateplusPayPerClick_Block_Affiliatepluspayperclick extends Mage_Core_Block_Template {

    /**
     * prepare block's layout
     *
     * @return Magestore_AffiliateplusPayPerClick_Block_Affiliatepluspayperclick
     */
    public function _prepareLayout() {
        $this->setTemplate('affiliatepluspayperclick/affiliatepluspayperclick.phtml');
        return parent::_prepareLayout();
    }

    protected function _getConfigHelper() {
        return Mage::helper('affiliatepluspayperclick');
    }
}