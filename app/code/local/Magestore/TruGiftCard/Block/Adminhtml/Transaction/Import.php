<?php

class Magestore_TruGiftCard_Block_Adminhtml_Transaction_Import extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();

        $this->_objectId = 'id';
        $this->_blockGroup = 'trugiftcard';
        $this->_controller = 'adminhtml_transaction';
        $this->_mode = 'import';
        $this->_updateButton('save', 'label', Mage::helper('trugiftcard')->__('Import'));

    }

    /**
     * get text to show in header when edit an item
     *
     * @return string
     */
    public function getHeaderText()
    {
        return Mage::helper('trugiftcard')->__('Import Transactions');
    }
}

?>
