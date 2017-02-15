<?php

class Magestore_TruBox_Model_Status extends Varien_Object
{
	const ORDER_CREATED_BY_ADMIN_YES	= 1;
	const ORDER_CREATED_BY_ADMIN_NO	= 2;

	static public function getOptionArray(){
		return array(
			self::ORDER_CREATED_BY_ADMIN_YES	=> Mage::helper('trubox')->__('Yes'),
			self::ORDER_CREATED_BY_ADMIN_NO   => Mage::helper('trubox')->__('No')
		);
	}
	
	static public function getOptionHash(){
		$options = array();
		$options[] = '';
		foreach (self::getOptionArray() as $value => $label)
			$options[] = array(
				'value'	=> $value,
				'label'	=> $label
			);
		return $options;
	}
}