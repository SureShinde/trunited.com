<?php

class Magestore_TruBox_Model_Status extends Varien_Object
{
	const ORDER_CREATED_BY_ADMIN_YES	= 1;
	const ORDER_CREATED_BY_ADMIN_NO	= 2;

	const COUPON_CODE_STATUS_PENDING = 1;
	const COUPON_CODE_STATUS_USED = 2;
	const COUPON_CODE_STATUS_EXPIRED = 3;

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

	static public function getOptionCodeArray(){
		return array(
			self::COUPON_CODE_STATUS_PENDING	=> Mage::helper('trubox')->__('Pending'),
			self::COUPON_CODE_STATUS_USED	=> Mage::helper('trubox')->__('Used'),
			self::COUPON_CODE_STATUS_EXPIRED   => Mage::helper('trubox')->__('Expired')
		);
	}

	static public function getOptionCodeHash(){
		$options = array();
		$options[] = '';
		foreach (self::getOptionCodeArray() as $value => $label)
			$options[] = array(
				'value'	=> $value,
				'label'	=> $label
			);
		return $options;
	}
}