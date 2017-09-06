<?php

class Magestore_TruGiftCard_Model_TruGiftCard extends Mage_Core_Model_Abstract
{
	public function _construct(){
		parent::_construct();
		$this->_init('trugiftcard/trugiftcard');
	}
}