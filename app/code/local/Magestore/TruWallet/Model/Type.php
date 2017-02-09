<?php

class Magestore_TruWallet_Model_Type extends Varien_Object
{
	const TYPE_TRANSACTION_BY_ADMIN	= 1;
	const TYPE_TRANSACTION_SHARING	= 2;
	const TYPE_TRANSACTION_RECEIVE_FROM_SHARING	= 3;
	const TYPE_TRANSACTION_TRANSFER	= 4;
	const TYPE_TRANSACTION_PAYOUT_EARNING	= 5;
	const TYPE_TRANSACTION_CANCEL_ORDER	= 6;
	const TYPE_TRANSACTION_CHECKOUT_ORDER = 7;
	const TYPE_TRANSACTION_REFUND_ORDER = 8;
	const TYPE_TRANSACTION_PURCHASE_GIFT_CARD = 9;

	static public function getOptionArray(){
		return array(
			self::TYPE_TRANSACTION_BY_ADMIN	=> Mage::helper('truwallet')->__('Changed by Admin'),
			self::TYPE_TRANSACTION_SHARING   => Mage::helper('truwallet')->__('Sharing truWallet money'),
			self::TYPE_TRANSACTION_RECEIVE_FROM_SHARING   => Mage::helper('truwallet')->__('Received truWallet money from Sharing'),
			self::TYPE_TRANSACTION_TRANSFER   => Mage::helper('truwallet')->__('Transfer dollars from balance to truWallet'),
			self::TYPE_TRANSACTION_PAYOUT_EARNING   => Mage::helper('truwallet')->__('Payout earnings'),
			self::TYPE_TRANSACTION_CANCEL_ORDER   => Mage::helper('truwallet')->__('Retrieve truWallet balance spent on cancelled order'),
			self::TYPE_TRANSACTION_CHECKOUT_ORDER   => Mage::helper('truwallet')->__('Spend truWallet balance to purchase order'),
			self::TYPE_TRANSACTION_REFUND_ORDER   => Mage::helper('truwallet')->__('Retrieve truWallet balance spent on refunded order'),
			self::TYPE_TRANSACTION_PURCHASE_GIFT_CARD   => Mage::helper('truwallet')->__('Purchased truWallet Gift Card on order'),
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

	static public function getDataType()
	{
		return array(
			self::TYPE_TRANSACTION_BY_ADMIN,
			self::TYPE_TRANSACTION_SHARING,
			self::TYPE_TRANSACTION_RECEIVE_FROM_SHARING,
			self::TYPE_TRANSACTION_TRANSFER,
			self::TYPE_TRANSACTION_PAYOUT_EARNING,
		);
	}

}