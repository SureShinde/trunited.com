<?php

class Magestore_Interest_Block_Adminhtml_Interest extends Mage_Adminhtml_Block_Widget_Grid_Container
{
	public function __construct(){
		$this->_controller = 'adminhtml_interest';
		$this->_blockGroup = 'interest';
		$this->_headerText = Mage::helper('interest')->__('Item Manager');
		$this->_addButtonLabel = Mage::helper('interest')->__('Add Item');
		parent::__construct();
        $this->_addButton('import', array(
            'label' => Mage::helper('interest')->__('Import Interest'),
            'onclick' => "setLocation('" . $this->getUrl('*/*/import', array('page_key' => 'collection')) . "')",
            'class' => 'add'
        ));
	}
}
