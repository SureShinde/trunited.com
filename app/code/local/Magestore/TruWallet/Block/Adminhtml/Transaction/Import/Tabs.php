<?php

class Magestore_TruWallet_Block_Adminhtml_Transaction_Import_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

  public function __construct()
  {
      parent::__construct();
      $this->setId('importtransaction_tabs');
      $this->setDestElementId('edit_form');
      $this->setTitle(Mage::helper('truwallet')->__('Import Transaction'));
  }

  protected function _beforeToHtml()
  {
      $this->addTab('form_section', array(
          'label'     => Mage::helper('truwallet')->__('Import Transaction'),
          'title'     => Mage::helper('truwallet')->__('Import Transaction'),
          'content'   => $this->getLayout()->createBlock('truwallet/adminhtml_transaction_import_tab_form')->toHtml(),
      ));
     
      return parent::_beforeToHtml();
  }
}