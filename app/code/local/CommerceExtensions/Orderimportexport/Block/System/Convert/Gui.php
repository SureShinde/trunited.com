<?php

class CommerceExtensions_Orderimportexport_Block_System_Convert_Gui extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_controller = 'system_convert_gui';
        $this->_blockGroup = 'commerceextensions_orderimportexport';
        
        $this->_headerText = Mage::helper('commerceextensions_orderimportexport')->__('Profiles');
        $this->_addButtonLabel = Mage::helper('commerceextensions_orderimportexport')->__('Add New Profile');

        parent::__construct();
    }
}