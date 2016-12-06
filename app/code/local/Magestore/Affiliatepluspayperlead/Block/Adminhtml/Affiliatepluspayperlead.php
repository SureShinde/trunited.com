<?php

class Magestore_Affiliatepluspayperlead_Block_Adminhtml_Affiliatepluspayperlead extends Mage_Adminhtml_Block_Widget_Grid_Container
{
	public function __construct(){
		$this->_controller = 'adminhtml_affiliatepluspayperlead';
		$this->_blockGroup = 'affiliatepluspayperlead';
		$this->_headerText = Mage::helper('affiliatepluspayperlead')->__('Lead Manager');		
		parent::__construct();
		$this->_removeButton('add');
	}
}