<?php

class Magestore_TruWallet_Model_Customer extends Mage_Core_Model_Abstract
{
	public function _construct(){
		parent::_construct();
		$this->_init('truwallet/customer');
	}

	public function updateCredit($data)
	{
		if($data['credit'] != '')
		{
			$current_credit = $this->getTruwalletCredit();
			$this->setTruWalletCredit($current_credit + $data['credit']);
			$this->save();
		}
	}

	/**
	 * @param $credit_amount
	 * @return mixed
	 */
	public function getConvertedFromBaseTruwalletCredit($credit_amount)
	{
		$store = Mage::app()->getStore();
		return $store->convertPrice($credit_amount);
		//return  Mage::helper('core')->currency($credit_amount, true, false);
	}

	/**
	 * @param $credit_amount
	 * @return string
	 */
	public function getConvertedToBaseTruwalletCredit($credit_amount)
	{
		$rate = Mage::app()->getStore()->convertPrice(1);
		return number_format($credit_amount / $rate,2);
	}
}
