<?php

class Magestore_ManageApi_Block_Adminhtml_Linkshareadvertisers extends Mage_Adminhtml_Block_Widget_Grid_Container
{
	public function __construct(){
		$this->_controller = 'adminhtml_linkshareadvertisers';
		$this->_blockGroup = 'manageapi';
		$this->_headerText = Mage::helper('manageapi')->__('LinkShare Advertisers');
		$this->_addButtonLabel = Mage::helper('manageapi')->__('Add');
		parent::__construct();
		$this->_removeButton('add');
	}
}