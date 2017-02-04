<?php

class Magestore_TruWallet_Model_Type extends Varien_Object
{
	const TYPE_TRANSACTION_BY_ADMIN	= 1;
	const TYPE_TRANSACTION_SHARING	= 2;
	const TYPE_TRANSACTION_RECEIVE_FROM_SHARING	= 3;
	const TYPE_TRANSACTION_TRANSFER	= 4;

	static public function getOptionArray(){
		return array(
			self::TYPE_TRANSACTION_BY_ADMIN	=> Mage::helper('truwallet')->__('Changed by Admin'),
			self::TYPE_TRANSACTION_SHARING   => Mage::helper('truwallet')->__('Sharing truWallet Money'),
			self::TYPE_TRANSACTION_RECEIVE_FROM_SHARING   => Mage::helper('truwallet')->__('Received truWallet Money from Sharing'),
			self::TYPE_TRANSACTION_TRANSFER   => Mage::helper('truwallet')->__('Transfer dollars from balance to truWallet'),
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