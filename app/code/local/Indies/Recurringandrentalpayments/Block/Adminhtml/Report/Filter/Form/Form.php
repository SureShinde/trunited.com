<?php
/** 
* 
* Do not edit or add to this file if you wish to upgrade the module to newer 
* versions in the future. If you wish to customize the module for your 
* needs please contact us to https://www.milople.com/contact-us.html
* 
* @category     Ecommerce
* @package      Indies_Recurringandrentalpayments
* @copyright	Copyright (c) 2016 Milople Technologies Pvt. Ltd. All Rights Reserved. 
* @url			https://www.milople.com/magento-extensions/partial-payment.html
* 
**/

class Indies_Recurringandrentalpayments_Block_Adminhtml_Report_Filter_Form_Form extends Mage_Adminhtml_Block_Widget_Form
{
	protected function _prepareForm()
  	{
		$filter = $this->getRequest()->get('filter');
		if (is_string($filter)) 
		{
            $data = array();
            $filter = base64_decode($filter);
            parse_str(urldecode($filter), $data);			
		}
		$form = new Varien_Data_Form(array(
               'id' => 'recurringandrentalpayments_reportdetail',
               'action' => $this->getUrl('*/*/index'),
               'method' => 'post',
               'enctype' => 'multipart/form-data'
               ));
		$form->setUseContainer(true);
		$this->setForm($form);
		$fieldset = $form->addFieldset('recurringandrentalpayments_reportdetail', array('legend'=>Mage::helper('recurringandrentalpayments')->__('Filter')));
		if(!empty($data['date']))
		{
			$date=date("d/m/Y", strtotime($data["date"]));
			Mage::register('set_date', $date);
		}
		else
		{
			$date="";
			Mage::register('set_date',NULL);
		}
     
		$fieldset->addField('date', 'date', array(
          'label'     => Mage::helper('recurringandrentalpayments')->__('Select Date'),
          'tabindex' => 1,
		  'name' => 'date',
		  'index' => 'date',
		  'class'     => 'required-entry',
		  'required'  => true,
          'image' => $this->getSkinUrl('images/grid-cal.gif'),
		  'value' => $date,
          'format' => Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT)
        ));

		if ( Mage::getSingleton('adminhtml/session')->getRecurringReportData() )
		{
			$form->setValues(Mage::getSingleton('adminhtml/session')->getRecurringReportData());
			Mage::getSingleton('adminhtml/session')->getRecurringReportData(null);
		} 
		elseif ( Mage::registry('recurring_report_data') ) 
		{
          $form->setValues(Mage::registry('recurring_report_data')->getData());
		}
		return parent::_prepareForm();
  	}
}
