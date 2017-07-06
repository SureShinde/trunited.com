<?php

class Magestore_Custompromotions_Block_Adminhtml_Sms_Import extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();

        $this->_objectId = 'id';
        $this->_blockGroup = 'custompromotions';
        $this->_controller = 'adminhtml_sms';
        $this->_mode = 'import';
        $this->_updateButton('save', 'label', Mage::helper('truwallet')->__('Import'));

    }

    /**
     * get text to show in header when edit an item
     *
     * @return string
     */
    public function getHeaderText()
    {
        return Mage::helper('custompromotions')->__('Import SMS');
    }
}

?>
