<?php

class Magestore_TruWallet_Model_Type extends Varien_Object
{
	const TYPE_TRANSACTION_BY_ADMIN	= 1;
	const TYPE_TRANSACTION_SHARING	= 2;

	static public function getOptionArray(){
		return array(
			self::TYPE_TRANSACTION_BY_ADMIN	=> Mage::helper('truwallet')->__('Changed by Admin'),
			self::TYPE_TRANSACTION_SHARING   => Mage::helper('truwallet')->__('Sharing truWallet Money')
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