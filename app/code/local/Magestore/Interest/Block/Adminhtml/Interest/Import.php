<?php

class Magestore_Interest_Block_Adminhtml_Interest_Import extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();

        $this->_objectId = 'id';
        $this->_blockGroup = 'interest';
        $this->_controller = 'adminhtml_interest';
        $this->_mode = 'import';
        $this->_updateButton('save', 'label', Mage::helper('interest')->__('Import'));

    }

    /**
     * get text to show in header when edit an item
     *
     * @return string
     */
    public function getHeaderText()
    {
        return Mage::helper('interest')->__('Import Interest');
    }
}

?>
