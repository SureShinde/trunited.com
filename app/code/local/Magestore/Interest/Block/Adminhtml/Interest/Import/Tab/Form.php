<?php

class Magestore_Interest_Block_Adminhtml_Interest_Import_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {

        $importFile = Mage::getBaseUrl('media') . 'interest/import/sample_interest_file.csv';
        $form = new Varien_Data_Form();

        $this->setForm($form);
        $fieldset = $form->addFieldset('import_form', array('legend' => Mage::helper('interest')->__('Import Settings')));

        $fieldset->addField('csv_store', 'file', array(
            'label' => Mage::helper('interest')->__('Select File to Import'),
            'class' => 'required-entry',
            'required' => true,
            'name' => 'csv_store',
            'note' => '<a href="' . $importFile . '">Sample File</a>',
        ));

        return parent::_prepareForm();
    }
}
