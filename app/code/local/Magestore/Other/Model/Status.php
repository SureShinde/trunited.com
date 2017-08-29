<?php

class Magestore_Other_Model_Status extends Varien_Object
{
	const STATUS_ENABLED	= 1;
	const STATUS_DISABLED	= 2;

	const STATUS_CUSTOMER_ACTIVE = 1;
	const STATUS_CUSTOMER_INACTIVE = 0;

	static public function getOptionArray(){
		return array(
			self::STATUS_ENABLED	=> Mage::helper('other')->__('Enabled'),
			self::STATUS_DISABLED   => Mage::helper('other')->__('Disabled')
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

	static public function getOptionCustomerStatusArray(){
		return array(
			self::STATUS_CUSTOMER_ACTIVE	=> Mage::helper('other')->__('Yes'),
			self::STATUS_CUSTOMER_INACTIVE   => Mage::helper('other')->__('No')
		);
	}

	static public function getOptionCustomerStatusHash(){
		$options = array();
		foreach (self::getOptionCustomerStatusArray() as $value => $label)
			$options[] = array(
				'value'	=> $value,
				'label'	=> $label
			);
		return $options;
	}
}