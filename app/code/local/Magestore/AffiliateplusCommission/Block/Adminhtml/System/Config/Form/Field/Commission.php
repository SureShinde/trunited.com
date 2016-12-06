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
 * @package     Magestore_AffiliateplusCommission
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * Config level commission
 * 
 * @category    Magestore
 * @package     Magestore_AffiliateplusCommission
 * @author      Magestore Developer
 */
class Magestore_AffiliateplusCommission_Block_Adminhtml_System_Config_Form_Field_Commission extends Mage_Adminhtml_Block_System_Config_Form_Field_Array_Abstract
{
	public function __construct(){
		$this->addColumn('sales',array(
			'label'	=> Mage::helper('affiliateplus')->__('#Sales/#Orders'),
			'style'	=> 'width:120px',
		));
		
		$this->addColumn('commission',array(
			'label'	=> Mage::helper('affiliateplus')->__('Commission'),
			'style'	=> 'width:120px',
		));
		
		$this->_addAfter = false;
		$this->_addButtonLabel = Mage::helper('affiliateplus')->__('Add Level');
		parent::__construct();
	}
}
