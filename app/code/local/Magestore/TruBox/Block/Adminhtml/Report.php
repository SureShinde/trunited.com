<?php

class Magestore_TruBox_Block_Adminhtml_Report extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_controller = 'adminhtml_report';
        $this->_blockGroup = 'trubox';
        $this->_headerText = Mage::helper('trubox')->__('TruBox Summary Report');
        $this->_addButtonLabel = Mage::helper('trubox')->__('TruBox Summary Report');
        parent::__construct();
        $this->_removeButton('add');
    }
}