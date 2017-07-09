<?php

class Magestore_Manageapi_Block_Adminhtml_Manageapi_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
	protected function _prepareForm(){
		$form = new Varien_Data_Form();
		$this->setForm($form);
		
		if (Mage::getSingleton('adminhtml/session')->getManageapiData()){
			$data = Mage::getSingleton('adminhtml/session')->getManageapiData();
			Mage::getSingleton('adminhtml/session')->setManageapiData(null);
		}elseif(Mage::registry('manageapi_data'))
			$data = Mage::registry('manageapi_data')->getData();
		
		$fieldset = $form->addFieldset('manageapi_form', array('legend'=>Mage::helper('manageapi')->__('API information')));

        $dateTimeFormatIso = Mage::app()->getLocale()->getDateTimeFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT);

        $fieldset->addField('start_date', 'datetime', array(
            'label'    => Mage::helper('manageapi')->__('Start Date'),
            'title'    => Mage::helper('manageapi')->__('Start Date'),
            'time'      => true,
            'name'     => 'start_date',
            'image'    => $this->getSkinUrl('images/grid-cal.gif'),
            'format'   => $dateTimeFormatIso,
            'required' => true,
        ));

        $fieldset->addField('end_date', 'datetime', array(
            'label'    => Mage::helper('manageapi')->__('End Date'),
            'title'    => Mage::helper('manageapi')->__('End Date'),
            'time'      => true,
            'name'     => 'end_date',
            'image'    => $this->getSkinUrl('images/grid-cal.gif'),
            'format'   => $dateTimeFormatIso,
            'required' => true,
        ));

        $fieldset->addField('select_api', 'checkboxes', array(
            'label'     => Mage::helper('manageapi')->__('Select APIs'),
            'name'      => 'select_api[]',
            'values' => array(
                array('value'=>1,'label'=>Mage::helper('manageapi')->__('Link Share')),
                array('value'=>2,'label'=>Mage::helper('manageapi')->__('Price Line - Hotel')),
                array('value'=>3,'label'=>Mage::helper('manageapi')->__('Price Line - Flight')),
                array('value'=>4,'label'=>Mage::helper('manageapi')->__('Price Line - Cart')),
                array('value'=>5,'label'=>Mage::helper('manageapi')->__('Price Line - Vacation')),
                array('value'=>6,'label'=>Mage::helper('manageapi')->__('Cj')),
                array('value'=>7,'label'=>Mage::helper('manageapi')->__('Target')),
            ),
            'checked'  => array(1,2,3,4,5,6,7),
            'required' => true,
            'after_element_html' => '<small></small>',
        ));




        if($this->getRequest()->getParam('run'))
        {
            if(sizeof(Mage::getSingleton('adminhtml/session')->getData('url_called')) > 0){
                $field_set = $form->addFieldset('manage_api_form', array('legend'=>Mage::helper('manageapi')->__('API LINKS')));
                $i = 0;
                foreach (Mage::getSingleton('adminhtml/session')->getData('url_called') as $k => $v)
                {
                    $field_set->addField($i, 'note', array(
                        'text'     => $v,
                        'after_element_html' => '<br /><small>Details: '.$k.'</small>',
                    ));
                    $i++;
                }
            }
        } else {
            Mage::getSingleton('adminhtml/session')->unsetData('url_called');
            Mage::getSingleton('adminhtml/session')->unsetData('api_called');
        }

		$form->setValues($data);
		return parent::_prepareForm();
	}
}