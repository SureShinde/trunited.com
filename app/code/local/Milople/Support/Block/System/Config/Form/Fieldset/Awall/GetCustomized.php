<?php
/**
* 
* Do not edit or add to this file if you wish to upgrade the module to newer
* versions in the future. If you wish to customize the module for your 
* needs please contact us to https://www.milople.com/magento-extensions/contacts/ 
* 
* @category    Ecommerce
* @package     Milople_Support
* @copyright   Copyright (c) 2015 Milople Technologies Pvt. Ltd. All Rights Reserved.
* @url	       http://www.milople.com/
*
**/

class Milople_Support_Block_System_Config_Form_Fieldset_Awall_GetCustomized extends Mage_Adminhtml_Block_System_Config_Form_Fieldset
{
	public function render(Varien_Data_Form_Element_Abstract $element)
    {
		$html = $this->_getHeaderHtml($element);
		$html .='<div><p>Looking for some customization in any of our extension? Then <a target="_blank" href="https://www.milople.com/magento-extensions/contacts/" rel="nofollow">Contact us</a> to get the extension customized specially for you as you wanted.</p></div>';
		$html .= $this->_getFooterHtml($element);
		return $html;
	}
}