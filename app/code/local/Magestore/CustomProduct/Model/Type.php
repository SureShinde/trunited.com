<?php

class Magestore_CustomProduct_Model_Type extends Varien_Object
{
    const PRODUCT_TYPE_SHOP_NOW = 'customproduct';
	const STATUS_ENABLED	= 1;
	const STATUS_DISABLED	= 2;

	static public function getOptionArray(){
		return array(
			self::STATUS_ENABLED	=> Mage::helper('customproduct')->__('Enabled'),
			self::STATUS_DISABLED   => Mage::helper('customproduct')->__('Disabled')
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