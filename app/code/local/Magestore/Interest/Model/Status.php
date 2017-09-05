<?php

class Magestore_Interest_Model_Status extends Varien_Object
{
	const STATUS_ENABLED	= 1;
	const STATUS_DISABLED	= 2;

	static public function getOptionArray(){
		return array(
			self::STATUS_ENABLED	=> Mage::helper('interest')->__('Enabled'),
			self::STATUS_DISABLED   => Mage::helper('interest')->__('Disabled')
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