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
 * @package     Magestore_AffiliateplusDirectLink
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * Affiliateplusdirectlink Adminhtml Block
 * 
 * @category    Magestore
 * @package     Magestore_AffiliateplusDirectLink
 * @author      Magestore Developer
 */
class Magestore_AffiliateplusDirectLink_Block_Adminhtml_Affiliateplusdirectlink extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_controller = 'adminhtml_affiliateplusdirectlink';
        $this->_blockGroup = 'affiliateplusdirectlink';
        $this->_headerText = Mage::helper('affiliateplusdirectlink')->__('Direct link Manager');
        $this->_addButtonLabel = Mage::helper('affiliateplusdirectlink')->__('Add Direct link');
        parent::__construct();
    }
}