<?php

class Magestore_TruBox_Model_Type extends Varien_Object
{
	const TYPE_ONE_TIME = 1;
	const TYPE_EVERY_MONTH = 2;
	const TYPE_EVERY_TWO_MONTHS = 3;

	static public function getOptionArray(){
		return array(
			self::TYPE_ONE_TIME	=> Mage::helper('trubox')->__('One Time Shipment'),
			self::TYPE_EVERY_MONTH   => Mage::helper('trubox')->__('Every Month'),
			self::TYPE_EVERY_TWO_MONTHS   => Mage::helper('trubox')->__('Every Two Months'),
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