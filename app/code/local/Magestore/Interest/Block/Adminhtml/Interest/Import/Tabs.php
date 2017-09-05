<?php

class Magestore_Interest_Block_Adminhtml_Interest_Import_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

  public function __construct()
  {
      parent::__construct();
      $this->setId('importinteresttabs');
      $this->setDestElementId('edit_form');
      $this->setTitle(Mage::helper('interest')->__('Import Interest'));
  }

  protected function _beforeToHtml()
  {
      $this->addTab('form_section', array(
          'label'     => Mage::helper('interest')->__('Import Interest'),
          'title'     => Mage::helper('interest')->__('Import Interest'),
          'content'   => $this->getLayout()->createBlock('interest/adminhtml_interest_import_tab_form')->toHtml(),
      ));
     
      return parent::_beforeToHtml();
  }
}
