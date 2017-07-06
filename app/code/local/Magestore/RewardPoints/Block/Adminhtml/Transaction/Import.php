<?php

class Magestore_RewardPoints_Block_Adminhtml_Transaction_Import extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();

        $this->_objectId = 'id';
        $this->_blockGroup = 'rewardpoints';
        $this->_controller = 'adminhtml_transaction';
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
        return Mage::helper('rewardpoints')->__('Import Transactions');
    }
}

?>
