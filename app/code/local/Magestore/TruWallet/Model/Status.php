<?php

class Magestore_TruWallet_Model_Status extends Varien_Object
{
	const STATUS_ENABLED	= 1;
	const STATUS_DISABLED	= 2;

	const STATUS_TRANSACTION_PENDING = 1;
	const STATUS_TRANSACTION_COMPLETED = 2;
	const STATUS_TRANSACTION_CANCELLED = 3;

	static public function getOptionArray(){
		return array(
			self::STATUS_ENABLED	=> Mage::helper('truwallet')->__('Enabled'),
			self::STATUS_DISABLED   => Mage::helper('truwallet')->__('Disabled')
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

	static public function getTransactionOptionArray(){
		return array(
			self::STATUS_TRANSACTION_PENDING	=> Mage::helper('truwallet')->__('Pending'),
			self::STATUS_TRANSACTION_COMPLETED   => Mage::helper('truwallet')->__('Completed'),
			self::STATUS_TRANSACTION_CANCELLED   => Mage::helper('truwallet')->__('Cancelled')
		);
	}

	static public function getTransactionOptionHash(){
		$options = array();
		foreach (self::getTransactionOptionArray() as $value => $label)
			$options[] = array(
				'value'	=> $value,
				'label'	=> $label
			);
		return $options;
	}
}