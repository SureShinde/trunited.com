<?php

class Magestore_TruGiftCard_Model_Status extends Varien_Object
{
	const STATUS_ENABLED	= 1;
	const STATUS_DISABLED	= 2;

	const STATUS_TRANSACTION_PENDING = 1;
	const STATUS_TRANSACTION_COMPLETED = 2;
	const STATUS_TRANSACTION_CANCELLED = 3;
	const STATUS_TRANSACTION_EXPIRED = 4;
	const STATUS_TRANSACTION_ON_HOLD = 5;

	static public function getOptionArray(){
		return array(
			self::STATUS_ENABLED	=> Mage::helper('trugiftcard')->__('Enabled'),
			self::STATUS_DISABLED   => Mage::helper('trugiftcard')->__('Disabled')
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
			self::STATUS_TRANSACTION_PENDING	=> Mage::helper('trugiftcard')->__('Pending'),
			self::STATUS_TRANSACTION_COMPLETED   => Mage::helper('trugiftcard')->__('Completed'),
			self::STATUS_TRANSACTION_CANCELLED   => Mage::helper('trugiftcard')->__('Canceled'),
			self::STATUS_TRANSACTION_EXPIRED   => Mage::helper('trugiftcard')->__('Expired'),
			self::STATUS_TRANSACTION_ON_HOLD   => Mage::helper('trugiftcard')->__('On Hold'),
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