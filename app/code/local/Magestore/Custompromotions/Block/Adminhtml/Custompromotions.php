<?php

class Magestore_Custompromotions_Block_Adminhtml_Custompromotions extends Mage_Adminhtml_Block_Widget_Grid_Container
{
	public function __construct(){
		$this->_controller = 'adminhtml_custompromotions';
		$this->_blockGroup = 'custompromotions';
		$this->_headerText = Mage::helper('custompromotions')->__('Black Friday Report');
		parent::__construct();
		$this->_removeButton('add');
	}
}