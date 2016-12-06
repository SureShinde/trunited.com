<?php

class Magestore_Custompromotions_Model_Status extends Varien_Object
{
	const STATUS_ENABLED	= 1;
	const STATUS_DISABLED	= 2;

	const TITLE_PROMOTION_REGISTER = 'Received truWallet Money from registering of promotion';
	const TITLE_PROMOTION_ORDER = 'Received truWallet Money from order of promotion';

	const TYPE_PROMOTION_REGISTER = 1;
	const TYPE_PROMOTION_ORDER = 2;

	static public function getOptionArray(){
		return array(
			self::STATUS_ENABLED	=> Mage::helper('custompromotions')->__('Enabled'),
			self::STATUS_DISABLED   => Mage::helper('custompromotions')->__('Disabled')
		);
	}

	static public function getOptionPromotionArray(){
		return array(
			self::TYPE_PROMOTION_REGISTER	=> Mage::helper('custompromotions')->__('Register'),
			self::TYPE_PROMOTION_ORDER   => Mage::helper('custompromotions')->__('Order')
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

	static public function getOptionPromotionHash(){
		$options = array();
		foreach (self::getOptionPromotionArray() as $value => $label)
			$options[] = array(
				'value'	=> $value,
				'label'	=> $label
			);
		return $options;
	}
}