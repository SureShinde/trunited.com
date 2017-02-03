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
}