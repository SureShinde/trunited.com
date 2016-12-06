<?php

class Magestore_Affiliatepluspayperlead_Model_Status extends Varien_Object
{
	const STATUS_PENDING	= 2;
	const STATUS_COMPLETE	= 1;
	const STATUS_CANCEL	= 3;

	static public function getOptionArray(){
		return array(
			self::STATUS_PENDING	=> Mage::helper('affiliatepluspayperlead')->__('Pending'),
			self::STATUS_COMPLETE   => Mage::helper('affiliatepluspayperlead')->__('Complete'),
			self::STATUS_CANCEL   => Mage::helper('affiliatepluspayperlead')->__('Canceled')
		);
	}
	
	static public function getOptionHash(){
		$options = array();
		foreach (self::getOptionArray() as $value => $label)
			$options[] = array(
				'value'	=> $value,
				'label'	=> $label
			);
		return $options;
	}
}