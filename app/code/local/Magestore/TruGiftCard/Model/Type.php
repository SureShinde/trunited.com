<?php

class Magestore_TruGiftCard_Model_Type extends Varien_Object
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
	const TYPE_TRANSACTION_RECEIVE_REWARD_FROM_PROMOTION = 10;
	const TYPE_TRANSACTION_RECEIVE_REWARD_FROM_REFERRED_PROMOTION = 11;
//	const TYPE_TRANSACTION_RECEIVE_FROM_ECHECK_PAYMENT = 10;

	static public function getOptionArray(){
		return array(
			self::TYPE_TRANSACTION_BY_ADMIN	=> Mage::helper('trugiftcard')->__('Changed by Admin'),
			self::TYPE_TRANSACTION_SHARING   => Mage::helper('trugiftcard')->__('Sharing truGiftCard money'),
			self::TYPE_TRANSACTION_RECEIVE_FROM_SHARING   => Mage::helper('trugiftcard')->__('Received truGiftCard money from Sharing'),
			self::TYPE_TRANSACTION_TRANSFER   => Mage::helper('trugiftcard')->__('Transfer dollars from balance to truGiftCard'),
			self::TYPE_TRANSACTION_PAYOUT_EARNING   => Mage::helper('trugiftcard')->__('Payout earnings'),
			self::TYPE_TRANSACTION_CANCEL_ORDER   => Mage::helper('trugiftcard')->__('Retrieve truGiftCard balance spent on cancelled order'),
			self::TYPE_TRANSACTION_CHECKOUT_ORDER   => Mage::helper('trugiftcard')->__('Spend truGiftCard balance to purchase order'),
			self::TYPE_TRANSACTION_REFUND_ORDER   => Mage::helper('trugiftcard')->__('Retrieve truGiftCard balance spent on refunded order'),
			self::TYPE_TRANSACTION_PURCHASE_GIFT_CARD   => Mage::helper('trugiftcard')->__('Purchased truGiftCard Gift Card on order'),
			self::TYPE_TRANSACTION_RECEIVE_REWARD_FROM_PROMOTION   => Mage::helper('trugiftcard')->__('Received truGiftCard rewards from promotion'),
			self::TYPE_TRANSACTION_RECEIVE_REWARD_FROM_REFERRED_PROMOTION   => Mage::helper('trugiftcard')->__('Received Tru gift card rewards from referring the customers on the promotion'),
//			self::TYPE_TRANSACTION_RECEIVE_FROM_ECHECK_PAYMENT   => Mage::helper('trugiftcard')->__('Received truGiftCard money from Sharing'),
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